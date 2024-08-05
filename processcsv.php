<?php
require_once('../../config.php');

// Define the URL where this script is located
$PAGE->set_url('/enrol/oneroster/processcsv.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_title('Process OneRoster CSV');
$PAGE->set_heading('Process OneRoster CSV');

// Include form definition
require_once('oneroster_csv_form.php');

// Setup a new form instance
$mform = new oneroster_csv_form();

// Function to recursively delete a directory
function delete_directory($dir) {
    if (!file_exists($dir)) {
        return true;
    }
    if (!is_dir($dir)) {
        return unlink($dir);
    }
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }
        if (!delete_directory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }
    }
    return rmdir($dir);
}

// Form processing and displaying
if ($mform->is_cancelled()) {
    // Handle form cancellation
    redirect(new moodle_url('/admin/settings.php', ['section' => 'enrolsettingsoneroster']));
} else if ($data = $mform->get_data()) {
    // Process the uploaded ZIP file
    $tempdir = make_temp_directory('oneroster_csv'); 

    $filecontent = $mform->get_file_content('uploadedzip');
    $zipfilepath = $tempdir . '/uploadedzip.zip';

    if (file_put_contents($zipfilepath, $filecontent)) {
        $zip = new ZipArchive;
        $res = $zip->open($zipfilepath);

        if ($res === TRUE) {
            $zip->extractTo($tempdir); 
            $zip->close();

               // Check if the manifest.csv file is present
               $manifest_path = $tempdir . '/manifest.csv';

               if (file_exists($manifest_path)) {
                   // Process the manifest.csv file
                   // CSV processing logic goes here
                   echo $OUTPUT->header();
                   echo 'CSV processing completed.<br>';
               } else {
                   echo $OUTPUT->header();
                   echo 'The manifest.csv file is missing.<br>';
               }
   
               // Cleanup: remove the entire temporary directory
               delete_directory($tempdir);
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