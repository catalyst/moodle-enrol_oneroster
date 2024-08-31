<?php
require_once('../../config.php');

// Define the URL where this script is located
$PAGE->set_url('/enrol/oneroster/processcsv.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Process OneRoster CSV');
$PAGE->set_heading('Process OneRoster CSV');

// Include form definition
require_once('oneroster_csv_form.php');

// Include helper functions
require_once('oneroster_helper.php');
use enrol_oneroster\OneRosterHelper;

// Setup a new form instance
$mform = new oneroster_csv_form();

const TEMPDIR = 'oneroster_csv';

// Form processing and displaying
if ($mform->is_cancelled()) {
    // Handle form cancellation
    redirect(new moodle_url('/admin/settings.php', ['section' => 'enrolsettingsoneroster']));
} else if ($data = $mform->get_data()) {
    // Process the uploaded ZIP file
    $tempdir = make_temp_directory(TEMPDIR); 

    $filecontent = $mform->get_file_content('uploadedzip');
    $zipfilepath = $tempdir . '/uploadedzip.zip';

    if (file_put_contents($zipfilepath, $filecontent)) {
        $zip = new ZipArchive;
        $res = $zip->open($zipfilepath);

        if ($res === true) {
            $zip->extractTo($tempdir); 
            $zip->close();

            // Check if the manifest.csv file is present
            $manifest_path = $tempdir . '/manifest.csv';

            if (file_exists($manifest_path)) {
                // Check the manifest.csv and validate required files
                $missing_files = OneRosterHelper::check_manifest_and_files($manifest_path, $tempdir);

                if (empty($missing_files['missing_files']) && empty($missing_files['invalid_headers'])) {
                    // Process the manifest.csv file and other required files
                    // CSV processing logic goes here
                    echo $OUTPUT->header();
                    echo 'CSV processing completed.<br>';
                } else {
                    echo $OUTPUT->header();
                    if (!empty($missing_files['missing_files'])) {
                        echo 'The following required files are missing: ' . implode(', ', $missing_files['missing_files']) . '<br>';
                    }
                    if (!empty($missing_files['invalid_headers'])) {
                        echo 'The following files have invalid or missing headers: ' . implode(', ', $missing_files['invalid_headers']) . '<br>';
                    }
                }
            } else {
                echo $OUTPUT->header();
                echo 'The manifest.csv file is missing.<br>';
            }

            // Cleanup: remove the entire temporary directory
            remove_dir($tempdir); // Using Moodle's built-in remove_dir function
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
    // Display the form
    echo $OUTPUT->header();
    $mform->display();
    echo $OUTPUT->footer();
}