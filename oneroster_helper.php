<?php 
namespace enrol_oneroster;
// Include expected csv headers
require_once('expected_csv_headers.php');
use enrol_oneroster\expected_csv_headers as expected_csv_headers;

class OneRosterHelper {

    // Function to validate CSV headers
    public static function validate_csv_headers($file_path) {

        // Clean file path to avoid directory traversal
        $clean_file_path = clean_param($file_path, PARAM_PATH);

        // Extract the base file name without the directory path
        $file_name = basename($clean_file_path);

        // Get the required headers for the file name
        $expected_headers = expected_csv_headers::getHeader($file_name);

        // Read and compare csv headers
        if (($handle = fopen($clean_file_path, "r")) !== false) {

            $headers = fgetcsv($handle, 1000, ",");
            fclose($handle);

            // Compare the headers
            return $headers === $expected_headers;
            
        } else {
            throw new Exception("Unable to open file: $clean_file_path");
        }
    }

    // Function to check the manifest.csv file and validate required files
    public static function check_manifest_and_files($manifest_path, $tempdir) {
        $invalid_headers = [];
        $required_files = [];

        if (($handle = fopen($manifest_path, "r")) !== false) {
            while (($data = fgetcsv($handle, 1000, ",")) !== false) {
                if (in_array($data[1], ['bulk', 'delta'])) {
                    $required_files[] = str_replace('file.', '', $data[0]) . '.csv';
                }
            }
            fclose($handle);
        }

        // Check if all required files are present
        $extracted_files = array_diff(scandir($tempdir), array('.', '..', 'uploadedzip.zip'));
        $missing_files = array_diff($required_files, $extracted_files);

        // Validate headers for each required file
        foreach ($required_files as $file) {
            if (in_array($file, $extracted_files)) {
                $file_path = $tempdir . '/' . $file;
                if (!self::validate_csv_headers($file_path)) {
                    $invalid_headers[] = $file;
                }
            }
        }

        return [
            'missing_files' => $missing_files,
            'invalid_headers' => $invalid_headers
        ];
    }

}