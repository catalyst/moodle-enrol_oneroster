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

/**
 * Class OneRosterHelper
 *
 * Helper class for OneRoster plugin
 *
 * @package    enrol_oneroster
 * @copyright  Gustavo Amorim De Almeida, Ruben Cooper, Josh Bateson, Brayden Porter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class OneRosterHelper {
    // Data type definitions for each file
    public static function get_file_datatypes(): array {
        return [
            OneRosterConstHelper::FILE_ACADEMIC_SESSIONS => [
                OneRosterConstHelper::HEADER_SOURCEDID => OneRosterConstHelper::DATATYPE_GUID,
                OneRosterConstHelper::HEADER_STATUS => [OneRosterConstHelper::DATATYPE_ENUM_STATUS, OneRosterConstHelper::DATATYPE_NULL],
                OneRosterConstHelper::HEADER_DATE_LAST_MODIFIED => [OneRosterConstHelper::DATATYPE_DATETIME, OneRosterConstHelper::DATATYPE_NULL],
                OneRosterConstHelper::HEADER_TITLE => OneRosterConstHelper::DATATYPE_STRING,
                OneRosterConstHelper::HEADER_TYPE => OneRosterConstHelper::DATATYPE_ENUM_TYPE,
                OneRosterConstHelper::HEADER_START_DATE => OneRosterConstHelper::DATATYPE_DATE,
                OneRosterConstHelper::HEADER_END_DATE => OneRosterConstHelper::DATATYPE_DATE,
                OneRosterConstHelper::HEADER_PARENT_SOURCEDID => [OneRosterConstHelper::DATATYPE_GUID, OneRosterConstHelper::DATATYPE_NULL],
                OneRosterConstHelper::HEADER_SCHOOL_YEAR => OneRosterConstHelper::DATATYPE_YEAR,
            ],
            OneRosterConstHelper::FILE_CLASSES => [
                OneRosterConstHelper::HEADER_SOURCEDID => OneRosterConstHelper::DATATYPE_GUID,
                OneRosterConstHelper::HEADER_STATUS => [OneRosterConstHelper::DATATYPE_ENUM_STATUS, OneRosterConstHelper::DATATYPE_NULL],
                OneRosterConstHelper::HEADER_DATE_LAST_MODIFIED => [OneRosterConstHelper::DATATYPE_DATETIME, OneRosterConstHelper::DATATYPE_NULL],
                OneRosterConstHelper::HEADER_TITLE => OneRosterConstHelper::DATATYPE_STRING,
                OneRosterConstHelper::HEADER_GRADES => [OneRosterConstHelper::DATATYPE_ARRAY_GRADE, OneRosterConstHelper::DATATYPE_NULL],
                OneRosterConstHelper::HEADER_COURSE_SOURCEDID => OneRosterConstHelper::DATATYPE_GUID,
                OneRosterConstHelper::HEADER_CLASS_CODE => [OneRosterConstHelper::DATATYPE_STRING, OneRosterConstHelper::DATATYPE_NULL],
                OneRosterConstHelper::HEADER_CLASS_TYPE => OneRosterConstHelper::DATATYPE_ENUM_CLASS_TYPE,
                OneRosterConstHelper::HEADER_LOCATION => [OneRosterConstHelper::DATATYPE_STRING, OneRosterConstHelper::DATATYPE_NULL],
                OneRosterConstHelper::HEADER_SCHOOL_SOURCEDID => OneRosterConstHelper::DATATYPE_GUID,
                OneRosterConstHelper::HEADER_TERM_SOURCEDIDS => [OneRosterConstHelper::DATATYPE_ARRAY_GUID],
                OneRosterConstHelper::HEADER_SUBJECTS => [OneRosterConstHelper::DATATYPE_ARRAY_SUBJECTS, OneRosterConstHelper::DATATYPE_NULL],
                OneRosterConstHelper::HEADER_SUBJECT_CODES => [OneRosterConstHelper::DATATYPE_ARRAY_SUBJECT_CODES, OneRosterConstHelper::DATATYPE_NULL],
                OneRosterConstHelper::HEADER_PERIODS => [OneRosterConstHelper::DATATYPE_ARRAY_PERIODS, OneRosterConstHelper::DATATYPE_NULL],
            ],
            OneRosterConstHelper::FILE_ENROLLMENTS => [
                OneRosterConstHelper::HEADER_SOURCEDID => OneRosterConstHelper::DATATYPE_GUID,
                OneRosterConstHelper::HEADER_STATUS => [OneRosterConstHelper::DATATYPE_ENUM_STATUS, OneRosterConstHelper::DATATYPE_NULL],
                OneRosterConstHelper::HEADER_DATE_LAST_MODIFIED => [OneRosterConstHelper::DATATYPE_DATETIME, OneRosterConstHelper::DATATYPE_NULL],
                OneRosterConstHelper::HEADER_CLASS_SOURCEDID => OneRosterConstHelper::DATATYPE_GUID,
                OneRosterConstHelper::HEADER_SCHOOL_SOURCEDID => OneRosterConstHelper::DATATYPE_GUID,
                OneRosterConstHelper::HEADER_USER_SOURCEDID => OneRosterConstHelper::DATATYPE_GUID,
                OneRosterConstHelper::HEADER_ROLE => OneRosterConstHelper::DATATYPE_ENUM_TYPE_ENROL,
                OneRosterConstHelper::HEADER_PRIMARY => [OneRosterConstHelper::DATATYPE_ENUM_PRIMARY, OneRosterConstHelper::DATATYPE_NULL],
                OneRosterConstHelper::HEADER_BEGIN_DATE => [OneRosterConstHelper::DATATYPE_DATE, OneRosterConstHelper::DATATYPE_NULL],
                OneRosterConstHelper::HEADER_END_DATE => [OneRosterConstHelper::DATATYPE_DATE, OneRosterConstHelper::DATATYPE_NULL],
            ],
            OneRosterConstHelper::FILE_ORGS => [
                OneRosterConstHelper::HEADER_SOURCEDID => OneRosterConstHelper::DATATYPE_GUID,
                OneRosterConstHelper::HEADER_STATUS => [OneRosterConstHelper::DATATYPE_ENUM_STATUS, OneRosterConstHelper::DATATYPE_NULL],
                OneRosterConstHelper::HEADER_DATE_LAST_MODIFIED => [OneRosterConstHelper::DATATYPE_DATETIME, OneRosterConstHelper::DATATYPE_NULL],
                OneRosterConstHelper::HEADER_NAME => OneRosterConstHelper::DATATYPE_STRING,
                OneRosterConstHelper::HEADER_TYPE => OneRosterConstHelper::DATATYPE_ENUM_ORG_TYPE,
                OneRosterConstHelper::HEADER_IDENTIFIER => [OneRosterConstHelper::DATATYPE_STRING, OneRosterConstHelper::DATATYPE_NULL],
                OneRosterConstHelper::HEADER_PARENT_SOURCEDID => [OneRosterConstHelper::DATATYPE_GUID, OneRosterConstHelper::DATATYPE_NULL],
            ],
            OneRosterConstHelper::FILE_USERS => [
                OneRosterConstHelper::HEADER_SOURCEDID => OneRosterConstHelper::DATATYPE_GUID,
                OneRosterConstHelper::HEADER_STATUS => [OneRosterConstHelper::DATATYPE_ENUM_STATUS, OneRosterConstHelper::DATATYPE_NULL],
                OneRosterConstHelper::HEADER_DATE_LAST_MODIFIED => [OneRosterConstHelper::DATATYPE_DATETIME, OneRosterConstHelper::DATATYPE_NULL],
                OneRosterConstHelper::HEADER_ENABLED_USER => OneRosterConstHelper::DATATYPE_ENUM_PRIMARY,
                OneRosterConstHelper::HEADER_ORG_SOURCEDIDS => OneRosterConstHelper::DATATYPE_ARRAY_GUID,
                OneRosterConstHelper::HEADER_ROLE => OneRosterConstHelper::DATATYPE_ENUM_ROLE_USER,
                OneRosterConstHelper::HEADER_USERNAME => OneRosterConstHelper::DATATYPE_STRING,
                OneRosterConstHelper::HEADER_USERIDS => [OneRosterConstHelper::DATATYPE_ARRAY_USERIDS, OneRosterConstHelper::DATATYPE_NULL],
                OneRosterConstHelper::HEADER_GIVEN_NAME => OneRosterConstHelper::DATATYPE_STRING,
                OneRosterConstHelper::HEADER_FAMILY_NAME => OneRosterConstHelper::DATATYPE_STRING,
                OneRosterConstHelper::HEADER_MIDDLE_NAME => [OneRosterConstHelper::DATATYPE_STRING, OneRosterConstHelper::DATATYPE_NULL],
                OneRosterConstHelper::HEADER_IDENTIFIER => [OneRosterConstHelper::DATATYPE_STRING, OneRosterConstHelper::DATATYPE_NULL],
                OneRosterConstHelper::HEADER_EMAIL => [OneRosterConstHelper::DATATYPE_STRING_EMAIL, OneRosterConstHelper::DATATYPE_NULL],
                OneRosterConstHelper::HEADER_SMS => [OneRosterConstHelper::DATATYPE_STRING, OneRosterConstHelper::DATATYPE_NULL],
                OneRosterConstHelper::HEADER_PHONE => [OneRosterConstHelper::DATATYPE_STRING, OneRosterConstHelper::DATATYPE_NULL],
                OneRosterConstHelper::HEADER_AGENT_SOURCEDIDS => [OneRosterConstHelper::DATATYPE_ARRAY_GUID, OneRosterConstHelper::DATATYPE_NULL],
                OneRosterConstHelper::HEADER_GRADES => [OneRosterConstHelper::DATATYPE_GRADE, OneRosterConstHelper::DATATYPE_NULL],
                OneRosterConstHelper::HEADER_PASSWORD => [OneRosterConstHelper::DATATYPE_PASSWORD, OneRosterConstHelper::DATATYPE_NULL],
            ],
        ];
    }

    // Validators for each data type
    public static function get_validator(): array {
        return [
            OneRosterConstHelper::DATATYPE_GUID => 'is_guid_type',
            OneRosterConstHelper::DATATYPE_INT => 'is_int_type',
            OneRosterConstHelper::DATATYPE_DATETIME => 'is_datetime_type',
            OneRosterConstHelper::DATATYPE_DATE => 'is_date_type',
            OneRosterConstHelper::DATATYPE_YEAR => 'is_year_type',
            OneRosterConstHelper::DATATYPE_ENUM_STATUS => 'is_status_enum_type',
            OneRosterConstHelper::DATATYPE_ENUM_TYPE => 'is_type_enum',
            OneRosterConstHelper::DATATYPE_ARRAY_GUID => 'is_valid_guid_list',
            OneRosterConstHelper::DATATYPE_ARRAY_GRADE => 'is_valid_grades',
            OneRosterConstHelper::DATATYPE_GRADE => 'is_valid_grade',
            OneRosterConstHelper::DATATYPE_STRING_EMAIL => 'is_email_type',
            OneRosterConstHelper::DATATYPE_ARRAY_USERIDS => 'is_valid_user_id',
            OneRosterConstHelper::DATATYPE_ENUM_ROLE_USER => 'is_role_user_enum',
            OneRosterConstHelper::DATATYPE_ENUM_TYPE_ENROL => 'is_role_enum',
            OneRosterConstHelper::DATATYPE_ENUM_PRIMARY => 'is_primary_enum',
            OneRosterConstHelper::DATATYPE_ENUM_CLASS_TYPE => 'is_class_type_enum',
            OneRosterConstHelper::DATATYPE_ENUM_ORG_TYPE => 'is_org_type_enum',
            OneRosterConstHelper::DATATYPE_ARRAY_SUBJECTS => 'is_list_of_strings',
            OneRosterConstHelper::DATATYPE_ARRAY_SUBJECT_CODES => 'is_valid_subject_codes',
            OneRosterConstHelper::DATATYPE_PASSWORD => 'is_valid_password',
            OneRosterConstHelper::DATATYPE_ARRAY_PERIODS => 'is_valid_periods',
            OneRosterConstHelper::DATATYPE_STRING => 'is_valid_human_readable_string',
        ];
    }

    /**
     * Function to validate CSV headers
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
     * Function to display missing and invalid files
     *
     * @param array $missing_files An array containing the missing files and invalid headers
     */
    public static function display_missing_and_invalid_files($missing_files) {
        $critical_files = [OneRosterConstHelper::FILE_ACADEMIC_SESSIONS, OneRosterConstHelper::FILE_CLASSES, OneRosterConstHelper::FILE_ENROLLMENTS, OneRosterConstHelper::FILE_ORGS, OneRosterConstHelper::FILE_USERS];
    
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
                echo get_string('missingfiles', 'enrol_oneroster', ['a' => $critical_files_list]) . '<br>';
            }

            foreach ($non_critical_files as $non_critical_file) {
                echo get_string('invalid_manifest_selection', 'enrol_oneroster', ['a' => $non_critical_file]) . '<br>';
            }
        }

        if (!empty($missing_files['invalid_headers'])) {
            $invalid_headers_list = implode(', ', $missing_files['invalid_headers']);
            echo get_string('invalidheaders', 'enrol_oneroster', ['a' => $invalid_headers_list]) . '<br>';
        }
    }
    
    /**
     * Function to get the header for a given file
     *
     * @param string $file_name The name of the file
     * @return array The header for the given file
     */
    public static function getHeader($file_name) {
        switch ($file_name) {
            case OneRosterConstHelper::FILE_ACADEMIC_SESSIONS:
                return OneRosterConstHelper::HEADER_ACADEMIC_SESSIONS;
            case OneRosterConstHelper::FILE_CLASSES:
                return OneRosterConstHelper::HEADER_CLASSES;
            case OneRosterConstHelper::FILE_ENROLLMENTS:
                return OneRosterConstHelper::HEADER_ENROLLMENTS;
            case OneRosterConstHelper::FILE_ORGS:
                return OneRosterConstHelper::HEADER_ORGS;
            case OneRosterConstHelper::FILE_USERS:
                return OneRosterConstHelper::HEADER_USERS;
            default:
                return [];
        }
    }

    /**
     * Get the expected data types for a given file
     *
     * @param string $file_name The name of the file
     * @return array The expected data types for the given file
     */
    public static function get_data_types($file_name) {
        return self::get_file_datatypes()[$file_name] ?? [];
    }

    /**
     * Validate the data types of the CSV files
     *
     * @param string $directory The directory containing the CSV files
     * @return array An array containing the validity of the files, the invalid files, and error messages
     */
    public static function validate_csv_data_types($directory) {
        $is_valid = true;
        $invalid_files = [];
        $error_messages = [];

        $files = scandir($directory);

        foreach ($files as $file) {
            if ($file === '.' || $file === '..' || pathinfo($file, PATHINFO_EXTENSION) !== 'csv' || $file === OneRosterConstHelper::FILE_MANIFEST) {
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
                            
                            if ($detected_data_types[$index] === 'unknown' || $detected_data_types[$index] === OneRosterConstHelper::DATATYPE_NULL || $detected_data_types[$index] !== false) {
                                $detected_data_types[$index] = $detected_type;
                            }
                        }
                    }
                }

                fclose($handle);

                $file_is_valid = true;
                foreach ($headers as $index => $header) {
                    $expected_types = $expected_data_types[$header] ?? ['N/A'];
                    $detected_type = $detected_data_types[$index];
                    if (!in_array($detected_type, (array)$expected_types, true)) {
                        $error_messages[] = "Validation failed for header '$header' in file '$file'.";
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
     * Function to display errors for CSV data type validation
     *
     * @param array $validation_result An array containing the validity of the files, the invalid files, and error messages
     */
    public static function display_validation_errors($validation_result) {
        if (!empty($validation_result['error_messages'])) {
            echo \html_writer::tag('h3', get_string('datatype_error_messages', 'enrol_oneroster'));

            $error_list_items = '';
            foreach ($validation_result['error_messages'] as $message) {
                $error_list_items .= \html_writer::tag('li', $message);
            }

            echo \html_writer::tag('ul', $error_list_items);
            $reference_message = get_string('reference_message', 'enrol_oneroster') . ' ';
            $link = \html_writer::link('https://www.imsglobal.org/oneroster-v11-final-csv-tables#_Toc480293266', get_string('csv_spec', 'enrol_oneroster'), ['target' => '_blank']);
            echo \html_writer::tag('p', $reference_message . $link . '.');
        }
    }

    /**
     * Function to validate users and configure settings for database entry
     * If all users have an identifier, the users will be saved to the database
     * Otherwise, no users will be saved
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
     * Determine the data type of a value
     *
     * @param string $value The value to determine the data type of
     * @param array $expected_types The expected data types for the value
     * @return string The detected data type
     */
    public static function determine_data_type($value, $expected_types) {
        if (trim($value) === '') {
            return OneRosterConstHelper::DATATYPE_NULL;
        }

        foreach ((array)$expected_types as $expected_type) {
            if (isset(self::get_validator()[$expected_type]) && call_user_func([self::class, self::get_validator()[$expected_type]], $value)) {
                return $expected_type;
            }
        }
        return 'unknown';
    }

    /**
     * Check if a value is a valid password
     *
     * @param mixed $value The value to check
     * @return bool True if the value is a valid password, false otherwise
     */
    public static function is_valid_password($value) {
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
     * Check if a value is of type int
     *
     * @param mixed $value The value to check
     * @return bool True if the value is of type int, false otherwise
     */
    public static function is_int_type($value): bool {
        return is_numeric($value) && filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

   /**
    * Check if a value is of type list (array of strings)
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
     * Validate and parse subject codes
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
     * Check if a value is of type datetime
     * Regular expression for ISO 8601 datetime format (YYYY-MM-DDTHH:MM:SS.SSSZ)
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
     * Check if a value is of type date
     * Regular expression for ISO 8601 date format (YYYY-MM-DD)
     *
     * @param mixed $value The value to check
     * @return bool True if the value is of type date, false otherwise
     */
    public static function is_date_type($value) {
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) === 1;
    }

    /**
     * Check if a value is of type 
     * Regular expression for GUID (characters: 0-9, a-z, A-Z, ., -, _, /, @)
     *
     * @param mixed $value The value to check
     * @return bool True if the value is of type GUID, false otherwise
     */
    public static function is_guid_type($value): bool {
        return strlen($value) < 256 && preg_match('/^[0-9a-zA-Z.\-_\/@]+$/', $value) === 1;
    }

    /**
     * Check if a value is of type status enum
     *
     * @param mixed $value The value to check
     * @return bool True if the value is of type status enum, false otherwise
     */
    public static function is_status_enum_type($value): bool {
        $valid_status_values = ['active', 'tobedeleted', 'inactive'];
        return in_array(strtolower($value), $valid_status_values, true);
    }

    /**
     * Check if a value is of type type enum
     *
     * @param mixed $value The value to check
     * @return bool True if the value is of type type enum, false otherwise
     */
    public static function is_type_enum($value): bool {
        $valid_type_values = ['gradingPeriod', 'semester', 'schoolYear', 'term'];
        return in_array($value, $valid_type_values, true);
    }

    /**
     * Check if a value is of type year
     * Regular expression for year format (YYYY)
     *
     * @param mixed $value The value to check
     * @return bool True if the value is of type year, false otherwise
     */
    public static function is_year_type($value): bool {
        return preg_match('/^\d{4}$/', $value) === 1;
    }

    /**
     * Check if a value is of type grade
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
     * Check if a value is of type grade
     *
     * @param string $value The value to check
     * @return bool True if the value is of type grade, false otherwise
     */
    public static function is_valid_grade(string $value): bool {
        $value = trim($value);
        return in_array($value, OneRosterConstHelper::valid_grade_codes, true);
    }

    /**
     * Check if a value is of type class type enum
     *
     * @param mixed $value The value to check
     * @return bool True if the value is of type class type enum, false otherwise
     */
    public static function is_class_type_enum($value): bool {        
        return in_array(strtolower($value), OneRosterConstHelper::valid_class_types, true);
    }

    /**
     * Check if a value is of type GUID list
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
     * Check if a value is of type role enum
     *
     * @param string $value The value to check
     * @return bool True if the value is of type role enum, false otherwise
     */
    public static function is_role_enum(string $value): bool {
        return in_array(strtolower($value), OneRosterConstHelper::valid_roles, true);
    }

    /**
     * Check if a value is of type primary enum
     *
     * @param mixed $value The value to check
     * @return bool True if the value is of type primary enum, false otherwise
     */
    public static function is_primary_enum($value): bool {
        return in_array(strtolower($value), OneRosterConstHelper::valid_primary_values, true);
    }

    /**
     * Check if a value is of type org type enum
     *
     * @param mixed $value The value to check
     * @return bool True if the value is of type org type enum, false otherwise
     */
    public static function is_org_type_enum($value): bool {
        return in_array(strtolower($value), OneRosterConstHelper::valid_org_types, true);
    }

    /**
     * Check if a value is of type email
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
     * Check if a value is of type role user enum
     *
     * @param mixed $value The value to check
     * @return bool True if the value is of type role user enum, false otherwise
     */
    public static function is_role_user_enum($value): bool {
        return in_array(strtolower($value), OneRosterConstHelper::valid_roles_users, true);
    }
}
