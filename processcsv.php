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

use enrol_oneroster\OneRosterHelper;
require_once('../../config.php');
require_once('oneroster_csv_form.php');
require_once('oneroster_helper.php');

/**
 * One Roster Client
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$PAGE->set_url('/enrol/oneroster/processcsv.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Process OneRoster CSV');
$PAGE->set_heading('Process OneRoster CSV');

$mform = new oneroster_csv_form();

const TEMPDIR = 'oneroster_csv';

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/admin/settings.php', ['section' => 'enrolsettingsoneroster']));

} else if ($data = $mform->get_data()) {
    $tempdir = make_temp_directory(TEMPDIR); 
    $filecontent = $mform->get_file_content('uploadedzip');
    $zipfilepath = $tempdir . '/uploadedzip.zip';

    if (file_put_contents($zipfilepath, $filecontent)) {
        $zip = new ZipArchive;
        $res = $zip->open($zipfilepath);

        if ($res === true) {
            $zip->extractTo($tempdir); 
            $zip->close();

            $manifest_path = $tempdir . '/manifest.csv';

            if (file_exists($manifest_path)) {
                $missing_files = OneRosterHelper::check_manifest_and_files($manifest_path, $tempdir);

                echo $OUTPUT->header();

                if (empty($missing_files['missing_files']) && empty($missing_files['invalid_headers'])) {
                    $csv_data = OneRosterHelper::extract_csvs_to_arrays($tempdir);

                    // Process the CSV files

                    echo 'CSV processing completed.<br>';
                } else {
                    OneRosterHelper::display_missing_and_invalid_files($missing_files);
                }
            } else {
                echo $OUTPUT->header();
                echo 'The manifest.csv file is missing.<br>';
            }

            remove_dir($tempdir);
        } else {
            echo $OUTPUT->header();
            echo 'Failed to open the ZIP file.<br>';
        }
    } else {
        echo $OUTPUT->header();
        echo 'Failed to move the uploaded ZIP file.<br>';
    }

    $backbuttonurl = new moodle_url('/enrol/oneroster/processcsv.php');
    echo $OUTPUT->single_button($backbuttonurl, get_string('back'));
    echo $OUTPUT->footer();

} else {
    echo $OUTPUT->header();
    $mform->display();
    echo $OUTPUT->footer();
}

