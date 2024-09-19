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
require_once('expected_csv_headers.php');
use enrol_oneroster\expected_csv_headers as expected_csv_headers;

/**
 * Class OneRosterHelper
 *
 * Helper class for OneRoster plugin
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class OneRosterHelper {

    /**
     * Function to validate CSV headers
     *
     * @param string $file_path Path to the CSV file
     * @return bool True if the headers are valid, false otherwise
     * @throws Exception If the file cannot be opened
     */
    public static function validate_csv_headers($file_path) {
        $clean_file_path = clean_param($file_path, PARAM_PATH);
        $file_name = basename($clean_file_path);
        $expected_headers = expected_csv_headers::getHeader($file_name);

        if (($handle = fopen($clean_file_path, "r")) !== false) {
            $headers = fgetcsv($handle, 1000, ",");
            fclose($handle);
            return $headers === $expected_headers;
        } else {
            throw new Exception("Unable to open file: $clean_file_path");
        }
    }

    /**
     * Function to check if the manifest and required files are present
     *
     * @param string $manifest_path Path to the manifest file
     * @param string $tempdir Path to the temporary directory
     * @return array An array containing the missing files and invalid headers
     */
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

        $extracted_files = array_diff(scandir($tempdir), array('.', '..', 'uploadedzip.zip'));
        $missing_files = array_diff($required_files, $extracted_files);

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


    /**
     * Function to extract CSV files to arrays
     *
     * @param string $directory Path to the directory containing the CSV files
     * @return array An associative array containing the CSV data
     */
    public static function extract_csvs_to_arrays($directory) {
        $csv_data = [];
        $files = scandir($directory);
    
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'csv') {
                $file_name = pathinfo($file, PATHINFO_FILENAME);
                $csv_data[$file_name] = [];

                if (($handle = fopen($directory . '/' . $file, 'r')) !== false) {
                    $headers = fgetcsv($handle, 1000, ',');
                    while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                        $csv_data[$file_name][] = array_combine($headers, $row);
                    }
                    fclose($handle);
                }
            }
        }
        return $csv_data;
    }

    /**
     * Function to display missing and invalid files
     *
     * @param array $missing_files An array containing the missing files and invalid headers
     */
    public static function display_missing_and_invalid_files($missing_files) {
        if (!empty($missing_files['missing_files'])) {
            echo 'The following required files are missing: ' . implode(', ', $missing_files['missing_files']) . '<br>';
        }
        if (!empty($missing_files['invalid_headers'])) {
            echo 'The following files have invalid or missing headers: ' . implode(', ', $missing_files['invalid_headers']) . '<br>';
        }
    }
}