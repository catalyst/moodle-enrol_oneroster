<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace enrol_oneroster;

use enrol_oneroster\classes\form\oneroster_org_selection_form;
use enrol_oneroster\classes\form\oneroster_csv_form;
use enrol_oneroster\classes\local\csv_client_helper;

require_once('../../config.php');
require_once(__DIR__ . '/classes/form/oneroster_csv_form.php');
require_once(__DIR__ . '/classes/form/oneroster_org_selection_form.php');
require_once(__DIR__ . '/classes/local/csv_client_helper.php');
require_once(__DIR__ . '/classes/local/csv_client_const_helper.php');
require_once($CFG->libdir . '/adminlib.php');

/**
 * One Roster Client.
 *
 * @package    enrol_oneroster
 * @copyright  Gustavo Amorim De Almeida, Ruben Cooper, Josh Bateson, Brayden Porter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 */
admin_externalpage_setup('enrol_oneroster_csv_upload');

$PAGE->set_url('/enrol/oneroster/process_csv.php');
$PAGE->set_title('Process OneRoster CSV');
$PAGE->set_heading('Process OneRoster CSV');

$mform = new oneroster_csv_form();

const TEMPDIR = 'oneroster_csv';

$step = optional_param('step', 1, PARAM_INT);
global $SESSION;

if ($step == 1) {
    $mform = new oneroster_csv_form();

    if ($mform->is_cancelled()) {
        redirect(new \moodle_url('/admin/settings.php', ['section' => 'enrolsettingsoneroster']));
    } else if ($data = $mform->get_data()) {
        $uniqueid = $USER->id . '_' . time();
        $tempdir = make_temp_directory('oneroster_csv/' . $uniqueid);
        $filecontent = $mform->get_file_content('uploadedzip');
        $zipfilepath = $tempdir . '/uploadedzip.zip';

        if (!file_put_contents($zipfilepath, $filecontent)) {
            redirect($PAGE->url, get_string('failed_upload_zip_file', 'enrol_oneroster'));
        }

        $zip = new \ZipArchive();
        $res = $zip->open($zipfilepath);
        if ($res !== true) {
            redirect($PAGE->url, get_string('failed_to_open_zip_file', 'enrol_oneroster'));
        }

        $zip->extractTo($tempdir);
        $zip->close();

        $manifest_path = $tempdir . '/manifest.csv';
        if (!file_exists($manifest_path)) {
            redirect($PAGE->url, get_string('missing_csv_files', 'enrol_oneroster'));
        }

        $missing_files = csv_client_helper::check_manifest_and_files($manifest_path, $tempdir);
        if (!empty($missing_files['missing_files']) || !empty($missing_files['invalid_headers'])) {
            $error_message = csv_client_helper::get_missing_and_invalid_files_message($missing_files);
            redirect($PAGE->url, $error_message);
        }

        $datatype = csv_client_helper::validate_csv_data_types($tempdir);
        if (!$datatype['is_valid']) {
            $error_message = csv_client_helper::get_validation_errors_message($datatype);
            redirect($PAGE->url, $error_message);
        }

        $csv_data = csv_client_helper::extract_csvs_to_arrays($tempdir);
        $orgs = $csv_data['orgs'] ?? [];

        // Prepare organization options.
        $orgoptions = [];
        foreach ($orgs as $org) {
            $orgoptions[$org['sourcedId']] = $org['name'];
        }

        if (count($orgoptions) == 1) {
            // Only one organization, proceed directly.
            $selected_org_sourcedId = array_key_first($orgoptions);
            process_selected_organization($selected_org_sourcedId, $tempdir, $csv_data);
            exit;
        } else {
            // Display the organization selection form.
            $SESSION->oneroster_csv['orgoptions'] = $orgoptions;
            $orgform = new oneroster_org_selection_form(null, ['orgoptions' => $orgoptions, 'tempdir' => $tempdir]);
            echo $OUTPUT->header();
            $orgform->display();
            echo $OUTPUT->footer();
            exit;
        }
    } else {
        echo $OUTPUT->header();
        if ($message = optional_param('message', '', PARAM_TEXT)) {
            echo $OUTPUT->notification($message, 'error');
        }
        $mform->display();
        echo $OUTPUT->footer();
    }
}
else if ($step == 2) {
    // Step 2: Select Organization.
    $tempdir = required_param('tempdir', PARAM_PATH); 
    $orgoptions = $SESSION->oneroster_csv['orgoptions']; 
    $orgform = new oneroster_org_selection_form(null, ['orgoptions' => $orgoptions, 'tempdir' => $tempdir]);

    if ($orgform->is_cancelled()) {
        redirect(new \moodle_url('/admin/settings.php', ['section' => 'enrolsettingsoneroster']));
    } 
    else if ($orgdata = $orgform->get_data()) {
        $selected_org_sourcedId = $orgdata->organization;
        $tempdir = $orgdata->tempdir;
    
        // Proceed to process the selected organization.
        process_selected_organization($selected_org_sourcedId, $tempdir);
    } 
    else {
        echo $OUTPUT->header();
        echo get_string('invalid_form', 'enrol_oneroster') . ' <br>';
        echo $OUTPUT->footer();
    }
}

/**
 * Processes the selected organization.
 *
 * @param string $selected_org_sourcedId The sourcedId of the selected organization.
 * @param string $tempdir The temporary directory where files are extracted.
 * @param array|null $csv_data Optional CSV data if already extracted.
 */
function process_selected_organization(string $selected_org_sourcedId, string $tempdir, array$csv_data = null) {
    global $OUTPUT;

    $zipfilepath = $tempdir . '/uploadedzip.zip';
    $zip = new \ZipArchive();
    $res = $zip->open($zipfilepath);
    if ($res !== true) {
        echo $OUTPUT->header();
        echo get_string('failed_to_open_zip_file', 'enrol_oneroster') . ' <br>';
        echo $OUTPUT->footer();
        exit;
    }

    $zip->extractTo($tempdir);
    $zip->close();
    $csv_data = csv_client_helper::extract_csvs_to_arrays($tempdir);

    $manifest = $csv_data['manifest'] ?? [];
    $users = $csv_data['users'] ?? [];
    $classes = $csv_data['classes'] ?? [];
    $orgs = $csv_data['orgs'] ?? [];
    $enrollments = $csv_data['enrollments'] ?? [];
    $academicSessions = $csv_data['academicSessions'] ?? [];

    $csvclient = client_helper::get_csv_client();

    $csvclient->set_org_id($selected_org_sourcedId);

    if (csv_client_helper::validate_user_data($csv_data) === true) {
        set_config('datasync_schools', $selected_org_sourcedId, 'enrol_oneroster');
    }

    $csvclient->set_data($manifest, $users, $classes, $orgs, $enrollments, $academicSessions);

    try {
        $csvclient->synchronise();
    } catch (\Exception $e) {
        echo $OUTPUT->header();
        echo $e->getMessage() . ' <br>';
        echo $OUTPUT->footer();
        exit;
    }

    echo $OUTPUT->header();
    echo get_string('successful_upload', 'enrol_oneroster') . ' <br>';
    echo $OUTPUT->footer();

    unset($SESSION->oneroster_csv);
}
