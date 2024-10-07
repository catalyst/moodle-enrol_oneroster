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

use enrol_oneroster\client_helper;
use enrol_oneroster\form\oneroster_org_selection_form;
require_once('../../config.php');
require_once('classes/client_helper.php');
require_once(__DIR__ . '/form/oneroster_csv_form.php');
require_once(__DIR__ . '/form/oneroster_org_selection_form.php');
require_once(__DIR__ . '/classes/local/csv_client.php');
require_once(__DIR__ . '/classes/local/csv_client_helper.php');

/**
 * One Roster Client
 *
 * @package    enrol_oneroster
 * @copyright  Gustavo Amorim De Almeida, Ruben Cooper, Josh Bateson, Brayden Porter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 */

$PAGE->set_url(get_string('set_url', 'enrol_oneroster'));
$PAGE->set_context(\context_system::instance());
$PAGE->set_title(get_string('title', 'enrol_oneroster'));
$PAGE->set_heading(get_string('title', 'enrol_oneroster'));
global $DB;
$mform = new oneroster_csv_form();

const TEMPDIR = 'oneroster_csv';

$step = optional_param('step', 1, PARAM_INT);

if ($step == 1) {
    // Step 1: Upload CSV ZIP file.
    $mform = new oneroster_csv_form();

    if ($mform->is_cancelled()) {
        redirect(new \moodle_url('/admin/settings.php', ['section' => 'enrolsettingsoneroster']));
    } 
    else if ($data = $mform->get_data()) {
        $uniqueid = $USER->id . '_' . time();
        $tempdir = make_temp_directory('oneroster_csv/' . $uniqueid);
        $filecontent = $mform->get_file_content('uploadedzip');
        $zipfilepath = $tempdir . '/uploadedzip.zip';
        if (file_put_contents($zipfilepath, $filecontent)) {
            $zip = new \ZipArchive();
            $res = $zip->open($zipfilepath);
            if ($res === true) {
                $zip->extractTo($tempdir); 
                $zip->close();
                $manifest_path = $tempdir . '/manifest.csv';
                if (file_exists($manifest_path)) {
                    $csv_data = OneRosterHelper::extract_csvs_to_arrays($tempdir);
                    $orgs = $csv_data['orgs'] ?? [];
                    // Prepare organization options
                    $orgoptions = [];
                    foreach ($orgs as $org) {
                        $orgoptions[$org['sourcedId']] = $org['name'];
                    }
                    if (count($orgoptions) == 1) {
                        // Only one organization, skip the selection form
                        $selected_org_sourcedId = array_key_first($orgoptions);
                        // Proceed to process the selected organization
                        process_selected_organization($selected_org_sourcedId, $tempdir, $csv_data);
                        exit;
                    } else {
                        // Display the organization selection form
                        $orgform = new oneroster_org_selection_form(null, ['orgoptions' => $orgoptions, 'tempdir' => $tempdir]);
                        echo $OUTPUT->header();
                        $orgform->display();
                        echo $OUTPUT->footer();
                        exit;
                    } 
                } 
                else {
                    echo $OUTPUT->header();
                    echo get_string('missing_csv_files', 'enrol_oneroster');
                    echo $OUTPUT->footer();
                    exit;
                }
            } 
            else {
                echo $OUTPUT->header();
                echo get_string('failed_to_open_zip_file', 'enrol_oneroster');
                echo $OUTPUT->footer();
                exit;
            }
        } 
        else {
            echo $OUTPUT->header();
            echo get_string('failed_upload_zip_file', 'enrol_oneroster');
            echo $OUTPUT->footer();
            exit;
        }
    } 
    else {
        echo $OUTPUT->header();
        $mform->display();
        echo $OUTPUT->footer();
    }

} 
else if ($step == 2) {
    // Step 2: Select Organization.
    $orgform = new oneroster_org_selection_form();

    if ($orgform->is_cancelled()) {
        redirect(new \moodle_url('/admin/settings.php', ['section' => 'enrolsettingsoneroster']));
    } 
    else if ($orgdata = $orgform->get_data()) {
        $selected_org_sourcedId = $orgdata->organization;
        $tempdir = $orgdata->tempdir;

        // Proceed to process the selected organization
        process_selected_organization($selected_org_sourcedId, $tempdir);
        exit;

    } 
    else {
        echo $OUTPUT->header();
        echo get_string('invalid_form', 'enrol_oneroster');
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
function process_selected_organization($selected_org_sourcedId, $tempdir, $csv_data = null) {
    global $OUTPUT;

    if (is_null($csv_data)) {
        $zipfilepath = $tempdir . '/uploadedzip.zip';

        if (file_exists($zipfilepath)) {
            $zip = new \ZipArchive();
            $res = $zip->open($zipfilepath);

            if ($res === true) {
                $zip->extractTo($tempdir); 
                $zip->close();

                $manifest_path = $tempdir . '/manifest.csv';

                if (file_exists($manifest_path)) {
                    $missing_files = OneRosterHelper::check_manifest_and_files($manifest_path, $tempdir);

                    if (empty($missing_files['missing_files']) && empty($missing_files['invalid_headers'])) {
                        $csv_data = OneRosterHelper::extract_csvs_to_arrays($tempdir);

                    } 
                    else {
                        echo $OUTPUT->header();
                        OneRosterHelper::display_missing_and_invalid_files($missing_files);
                        echo $OUTPUT->footer();
                        return;
                    }
                } 
                else {
                    echo $OUTPUT->header();
                    echo get_string('missing_csv_files', 'enrol_oneroster');
                    echo $OUTPUT->footer();
                    exit;
                }
            } 
            else {
                echo $OUTPUT->header();
                echo get_string('failed_to_open_zip_file', 'enrol_oneroster');
                echo $OUTPUT->footer();
                exit;
            }
        } 
        else {
            echo $OUTPUT->header();
            echo get_string('failed_upload_zip_file', 'enrol_oneroster');
            echo $OUTPUT->footer();
            exit;
        }
    }

    $manifest = $csv_data['manifest'] ?? [];
    $users = $csv_data['users'] ?? [];
    $classes = $csv_data['classes'] ?? [];
    $orgs = $csv_data['orgs'] ?? [];
    $enrollments = $csv_data['enrollments'] ?? [];
    $academicSessions = $csv_data['academicSessions'] ?? [];

    $csvclient = client_helper::get_csv_client();

    $csvclient->set_orgid($selected_org_sourcedId);

    $csvclient->set_data(
        $manifest,
        $users,
        $classes,
        $orgs,
        $enrollments,
        $academicSessions
    );

    $csvclient->synchronise();

    echo $OUTPUT->header();
    echo get_string('successful_upload', 'enrol_oneroster');;
    echo $OUTPUT->footer();

    // Clean up temp directory
    remove_dir($tempdir);
}
