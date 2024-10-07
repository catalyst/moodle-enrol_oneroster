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
    // Define individual header constants
    const HEADER_SOURCEDID = 'sourcedId';
    const HEADER_STATUS = 'status';
    const HEADER_DATE_LAST_MODIFIED = 'dateLastModified';
    const HEADER_TITLE = 'title';
    const HEADER_TYPE = 'type';
    const HEADER_START_DATE = 'startDate';
    const HEADER_END_DATE = 'endDate';
    const HEADER_PARENT_SOURCEDID = 'parentSourcedId';
    const HEADER_SCHOOL_YEAR = 'schoolYear';
    const HEADER_GRADES = 'grades';
    const HEADER_COURSE_SOURCEDID = 'courseSourcedId';
    const HEADER_CLASS_CODE = 'classCode';
    const HEADER_CLASS_TYPE = 'classType';
    const HEADER_LOCATION = 'location';
    const HEADER_SCHOOL_SOURCEDID = 'schoolSourcedId';
    const HEADER_TERM_SOURCEDIDS = 'termSourcedIds';
    const HEADER_SUBJECTS = 'subjects';
    const HEADER_SUBJECT_CODES = 'subjectCodes';
    const HEADER_PERIODS = 'periods';
    const HEADER_CLASS_SOURCEDID = 'classSourcedId';
    const HEADER_USER_SOURCEDID = 'userSourcedId';
    const HEADER_ROLE = 'role';
    const HEADER_PRIMARY = 'primary';
    const HEADER_BEGIN_DATE = 'beginDate';
    const HEADER_IDENTIFIER = 'identifier';
    const HEADER_NAME = 'name';
    const HEADER_ENABLED_USER = 'enabledUser';
    const HEADER_ORG_SOURCEDIDS = 'orgSourcedIds';
    const HEADER_USERNAME = 'username';
    const HEADER_USERIDS = 'userIds';
    const HEADER_GIVEN_NAME = 'givenName';
    const HEADER_FAMILY_NAME = 'familyName';
    const HEADER_MIDDLE_NAME = 'middleName';
    const HEADER_EMAIL = 'email';
    const HEADER_SMS = 'sms';
    const HEADER_PHONE = 'phone';
    const HEADER_AGENT_SOURCEDIDS = 'agentSourcedIds';
    const HEADER_PASSWORD = 'password';

    // Define header arrays using the individual constants
    const HEADER_MANIFEST = [
        'manifest.version',
        'oneroster.version',
        'file.academicSessions',
        'file.categories',
        'file.classes',
        'file.classResources',
        'file.courses',
        'file.courseResources',
        'file.demographics',
        'file.enrollments',
        'file.lineItems',
        'file.orgs',
        'file.resources',
        'file.results',
        'file.users'
    ];
    const HEADER_ACADEMIC_SESSIONS = [
        self::HEADER_SOURCEDID, self::HEADER_STATUS, self::HEADER_DATE_LAST_MODIFIED, self::HEADER_TITLE,
        self::HEADER_TYPE, self::HEADER_START_DATE, self::HEADER_END_DATE, self::HEADER_PARENT_SOURCEDID, self::HEADER_SCHOOL_YEAR
    ];
    const HEADER_CLASSES = [
        self::HEADER_SOURCEDID, self::HEADER_STATUS, self::HEADER_DATE_LAST_MODIFIED, self::HEADER_TITLE, self::HEADER_GRADES,
        self::HEADER_COURSE_SOURCEDID, self::HEADER_CLASS_CODE, self::HEADER_CLASS_TYPE, self::HEADER_LOCATION,
        self::HEADER_SCHOOL_SOURCEDID, self::HEADER_TERM_SOURCEDIDS, self::HEADER_SUBJECTS, self::HEADER_SUBJECT_CODES, self::HEADER_PERIODS
    ];
    const HEADER_ENROLLMENTS = [
        self::HEADER_SOURCEDID, self::HEADER_STATUS, self::HEADER_DATE_LAST_MODIFIED, self::HEADER_CLASS_SOURCEDID,
        self::HEADER_SCHOOL_SOURCEDID, self::HEADER_USER_SOURCEDID, self::HEADER_ROLE, self::HEADER_PRIMARY,
        self::HEADER_BEGIN_DATE, self::HEADER_END_DATE
    ];
    const HEADER_ORGS = [
        self::HEADER_SOURCEDID, self::HEADER_STATUS, self::HEADER_DATE_LAST_MODIFIED, self::HEADER_NAME, self::HEADER_TYPE,
        self::HEADER_IDENTIFIER, self::HEADER_PARENT_SOURCEDID
    ];
    const HEADER_USERS = [
        self::HEADER_SOURCEDID, self::HEADER_STATUS, self::HEADER_DATE_LAST_MODIFIED, self::HEADER_ENABLED_USER,
        self::HEADER_ORG_SOURCEDIDS, self::HEADER_ROLE, self::HEADER_USERNAME, self::HEADER_USERIDS,
        self::HEADER_GIVEN_NAME, self::HEADER_FAMILY_NAME, self::HEADER_MIDDLE_NAME, self::HEADER_IDENTIFIER,
        self::HEADER_EMAIL, self::HEADER_SMS, self::HEADER_PHONE, self::HEADER_AGENT_SOURCEDIDS, self::HEADER_GRADES, self::HEADER_PASSWORD
    ];
    const REQUIRED_FILES = [
        'manifest.csv' => self::HEADER_MANIFEST,
        'academicSessions.csv' => self::HEADER_ACADEMIC_SESSIONS,
        'classes.csv' => self::HEADER_CLASSES,
        'enrollments.csv' => self::HEADER_ENROLLMENTS,
        'orgs.csv' => self::HEADER_ORGS,
        'users.csv' => self::HEADER_USERS,
    ];

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
            $headers = fgetcsv($handle, 1000, ",");
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
            echo get_string('missingfiles', 'enrol_oneroster') . implode(', ', $missing_files['missing_files']) . '<br>';
        }
        if (!empty($missing_files['invalid_headers'])) {
            echo get_string('invalidheaders', 'enrol_oneroster')  . implode(', ', $missing_files['invalid_headers']) . '<br>';
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
            case 'manifest.csv':
                return self::HEADER_MANIFEST;
            case 'academicSessions.csv':
                return self::HEADER_ACADEMIC_SESSIONS;
            case 'classes.csv':
                return self::HEADER_CLASSES;
            case 'enrollments.csv':
                return self::HEADER_ENROLLMENTS;
            case 'orgs.csv':
                return self::HEADER_ORGS;
            case 'users.csv':
                return self::HEADER_USERS;
            default:
                return [];
        }
    }
}
