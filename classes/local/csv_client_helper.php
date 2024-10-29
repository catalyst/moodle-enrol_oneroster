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
namespace enrol_oneroster\local;

/**
 * Class csv_client_helper
 *
 * Helper class for OneRoster plugin
 *
 * @package    enrol_oneroster
 * @copyright  Gustavo Amorim De Almeida, Ruben Cooper, Josh Bateson, Brayden Porter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class csv_client_helper {
    /**
     * Get the expected data types for each file.
     * 
     * @return array An array containing the expected data types for each file.
     */
    public static function get_file_datatypes(): array {
        return [
            csv_client_const_helper::FILE_ACADEMIC_SESSIONS => [
                csv_client_const_helper::HEADER_SOURCEDID => csv_client_const_helper::DATATYPE_GUID,
                csv_client_const_helper::HEADER_STATUS => [csv_client_const_helper::DATATYPE_ENUM_STATUS, csv_client_const_helper::DATATYPE_NULL],
                csv_client_const_helper::HEADER_DATE_LAST_MODIFIED => [csv_client_const_helper::DATATYPE_DATETIME, csv_client_const_helper::DATATYPE_NULL],
                csv_client_const_helper::HEADER_TITLE => csv_client_const_helper::DATATYPE_STRING,
                csv_client_const_helper::HEADER_TYPE => csv_client_const_helper::DATATYPE_ENUM_TYPE,
                csv_client_const_helper::HEADER_START_DATE => csv_client_const_helper::DATATYPE_DATE,
                csv_client_const_helper::HEADER_END_DATE => csv_client_const_helper::DATATYPE_DATE,
                csv_client_const_helper::HEADER_PARENT_SOURCEDID => [csv_client_const_helper::DATATYPE_GUID, csv_client_const_helper::DATATYPE_NULL],
                csv_client_const_helper::HEADER_SCHOOL_YEAR => csv_client_const_helper::DATATYPE_YEAR,
            ],
            csv_client_const_helper::FILE_CLASSES => [
                csv_client_const_helper::HEADER_SOURCEDID => csv_client_const_helper::DATATYPE_GUID,
                csv_client_const_helper::HEADER_STATUS => [csv_client_const_helper::DATATYPE_ENUM_STATUS, csv_client_const_helper::DATATYPE_NULL],
                csv_client_const_helper::HEADER_DATE_LAST_MODIFIED => [csv_client_const_helper::DATATYPE_DATETIME, csv_client_const_helper::DATATYPE_NULL],
                csv_client_const_helper::HEADER_TITLE => csv_client_const_helper::DATATYPE_STRING,
                csv_client_const_helper::HEADER_GRADES => [csv_client_const_helper::DATATYPE_ARRAY_GRADE, csv_client_const_helper::DATATYPE_NULL],
                csv_client_const_helper::HEADER_COURSE_SOURCEDID => csv_client_const_helper::DATATYPE_GUID,
                csv_client_const_helper::HEADER_CLASS_CODE => [csv_client_const_helper::DATATYPE_STRING, csv_client_const_helper::DATATYPE_NULL],
                csv_client_const_helper::HEADER_CLASS_TYPE => csv_client_const_helper::DATATYPE_ENUM_CLASS_TYPE,
                csv_client_const_helper::HEADER_LOCATION => [csv_client_const_helper::DATATYPE_STRING, csv_client_const_helper::DATATYPE_NULL],
                csv_client_const_helper::HEADER_SCHOOL_SOURCEDID => csv_client_const_helper::DATATYPE_GUID,
                csv_client_const_helper::HEADER_TERM_SOURCEDIDS => [csv_client_const_helper::DATATYPE_ARRAY_GUID],
                csv_client_const_helper::HEADER_SUBJECTS => [csv_client_const_helper::DATATYPE_ARRAY_SUBJECTS, csv_client_const_helper::DATATYPE_NULL],
                csv_client_const_helper::HEADER_SUBJECT_CODES => [csv_client_const_helper::DATATYPE_ARRAY_SUBJECT_CODES, csv_client_const_helper::DATATYPE_NULL],
                csv_client_const_helper::HEADER_PERIODS => [csv_client_const_helper::DATATYPE_ARRAY_PERIODS, csv_client_const_helper::DATATYPE_NULL],
            ],
            csv_client_const_helper::FILE_ENROLLMENTS => [
                csv_client_const_helper::HEADER_SOURCEDID => csv_client_const_helper::DATATYPE_GUID,
                csv_client_const_helper::HEADER_STATUS => [csv_client_const_helper::DATATYPE_ENUM_STATUS, csv_client_const_helper::DATATYPE_NULL],
                csv_client_const_helper::HEADER_DATE_LAST_MODIFIED => [csv_client_const_helper::DATATYPE_DATETIME, csv_client_const_helper::DATATYPE_NULL],
                csv_client_const_helper::HEADER_CLASS_SOURCEDID => csv_client_const_helper::DATATYPE_GUID,
                csv_client_const_helper::HEADER_SCHOOL_SOURCEDID => csv_client_const_helper::DATATYPE_GUID,
                csv_client_const_helper::HEADER_USER_SOURCEDID => csv_client_const_helper::DATATYPE_GUID,
                csv_client_const_helper::HEADER_ROLE => csv_client_const_helper::DATATYPE_ENUM_TYPE_ENROL,
                csv_client_const_helper::HEADER_PRIMARY => [csv_client_const_helper::DATATYPE_ENUM_PRIMARY, csv_client_const_helper::DATATYPE_NULL],
                csv_client_const_helper::HEADER_BEGIN_DATE => [csv_client_const_helper::DATATYPE_DATE, csv_client_const_helper::DATATYPE_NULL],
                csv_client_const_helper::HEADER_END_DATE => [csv_client_const_helper::DATATYPE_DATE, csv_client_const_helper::DATATYPE_NULL],
            ],
            csv_client_const_helper::FILE_ORGS => [
                csv_client_const_helper::HEADER_SOURCEDID => csv_client_const_helper::DATATYPE_GUID,
                csv_client_const_helper::HEADER_STATUS => [csv_client_const_helper::DATATYPE_ENUM_STATUS, csv_client_const_helper::DATATYPE_NULL],
                csv_client_const_helper::HEADER_DATE_LAST_MODIFIED => [csv_client_const_helper::DATATYPE_DATETIME, csv_client_const_helper::DATATYPE_NULL],
                csv_client_const_helper::HEADER_NAME => csv_client_const_helper::DATATYPE_STRING,
                csv_client_const_helper::HEADER_TYPE => csv_client_const_helper::DATATYPE_ENUM_ORG_TYPE,
                csv_client_const_helper::HEADER_IDENTIFIER => [csv_client_const_helper::DATATYPE_STRING, csv_client_const_helper::DATATYPE_NULL],
                csv_client_const_helper::HEADER_PARENT_SOURCEDID => [csv_client_const_helper::DATATYPE_GUID, csv_client_const_helper::DATATYPE_NULL],
            ],
            csv_client_const_helper::FILE_USERS => [
                csv_client_const_helper::HEADER_SOURCEDID => csv_client_const_helper::DATATYPE_GUID,
                csv_client_const_helper::HEADER_STATUS => [csv_client_const_helper::DATATYPE_ENUM_STATUS, csv_client_const_helper::DATATYPE_NULL],
                csv_client_const_helper::HEADER_DATE_LAST_MODIFIED => [csv_client_const_helper::DATATYPE_DATETIME, csv_client_const_helper::DATATYPE_NULL],
                csv_client_const_helper::HEADER_ENABLED_USER => csv_client_const_helper::DATATYPE_ENUM_PRIMARY,
                csv_client_const_helper::HEADER_ORG_SOURCEDIDS => csv_client_const_helper::DATATYPE_ARRAY_GUID,
                csv_client_const_helper::HEADER_ROLE => csv_client_const_helper::DATATYPE_ENUM_ROLE_USER,
                csv_client_const_helper::HEADER_USERNAME => csv_client_const_helper::DATATYPE_STRING,
                csv_client_const_helper::HEADER_USERIDS => [csv_client_const_helper::DATATYPE_ARRAY_USERIDS, csv_client_const_helper::DATATYPE_NULL],
                csv_client_const_helper::HEADER_GIVEN_NAME => csv_client_const_helper::DATATYPE_STRING,
                csv_client_const_helper::HEADER_FAMILY_NAME => csv_client_const_helper::DATATYPE_STRING,
                csv_client_const_helper::HEADER_MIDDLE_NAME => [csv_client_const_helper::DATATYPE_STRING, csv_client_const_helper::DATATYPE_NULL],
                csv_client_const_helper::HEADER_IDENTIFIER => [csv_client_const_helper::DATATYPE_STRING, csv_client_const_helper::DATATYPE_NULL],
                csv_client_const_helper::HEADER_EMAIL => [csv_client_const_helper::DATATYPE_STRING_EMAIL, csv_client_const_helper::DATATYPE_NULL],
                csv_client_const_helper::HEADER_SMS => [csv_client_const_helper::DATATYPE_STRING, csv_client_const_helper::DATATYPE_NULL],
                csv_client_const_helper::HEADER_PHONE => [csv_client_const_helper::DATATYPE_STRING, csv_client_const_helper::DATATYPE_NULL],
                csv_client_const_helper::HEADER_AGENT_SOURCEDIDS => [csv_client_const_helper::DATATYPE_ARRAY_GUID, csv_client_const_helper::DATATYPE_NULL],
                csv_client_const_helper::HEADER_GRADES => [csv_client_const_helper::DATATYPE_GRADE, csv_client_const_helper::DATATYPE_NULL],
                csv_client_const_helper::HEADER_PASSWORD => [csv_client_const_helper::DATATYPE_PASSWORD, csv_client_const_helper::DATATYPE_NULL],
            ],
        ];
    }
    /**
     * Get the validator functions for each data type.
     * 
     * @return array An array containing the validator functions for each data type
     */
    public static function get_validator(): array {
        return [
            csv_client_const_helper::DATATYPE_GUID => 'is_guid_type',
            csv_client_const_helper::DATATYPE_INT => 'is_int_type',
            csv_client_const_helper::DATATYPE_DATETIME => 'is_datetime_type',
            csv_client_const_helper::DATATYPE_DATE => 'is_date_type',
            csv_client_const_helper::DATATYPE_YEAR => 'is_year_type',
            csv_client_const_helper::DATATYPE_ENUM_STATUS => 'is_status_enum_type',
            csv_client_const_helper::DATATYPE_ENUM_TYPE => 'is_type_enum',
            csv_client_const_helper::DATATYPE_ARRAY_GUID => 'is_valid_guid_list',
            csv_client_const_helper::DATATYPE_ARRAY_GRADE => 'is_valid_grades',
            csv_client_const_helper::DATATYPE_GRADE => 'is_valid_grade',
            csv_client_const_helper::DATATYPE_STRING_EMAIL => 'is_email_type',
            csv_client_const_helper::DATATYPE_ARRAY_USERIDS => 'is_valid_user_id',
            csv_client_const_helper::DATATYPE_ENUM_ROLE_USER => 'is_role_user_enum',
            csv_client_const_helper::DATATYPE_ENUM_TYPE_ENROL => 'is_role_enum',
            csv_client_const_helper::DATATYPE_ENUM_PRIMARY => 'is_primary_enum',
            csv_client_const_helper::DATATYPE_ENUM_CLASS_TYPE => 'is_class_type_enum',
            csv_client_const_helper::DATATYPE_ENUM_ORG_TYPE => 'is_org_type_enum',
            csv_client_const_helper::DATATYPE_ARRAY_SUBJECTS => 'is_list_of_strings',
            csv_client_const_helper::DATATYPE_ARRAY_SUBJECT_CODES => 'is_valid_subject_codes',
            csv_client_const_helper::DATATYPE_PASSWORD => 'is_valid_password',
            csv_client_const_helper::DATATYPE_ARRAY_PERIODS => 'is_valid_periods',
            csv_client_const_helper::DATATYPE_STRING => 'is_valid_human_readable_string',
        ];
    }

    /**
     * Function to validate CSV headers.
     *
     * @param string $file_path Path to the CSV file
     * @return bool True if the headers are valid, false otherwise
     */
    public static function validate_csv_headers(string $file_path): bool {
        $clean_file_path = clean_param($file_path, PARAM_PATH);
        $file_name = basename($clean_file_path);
        $expected_headers = self::getHeader($file_name);

        if (($handle = fopen($clean_file_path, "r")) !== false) {
            $headers = fgetcsv($handle, 0, ",");
            fclose($handle);
            return $headers === $expected_headers;
        } 
        return false;
    }

    /**
     * Function to check if the manifest and required files are present.
     *
     * @param string $manifest_path Path to the manifest file
     * @param string $tempdir Path to the temporary directory
     * @return array An array containing the missing files and invalid headers
     */
    public static function check_manifest_and_files(string $manifest_path, string $tempdir): array {
        $invalid_headers = [];
        $required_files = [];

        if (($handle = fopen($manifest_path, "r")) !== false) {
            while (($data = fgetcsv($handle, 0, ",")) !== false) {
                if (in_array($data[1], ['bulk', 'delta'])) {
                    // remove 'file.' prefix and add '.csv' suffix in the manifest file to clean the param names.
                    $required_files[] = str_replace('file.', '', $data[0]) . '.csv';
                }
            }
            fclose($handle);
        }

        $extracted_files = array_diff(scandir($tempdir), array('.', '..', 'uploadedzip.zip'));
        $missing_files = array_diff($required_files, $extracted_files);

        foreach ($required_files as $file) {
            $clean_file_path = clean_param($file, PARAM_PATH);

            if (in_array($clean_file_path, $extracted_files)) {
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
     * Function to extract CSV files to arrays.
     *
     * @param string $directory Path to the directory containing the CSV files
     * @return array An associative array containing the CSV data
     */
    public static function extract_csvs_to_arrays(string $directory): array {
        $csv_data = [];
        $files = scandir($directory);
    
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'csv') {
                $file_name = pathinfo($file, PATHINFO_FILENAME);
                $csv_data[$file_name] = [];

                if (($handle = fopen($directory . '/' . $file, 'r')) !== false) {
                    $headers = fgetcsv($handle, 0, ',');
                    while (($row = fgetcsv($handle, 0, ',')) !== false) {
                        $csv_data[$file_name][] = array_combine($headers, $row);
                    }
                    fclose($handle);
                }
            }
        }
        return $csv_data;
    }

    /**
     * Function to display missing and invalid files.
     *
     * @param array $missing_files An array containing the missing files and invalid headers
     * @return string The error messages to be displayed
     */
    public static function display_missing_and_invalid_files(array $missing_files): string {
        $critical_files = [
            csv_client_const_helper::FILE_ACADEMIC_SESSIONS,
            csv_client_const_helper::FILE_CLASSES,
            csv_client_const_helper::FILE_ENROLLMENTS,
            csv_client_const_helper::FILE_ORGS,
            csv_client_const_helper::FILE_USERS
        ];

        $error_message = '';

        if (!empty($missing_files['missing_files'])) {
            $critical_missing_files = [];
            $non_critical_files = [];

            foreach ($missing_files['missing_files'] as $missing_file) {
                if (in_array($missing_file, $critical_files, true)) {
                    $critical_missing_files[] = $missing_file;
                } else {
                    $non_critical_files[] = $missing_file;
                }
            }

            if (!empty($critical_missing_files)) {
                $critical_files_list = implode(', ', $critical_missing_files);
                $error_message .= get_string('missingfiles', 'enrol_oneroster', (object)['files' => $critical_files_list]) . '<br>';
            }

            foreach ($non_critical_files as $non_critical_file) {
                $error_message .= get_string('invalid_manifest_selection', 'enrol_oneroster', (object)['manifest' => $non_critical_file]) . '<br>';
            }
        }

        if (!empty($missing_files['invalid_headers'])) {
            $invalid_headers_list = implode(', ', $missing_files['invalid_headers']);
            $error_message .= get_string('invalidheaders', 'enrol_oneroster', (object)['headers' => $invalid_headers_list]) . '<br>';
        }

        return $error_message;
    }
    
    /**
     * Function to get the header for a given file.
     *
     * @param string $file_name The name of the file
     * @return array The header for the given file
     */
    public static function getHeader(string $file_name): array {
        switch ($file_name) {
            case csv_client_const_helper::FILE_ACADEMIC_SESSIONS:
                return csv_client_const_helper::HEADER_ACADEMIC_SESSIONS;
            case csv_client_const_helper::FILE_CLASSES:
                return csv_client_const_helper::HEADER_CLASSES;
            case csv_client_const_helper::FILE_ENROLLMENTS:
                return csv_client_const_helper::HEADER_ENROLLMENTS;
            case csv_client_const_helper::FILE_ORGS:
                return csv_client_const_helper::HEADER_ORGS;
            case csv_client_const_helper::FILE_USERS:
                return csv_client_const_helper::HEADER_USERS;
            default:
                return [];
        }
    }

    /**
     * Get the expected data types for a given file.
     *
     * @param string $file_name The name of the file
     * @return array The expected data types for the given file
     */
    public static function get_data_types(string $file_name): array {
        return self::get_file_datatypes()[$file_name] ?? [];
    }

    /**
     * Validate the data types of the CSV files.
     *
     * @param string $directory The directory containing the CSV files
     * @return array An array containing the validity of the files, the invalid files, and error messages
     */
    public static function validate_csv_data_types(string $directory): array {
        $is_valid = true;
        $invalid_files = [];
        $error_messages = [];

        $files = scandir($directory);

        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || pathinfo($file, PATHINFO_EXTENSION) !== 'csv' || $file === csv_client_const_helper::FILE_MANIFEST) {
                continue;
            }

            $clean_file_path = clean_param($directory . '/' . $file, PARAM_PATH);
            $expected_data_types = self::get_data_types($file);
            $detected_data_types = [];

            if (($handle = fopen($clean_file_path, "r")) !== false) {
                $headers = fgetcsv($handle, 0, ",");
                if ($headers === false) {
                    $is_valid = false;
                    $invalid_files[] = $file;
                    $error_messages[] = "Failed to read headers from CSV file: $file";
                    continue;
                }

                $detected_data_types = array_fill(0, count($headers), 'unknown');

                while (($row = fgetcsv($handle, 0, ",")) !== false) {
                    $row = array_slice($row, 0, count($headers));
                    foreach ($row as $index => $value) {
                        if (isset($headers[$index])) {
                            $detected_type = self::determine_data_type($value, $expected_data_types[$headers[$index]] ?? []);
                            
                            if ($detected_data_types[$index] === 'unknown' || $detected_data_types[$index] === csv_client_const_helper::DATATYPE_NULL || $detected_data_types[$index] !== false) {
                                $detected_data_types[$index] = $detected_type;
                            }
                        }
                    }
                }

                fclose($handle);

                $file_is_valid = true;
                foreach ($headers as $index => $header) {
                    $expected_types = $expected_data_types[$header] ?? [get_string('na', 'enrol_oneroster')];
                    $detected_type = $detected_data_types[$index];
                    if (!in_array($detected_type, (array)$expected_types, true)) {
                        $error_messages[] = get_string('validation', 'enrol_oneroster', (object)[ 'header' => $header, 'file' => $file]);
                        $is_valid = false;
                        $file_is_valid = false;
                    }
                }

                if (!$file_is_valid) {
                    $invalid_files[] = $file;
                }
            }
        }

        return [
            'is_valid' => $is_valid,
            'invalid_files' => $invalid_files,
            'error_messages' => $error_messages
        ];
    }

    /**
     * Function to display errors for CSV data type validation.
     *
     * @param array $validation_result An array containing the validity of the files, the invalid files, and error messages
     * @return string The error messages to be displayed
     */
    public static function display_validation_errors(array $validation_result): string {
        $error_message = '';

        if (!empty($validation_result['error_messages'])) {
            $error_message .= \html_writer::tag('h3', get_string('datatype_error_messages', 'enrol_oneroster'));

            $error_list_items = '';
            foreach ($validation_result['error_messages'] as $message) {
                $error_list_items .= \html_writer::tag('li', $message);
            }

            $error_message .= \html_writer::tag('ul', $error_list_items);

            $reference_message = get_string('reference_message', 'enrol_oneroster') . ' ';
            $link = \html_writer::link(
                'https://www.imsglobal.org/oneroster-v11-final-csv-tables#_Toc480293266',
                get_string('csv_spec', 'enrol_oneroster'),
                ['target' => '_blank']
            );
            $error_message .= \html_writer::tag('p', $reference_message . $link . '.');
        }

        return $error_message;
    }

    /**
     * Function to validate users and configure settings for database entry.
     * If all users have an identifier, the users will be saved to the database.
     * Otherwise, no users will be saved.
     *
     * @param array $csv_data An array containing user data, including identifiers and other relevant details.
     * @return bool True if all users have an identifier and password, false otherwise
     */
    public static function validate_user_data(array $csv_data): bool {
        foreach ($csv_data['users'] as $user) {
            if (empty($user['identifier']) || empty($user['password'])) {
                return false;
            }
        }
        return true;
    }

    /**
     * Determine the data type of a value.
     *
     * @param string $value The value to determine the data type of
     * @param array $expected_types The expected data types for the value
     * @return string The detected data type
     */
    public static function determine_data_type($value, $expected_types): string {
        if (trim($value) === '') {
            return csv_client_const_helper::DATATYPE_NULL;
        }

        foreach ((array)$expected_types as $expected_type) {
            if (isset(self::get_validator()[$expected_type]) && call_user_func([self::class, self::get_validator()[$expected_type]], $value)) {
                return $expected_type;
            }
        }
        return 'unknown';
    }

    /**
     * Check if a value is a valid password.
     *
     * @param mixed $value The value to check
     * @return bool True if the value is a valid password, false otherwise
     */
    public static function is_valid_password($value): bool {
        return check_password_policy($value, $errormsg);
    }

    /**
     * Check if a value is a valid human-readable string.
     *
     * A valid human-readable string is defined as a string that:
     * - Contains only letters, numbers, spaces, and a limited set of punctuation characters (.,-).
     * - The regex uses Unicode properties to allow for letters from different languages (\p{L} for letters and \p{N} for numbers).
     *
     * @param mixed $value The value to check
     * @return bool True if the value is a human-readable string, false otherwise
     */
    public static function is_valid_human_readable_string($value): bool {
        $trimmed_value = trim($value);
        $result = is_string($trimmed_value) && preg_match('/^[\p{L}\p{N}\s.,-]+$/u', $trimmed_value);
        
        return $result;
    }

    /**
     * Check if a value is of type int.
     *
     * @param mixed $value The value to check
     * @return bool True if the value is of type int, false otherwise
     */
    public static function is_int_type($value): bool {
        return is_numeric($value) && filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

   /**
    * Check if a value is of type list (array of strings).
    *
    * @param mixed $value The value to check
    * @return bool True if the value is of type list of strings, false otherwise
    */
    public static function is_list_of_strings($value): bool {
        if (is_string($value)) {
            $value = array_map('trim', explode(',', $value)); 
        }
    
        foreach ($value as &$item) {
            $item = str_replace(',', '', $item);
            if (trim($item) === '') {
                continue;
            }
            if (!is_string($item)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Validate and parse subject codes.
     * Subject codes can be a single string or a list of strings separated by commas
     *
     * @param string $value The value to check
     * @return bool True if the value is a valid list of subject codes, false otherwise
     */
    public static function is_valid_subject_codes(string $value): bool {
        if (is_string($value)) {
            $value = array_map('trim', explode(',', $value)); 
        }

        foreach ($value as $item) {
            if (preg_match('/^".*?"$/', trim($item))) {
                $item = trim($item, '"');
                $codes = explode(',', $item);
                foreach ($codes as $code) {
                    if (!is_string(trim($code)) || trim($code) === '') {
                        return false; 
                    }
                }
            } else {
                if (!is_string(trim($item)) || trim($item) === '') {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Validate and parse periods string.
     *
     * @param string $value The value to check and parse
     * @return bool True if the value is a valid periods string, false otherwise
     */
    public static function is_valid_periods(string $value): bool {
        $value = trim($value);

        if (preg_match('/^".*?"$/', $value)) {
            $value = trim($value, '"');
        }

        $periods = array_map('trim', explode(',', $value));

        foreach ($periods as $period) {
            if (!self::is_int_type($period)) {
                return false;
            }
        }
        return true;
    }
 
    /**
     * Check if a value is of type datetime.
     * Regular expression for ISO 8601 datetime format (YYYY-MM-DDTHH:MM:SS.SSSZ).
     *
     * If the value is in the old format (YYYY-MM-DD), it transforms to the new format (YYYY-MM-DDT23:59:59.999Z).
     *
     * @param mixed $value The value to check
     * @return bool|string True if the value is of type datetime, transformed datetime string if in v1.0 format, false otherwise
     */
    public static function is_datetime_type($value) {
        if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d{3}Z$/', $value)) {
            return true;
        }

        // Check if the value matches the v1.0 format (YYYY-MM-DD)
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return true;
        }
        return false;
    }

    /**
     * Check if a value is of type date.
     * Regular expression for ISO 8601 date format (YYYY-MM-DD).
     *
     * @param mixed $value The value to check
     * @return bool True if the value is of type date, false otherwise
     */
    public static function is_date_type($value) {
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) === 1;
    }

    /**
     * Check if a value is of type GUID.
     * Regular expression for GUID (characters: 0-9, a-z, A-Z, ., -, _, /, @).
     *
     * @param mixed $value The value to check
     * @return bool True if the value is of type GUID, false otherwise
     */
    public static function is_guid_type($value): bool {
        return strlen($value) < 256 && preg_match('/^[0-9a-zA-Z.\-_\/@]+$/', $value) === 1;
    }

    /**
     * Check if a value is of type status enum.
     *
     * @param mixed $value The value to check
     * @return bool True if the value is of type status enum, false otherwise
     */
    public static function is_status_enum_type($value): bool {
        $valid_status_values = ['active', 'tobedeleted', 'inactive'];
        return in_array(strtolower($value), $valid_status_values, true);
    }

    /**
     * Check if a value is of type enum.
     *
     * @param mixed $value The value to check
     * @return bool True if the value is of type type enum, false otherwise
     */
    public static function is_type_enum($value): bool {
        $valid_type_values = ['gradingPeriod', 'semester', 'schoolYear', 'term'];
        return in_array($value, $valid_type_values, true);
    }

    /**
     * Check if a value is of type year.
     * Regular expression for year format (YYYY).
     *
     * @param mixed $value The value to check
     * @return bool True if the value is of type year, false otherwise
     */
    public static function is_year_type($value): bool {
        return preg_match('/^\d{4}$/', $value) === 1;
    }

    /**
     * Check if a value is of type grade.
     *
     * @param mixed $value The value to check
     * @return bool True if the value is of type grade, false otherwise
     */
    public static function is_valid_grades($value): bool {
        $grades = array_map('trim', explode(',', $value));

        foreach ($grades as $grade) {
            if (!self::is_valid_grade($grade)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if a value is of type grade.
     *
     * @param string $value The value to check
     * @return bool True if the value is of type grade, false otherwise
     */
    public static function is_valid_grade(string $value): bool {
        $value = trim($value);
        return in_array($value, csv_client_const_helper::valid_grade_codes, true);
    }

    /**
     * Check if a value is of type class type enum.
     *
     * @param mixed $value The value to check
     * @return bool True if the value is of type class type enum, false otherwise
     */
    public static function is_class_type_enum($value): bool {        
        return in_array(strtolower($value), csv_client_const_helper::valid_class_types, true);
    }

    /**
     * Check if a value is of type GUID list.
     *
     * @param mixed $value The value to check
     * @return bool True if the value is of type GUID list, false otherwise
     */
    public static function is_valid_guid_list($value): bool {
        $guids = array_map('trim', explode(',', $value));
    
        foreach ($guids as $guid) {
            if (!self::is_guid_type($guid)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if a value is of type role enum.
     *
     * @param string $value The value to check
     * @return bool True if the value is of type role enum, false otherwise
     */
    public static function is_role_enum(string $value): bool {
        return in_array(strtolower($value), csv_client_const_helper::valid_roles, true);
    }

    /**
     * Check if a value is of type primary enum.
     *
     * @param mixed $value The value to check
     * @return bool True if the value is of type primary enum, false otherwise
     */
    public static function is_primary_enum($value): bool {
        return in_array(strtolower($value), csv_client_const_helper::valid_primary_values, true);
    }

    /**
     * Check if a value is of type org type enum.
     *
     * @param mixed $value The value to check
     * @return bool True if the value is of type org type enum, false otherwise
     */
    public static function is_org_type_enum($value): bool {
        return in_array(strtolower($value), csv_client_const_helper::valid_org_types, true);
    }

    /**
     * Check if a value is of type email.
     *
     * @param mixed $value The value to check
     * @return bool True if the value is of type email, false otherwise
     */
    public static function is_email_type($value): bool {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Check if a value is of type user ID or a valid user ID list.
     * Regular expression for user ID format ({[a-zA-Z0-9]+:[a-zA-Z0-9]+}).
     *
     * @param mixed $value The value to check
     * @return bool True if the value is of type user ID or user ID list, false otherwise
     */
    public static function is_valid_user_id($value): bool {
        $value = trim($value, '"');
        $userIds = array_map('trim', explode(',', $value));
        
        foreach ($userIds as $userId) {
            if (!preg_match('/^\{[a-zA-Z0-9]+:[a-zA-Z0-9]+\}$/', $userId)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if a value is of type role user enum.
     *
     * @param mixed $value The value to check
     * @return bool True if the value is of type role user enum, false otherwise
     */
    public static function is_role_user_enum($value): bool {
        return in_array(strtolower($value), csv_client_const_helper::valid_roles_users, true);
    }
}
