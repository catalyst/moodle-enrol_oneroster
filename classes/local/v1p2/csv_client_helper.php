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
namespace enrol_oneroster\local\v1p2;


use enrol_oneroster\local\v1p2\csv_client_const_helper;
use enrol_oneroster\local\v1p1\csv_client_helper as csv_client_helper_version_one;
use PHPUnit\Framework\Constraint\ArrayHasKey;

use function PHPUnit\Framework\assertEquals;

require_once(__DIR__ . '/../v1p1/csv_client_helper.php');
require_once(__DIR__ . '/csv_client_const_helper.php');
use enrol_oneroster\local\v1p1\csv_client_helper as csv_client_helper_version_one;

/**
 * Class csv_client_helper
 *
 * Helper class for OneRoster v1p2 plugin
 *
 * @package    enrol_oneroster
 * @copyright  QUT Capstone Team - Abhinav Gandham, Harrison Dyba, Jonathon Foo, Kushi Patel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class csv_client_helper extends csv_client_helper_version_one {

    /**
     * Function to get the header for a given file.
     *
     * @param string $filename The name of the file.
     * @return array The header for the given file.
     */
    public static function get_header(string $filename): array {
        switch ($filename) {
            case csv_client_const_helper::FILE_USERPROFILES:
                return csv_client_const_helper::HEADER_USERPROFILES;
            case csv_client_const_helper::FILE_ROLES:
                return csv_client_const_helper::HEADER_ROLES;
            default:
                return parent::get_header($filename);
        }
    }

   public static function get_file_datatypes(): array {
        //call parent.
        $data = parent::get_file_datatypes();
        //Remove v1p1 users.
        unset($data[csv_client_const_helper::FILE_USERS]);

        //Add new Users.
        $users = [
            csv_client_const_helper::FILE_USERS => [
                csv_client_const_helper::HEADER_SOURCEDID => csv_client_const_helper::DATATYPE_GUID,
                csv_client_const_helper::HEADER_STATUS => [
                    csv_client_const_helper::DATATYPE_ENUM_STATUS,
                    csv_client_const_helper::DATATYPE_NULL
                ],
                csv_client_const_helper::HEADER_DATE_LAST_MODIFIED => [
                    csv_client_const_helper::DATATYPE_DATETIME,
                    csv_client_const_helper::DATATYPE_NULL
                ],
                csv_client_const_helper::HEADER_ENABLED_USER => csv_client_const_helper::DATATYPE_ENUM_PRIMARY,
                csv_client_const_helper::HEADER_USERNAME => csv_client_const_helper::DATATYPE_STRING,
                csv_client_const_helper::HEADER_USERIDS => [
                    csv_client_const_helper::DATATYPE_ARRAY_USERIDS,
                    csv_client_const_helper::DATATYPE_NULL
                ],
                csv_client_const_helper::HEADER_GIVEN_NAME => csv_client_const_helper::DATATYPE_STRING,
                csv_client_const_helper::HEADER_FAMILY_NAME => csv_client_const_helper::DATATYPE_STRING,
                csv_client_const_helper::HEADER_MIDDLE_NAME => [
                    csv_client_const_helper::DATATYPE_STRING,
                    csv_client_const_helper::DATATYPE_NULL
                ],
                csv_client_const_helper::HEADER_IDENTIFIER => [
                    csv_client_const_helper::DATATYPE_STRING,
                    csv_client_const_helper::DATATYPE_NULL
                ],
                csv_client_const_helper::HEADER_EMAIL => [
                    csv_client_const_helper::DATATYPE_STRING_EMAIL,
                    csv_client_const_helper::DATATYPE_NULL
                ],
                csv_client_const_helper::HEADER_SMS => [
                    csv_client_const_helper::DATATYPE_STRING,
                    csv_client_const_helper::DATATYPE_NULL
                ],
                csv_client_const_helper::HEADER_PHONE => [
                    csv_client_const_helper::DATATYPE_STRING,
                    csv_client_const_helper::DATATYPE_NULL
                ],
                csv_client_const_helper::HEADER_AGENT_SOURCEDIDS => [
                    csv_client_const_helper::DATATYPE_ARRAY_GUID,
                    csv_client_const_helper::DATATYPE_NULL
                ],
                csv_client_const_helper::HEADER_GRADES => [
                    csv_client_const_helper::DATATYPE_ARRAY_GRADE,
                    csv_client_const_helper::DATATYPE_NULL
                ],
                csv_client_const_helper::HEADER_PASSWORD => [
                    csv_client_const_helper::DATATYPE_PASSWORD,
                    csv_client_const_helper::DATATYPE_NULL
                ],
                csv_client_const_helper::HEADER_MASTER_IDENTIFIER => [
                    csv_client_const_helper::DATATYPE_STRING,
                    csv_client_const_helper::DATATYPE_NULL
                ],
                csv_client_const_helper::HEADER_PREFERRED_GIVEN_NAME => [
                    csv_client_const_helper::DATATYPE_STRING,
                    csv_client_const_helper::DATATYPE_NULL
                ],
                csv_client_const_helper::HEADER_PREFERRED_MIDDLE_NAME => [
                    csv_client_const_helper::DATATYPE_STRING,
                    csv_client_const_helper::DATATYPE_NULL
                ],
                csv_client_const_helper::HEADER_PREFERRED_FAMILY_NAME => [
                    csv_client_const_helper::DATATYPE_STRING,
                    csv_client_const_helper::DATATYPE_NULL
                ],
                csv_client_const_helper::HEADER_PRIMARY_ORG_SOURCED_ID => [
                    csv_client_const_helper::DATATYPE_GUID,
                    csv_client_const_helper::DATATYPE_NULL
                ],
                csv_client_const_helper::HEADER_PRONOUNS => [
                    csv_client_const_helper::DATATYPE_STRING,
                    csv_client_const_helper::DATATYPE_NULL
                ],
            ]
        ];
        $demographics = [
            csv_client_const_helper::FILE_DEMOGRAPHICS =>[
                csv_client_const_helper::HEADER_SOURCEDID => csv_client_const_helper::DATATYPE_GUID,
                csv_client_const_helper::HEADER_STATUS => [
                    csv_client_const_helper::DATATYPE_ENUM_STATUS,
                    csv_client_const_helper::DATATYPE_NULL 
                ],
                csv_client_const_helper::HEADER_DATE_LAST_MODIFIED => [
                    csv_client_const_helper::DATATYPE_DATETIME,
                    csv_client_const_helper::DATATYPE_NULL
                ],
                csv_client_const_helper::HEADER_BIRTHDATE =>[
                    csv_client_const_helper::DATATYPE_DATE,
                    csv_client_const_helper::DATATYPE_NULL
                ],
                csv_client_const_helper::HEADER_SEX => [
                    csv_client_const_helper::DATATYPE_SEX,
                    csv_client_const_helper::DATATYPE_NULL
                ],
                csv_client_const_helper::HEADER_AMERICAN_INDIAN_OR_ALASKA_NATIVE =>[
                    csv_client_const_helper::DATATYPE_DEMOGRAPHIC_ENUM,
                    csv_client_const_helper::DATATYPE_NULL
                ],
                csv_client_const_helper::HEADER_ASIAN =>[
                    csv_client_const_helper::DATATYPE_DEMOGRAPHIC_ENUM,
                    csv_client_const_helper::DATATYPE_NULL
                ],
                csv_client_const_helper::HEADER_BLACK_OR_AFRICAN_AMERICAN =>[
                    csv_client_const_helper::DATATYPE_DEMOGRAPHIC_ENUM,
                    csv_client_const_helper::DATATYPE_NULL
                ],
                csv_client_const_helper::HEADER_NATIVE_HAWAIIAN_OR_OTHER_PACIFIC_ISLANDER =>[
                    csv_client_const_helper::DATATYPE_DEMOGRAPHIC_ENUM,
                    csv_client_const_helper::DATATYPE_NULL
                ],
                csv_client_const_helper::HEADER_WHITE =>[
                    csv_client_const_helper::DATATYPE_DEMOGRAPHIC_ENUM,
                    csv_client_const_helper::DATATYPE_NULL
                ],
                csv_client_const_helper::HEADER_DEMOGRAPHIC_RACE_TWO_OR_MORE_RACES =>[
                    csv_client_const_helper::DATATYPE_DEMOGRAPHIC_ENUM,
                    csv_client_const_helper::DATATYPE_NULL
                ],
                csv_client_const_helper::HEADER_HISPANIC_OR_LATINO_ETHNICITY =>[
                    csv_client_const_helper::DATATYPE_DEMOGRAPHIC_ENUM,
                    csv_client_const_helper::DATATYPE_NULL
                ],
                csv_client_const_helper::HEADER_COUNTRY_OF_BIRTH_CODE =>[
                    csv_client_const_helper::DATATYPE_STRING,
                    csv_client_const_helper::DATATYPE_NULL
                ],
                csv_client_const_helper::HEADER_STATE_OF_BITH_ABBREVIATION =>[
                    csv_client_const_helper::DATATYPE_STRING,
                    csv_client_const_helper::DATATYPE_NULL
                ],
                csv_client_const_helper::HEADER_CITY_OF_BIRTH =>[
                    csv_client_const_helper::DATATYPE_STRING,
                    csv_client_const_helper::DATATYPE_NULL
                ],
                csv_client_const_helper::HEADER_PUBLIC_SCHOOL_RESIDENCE_STATUS =>[
                    csv_client_const_helper::DATATYPE_STRING,
                    csv_client_const_helper::DATATYPE_NULL
                ]
            ]
        ];
        $courses = [
            csv_client_const_helper::FILE_COURSES =>[
                csv_client_const_helper::HEADER_SOURCEDID => csv_client_const_helper::DATATYPE_GUID,
                csv_client_const_helper::HEADER_STATUS => [
                    csv_client_const_helper::DATATYPE_ENUM_STATUS,
                    csv_client_const_helper::DATATYPE_NULL 
                ],
                csv_client_const_helper::HEADER_DATE_LAST_MODIFIED => [
                    csv_client_const_helper::DATATYPE_DATETIME,
                    csv_client_const_helper::DATATYPE_NULL
                ],
                csv_client_const_helper::HEADER_SCHOOL_YEAR_SOURCED_ID => [
                    csv_client_const_helper::DATATYPE_GUID,
                    csv_client_const_helper::DATATYPE_NULL
                ],
                csv_client_const_helper::HEADER_TITLE => csv_client_const_helper::DATATYPE_STRING,
                csv_client_const_helper::HEADER_COURSE_CODE => [
                    csv_client_const_helper::DATATYPE_STRING,
                    csv_client_const_helper::DATATYPE_NULL,
                ],
                csv_client_const_helper::HEADER_COURSE_CODE => [
                    csv_client_const_helper::DATATYPE_STRING,
                    csv_client_const_helper::DATATYPE_NULL,
                ],
                csv_client_const_helper::HEADER_GRADES => [
                    csv_client_const_helper::DATATYPE_ARRAY_GRADE,
                    csv_client_const_helper::DATATYPE_NULL,
                ],
                csv_client_const_helper::HEADER_ORG_SOURCEDID => csv_client_const_helper::DATATYPE_GUID,
                csv_client_const_helper::HEADER_SUBJECTS => [
                    csv_client_const_helper::DATATYPE_STRING,
                    csv_client_const_helper::DATATYPE_NULL,
                ],
                csv_client_const_helper::HEADER_SUBJECT_CODES => [
                    csv_client_const_helper::DATATYPE_STRING,
                    csv_client_const_helper::DATATYPE_NULL,
                ]
            ]
        ];

        // add userprofiles.
        $userprofiledata = [
        csv_client_const_helper::FILE_USERPROFILES => [
           csv_client_const_helper::HEADER_SOURCEDID => csv_client_const_helper::DATATYPE_GUID,
           csv_client_const_helper::HEADER_STATUS => [
               csv_client_const_helper::DATATYPE_ENUM_STATUS,
               csv_client_const_helper::DATATYPE_NULL
           ],
           csv_client_const_helper::HEADER_DATE_LAST_MODIFIED => [
               csv_client_const_helper::DATATYPE_DATETIME,
               csv_client_const_helper::DATATYPE_NULL
           ],
           csv_client_const_helper::HEADER_USER_SOURCEDID => csv_client_const_helper::DATATYPE_GUID,
           csv_client_const_helper::HEADER_PROFILE_TYPE => csv_client_const_helper::DATATYPE_STRING,
           csv_client_const_helper::HEADER_VENDOR_ID => csv_client_const_helper::DATATYPE_STRING,
           csv_client_const_helper::HEADER_APPLICATION_ID => [
            csv_client_const_helper::DATATYPE_STRING,
            csv_client_const_helper::DATATYPE_NULL],
           csv_client_const_helper::HEADER_DESCRIPTION => [
            csv_client_const_helper::DATATYPE_STRING,
            csv_client_const_helper::DATATYPE_NULL],
           csv_client_const_helper::HEADER_CREDENTIAL_TYPE => csv_client_const_helper::DATATYPE_STRING,
           csv_client_const_helper::HEADER_USERNAME => csv_client_const_helper::DATATYPE_STRING,
           csv_client_const_helper::HEADER_PASSWORD => [
             csv_client_const_helper::DATATYPE_STRING,
             csv_client_const_helper::DATATYPE_NULL]
        ]
     ];
      //add roles
      $roles= 
         [
            csv_client_const_helper::FILE_ROLES => [
                csv_client_const_helper::HEADER_SOURCEDID => csv_client_const_helper::DATATYPE_GUID,
                csv_client_const_helper::HEADER_STATUS => [
                    csv_client_const_helper::DATATYPE_ENUM_STATUS,
                    csv_client_const_helper::DATATYPE_NULL
                ],
                csv_client_const_helper::HEADER_DATE_LAST_MODIFIED => [
                    csv_client_const_helper::DATATYPE_DATETIME,
                    csv_client_const_helper::DATATYPE_NULL
                ],
                csv_client_const_helper::HEADER_USER_SOURCEDID => csv_client_const_helper::DATATYPE_GUID,
                csv_client_const_helper::HEADER_ROLE_TYPE => csv_client_const_helper::DATATYPE_ENUM_ROLE_TYPE,
                csv_client_const_helper::HEADER_ROLE => csv_client_const_helper::DATATYPE_ENUM_ROLE_ROLE,
                csv_client_const_helper::HEADER_BEGIN_DATE => [
                    csv_client_const_helper::DATATYPE_DATE,
                    csv_client_const_helper::DATATYPE_NULL
                ],    
                csv_client_const_helper::HEADER_END_DATE => [
                    csv_client_const_helper::DATATYPE_DATE,
                    csv_client_const_helper::DATATYPE_NULL
                ],
                csv_client_const_helper::HEADER_ORG_SOURCEDID => csv_client_const_helper::DATATYPE_GUID,
                csv_client_const_helper::HEADER_USERPROFILE_SOURCEDID => [
                    csv_client_const_helper::DATATYPE_GUID,
                    csv_client_const_helper::DATATYPE_NULL] 
            ]
         ];
      $mergedarray = array_merge($data, $demographics, $courses, $roles, $userprofiledata, $users);
      return $mergedarray;
      }

    /**
     * Get the validator functions for each data type.
     *
     * @return array An array containing the validator functions for each data type.
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
            csv_client_const_helper::DATATYPE_ENUM_TYPE_ENROL => 'is_enrollment_role_enum',
            csv_client_const_helper::DATATYPE_ENUM_PRIMARY => 'is_primary_enum',
            csv_client_const_helper::DATATYPE_ENUM_CLASS_TYPE => 'is_class_type_enum',
            csv_client_const_helper::DATATYPE_ENUM_ORG_TYPE => 'is_org_type_enum',
            csv_client_const_helper::DATATYPE_ENUM_ROLE_TYPE => 'is_role_type_enum',
            csv_client_const_helper::DATATYPE_ENUM_ROLE_ROLE => 'is_role_enum',
            csv_client_const_helper::DATATYPE_ARRAY_SUBJECTS => 'is_list_of_strings',
            csv_client_const_helper::DATATYPE_ARRAY_SUBJECT_CODES => 'is_valid_subject_codes',
            csv_client_const_helper::DATATYPE_PASSWORD => 'is_valid_password',
            csv_client_const_helper::DATATYPE_ARRAY_PERIODS => 'is_valid_periods',
            csv_client_const_helper::DATATYPE_STRING => 'is_valid_human_readable_string',
            csv_client_const_helper::DATATYPE_DEMOGRAPHIC_ENUM => 'is_demographic_enum',
            csv_client_const_helper::DATATYPE_SEX => 'is_sex_enum'
        ];
    }
    /**
     * Function to display missing and invalid files.
     *
     * @param array $missingfiles An array containing the missing files and invalid headers.
     * @return string The error messages to be displayed.
     */
    public static function display_missing_and_invalid_files(array $missingfiles): string {
        $criticalfiles = [
            csv_client_const_helper::FILE_ACADEMIC_SESSIONS,
            csv_client_const_helper::FILE_CLASSES,
            csv_client_const_helper::FILE_COURSES,
            csv_client_const_helper::FILE_ENROLLMENTS,
            csv_client_const_helper::FILE_ORGS,
            csv_client_const_helper::FILE_USERS,
            csv_client_const_helper::FILE_ROLES,
        ];

        $errormessage = '';

        if (!empty($missingfiles['missingfiles'])) {
            $criticalmissingfiles = [];
            $noncriticalfiles = [];

            foreach ($missingfiles['missingfiles'] as $missingfile) {
                if (in_array($missingfile, $criticalfiles, true)) {
                    $criticalmissingfiles[] = $missingfile;
                } else {
                    $noncriticalfiles[] = $missingfile;
                }
            }

            if (!empty($criticalmissingfiles)) {
                $criticalfileslist = implode(', ', $criticalmissingfiles);
                $errormessage .= get_string(
                    'missingfiles',
                    'enrol_oneroster',
                    (object)['files' => $criticalfileslist]
                ) . '<br>';
            }

            foreach ($noncriticalfiles as $noncriticalfile) {
                $errormessage .= get_string(
                    'invalid_manifest_selection',
                    'enrol_oneroster',
                    (object)['manifest' => $noncriticalfile]
                ) . '<br>';
            }
        }

        if (!empty($missingfiles['invalidheaders'])) {
            $invalidheaderslist = implode(', ', $missingfiles['invalidheaders']);
            $errormessage .= get_string(
                'invalidheaders',
                'enrol_oneroster',
                (object)['headers' => $invalidheaderslist]
            ) . '<br>';
        }

        return $errormessage;
    }

  /**
     * Function to get the header for a given file.
     *
     * @param string $filename The name of the file.
     * @return array The header for the given file.
     */
    public static function get_header(string $filename): array {
        $data = [];
        switch ($filename) {
            case csv_client_const_helper::FILE_ROLES:
                $data = csv_client_const_helper::HEADER_ROLES;
                break;
            case csv_client_const_helper::FILE_USERPROFILES:
                $data = csv_client_const_helper::HEADER_USERPROFILES;
                break;
            case csv_client_const_helper::FILE_COURSES:
                $data = csv_client_const_helper::HEADER_COURSES;
                break;
            case csv_client_const_helper::FILE_USERS:
                $data = csv_client_const_helper::HEADER_USERS;
                break;
            case csv_client_const_helper::FILE_DEMOGRAPHICS:
                $data = csv_client_const_helper::HEADER_DEMOGRAPHICS;
                break;
            default:
                break;
        }
      
        if ($data!= []) {
            return $data;
        }      
        return parent::get_header($filename);
    }

    /**
     * Function to validate CSV headers.
     *
     * @param string $filepath Path to the CSV file.
     * @return bool True if the headers are valid, false otherwise.
     */
    public static function validate_csv_headers(string $filepath): bool {
        $cleanfilepath = clean_param($filepath, PARAM_PATH);
        $filename = basename($cleanfilepath);
        $expectedheaders = self::get_header($filename);
        if (($handle = fopen($cleanfilepath, 'r')) !== false) {
            $headers = fgetcsv($handle, 0, ',');
            fclose($handle);
            return $headers === $expectedheaders;
        }
        return false;
    }

    /**
     * Function to check if the manifest and required files are present.
     *
     * @param string $manifestpath Path to the manifest file.
     * @param string $tempdir Path to the temporary directory.
     * @return array An array containing the missing files and invalid headers.
     */
    public static function check_manifest_and_files(string $manifestpath, string $tempdir): array {
        $invalidheaders = [];
        $requiredfiles = [];

        if (($handle = fopen($manifestpath, 'r')) !== false) {
            while (($data = fgetcsv($handle, 0, ',')) !== false) {
                if (in_array($data[1], ['bulk', 'delta'])) {
                    // Remove 'file.' prefix and add '.csv' suffix in the manifest file to clean the param names.
                    $requiredfiles[] = str_replace('file.', '', $data[0]) . '.csv';
                }
            }
            fclose($handle);
        }

        $extractedfiles = array_diff(scandir($tempdir), ['.', '..', 'uploadedzip.zip']);
        $missingfiles = array_diff($requiredfiles, $extractedfiles);
        foreach ($requiredfiles as $file) {
            $cleanfilepath = clean_param($file, PARAM_PATH);

            if (in_array($cleanfilepath, $extractedfiles)) {
                $filepath = $tempdir . '/' . $file;
                if (!self::validate_csv_headers($filepath)) {
                    $invalidheaders[] = $file;
                }
            }
        }
        return [
            'missingfiles' => $missingfiles,
            'invalidheaders' => $invalidheaders
        ];
    }
    public static function get_data_types(string $filename): array {
        return self::get_file_datatypes()[$filename] ?? [];
    }
    public static function validate_csv_data_types(string $directory): array {
        $isvalid = true;
        $invalidfiles = [];
        $errormessages = [];

        $files = scandir($directory);

        foreach ($files as $file) {
            if ($file === '.' || $file === '..' ||
                pathinfo($file, PATHINFO_EXTENSION) !== 'csv' ||
                $file === csv_client_const_helper::FILE_MANIFEST) {
                continue;
            }

            $cleanfilepath = clean_param($directory . '/' . $file, PARAM_PATH);
            $expecteddatatypes = self::get_data_types($file);
            //var_dump($expecteddatatypes);
            $detecteddatatypes = [];

            if (($handle = fopen($cleanfilepath, 'r')) !== false) {
                $headers = fgetcsv($handle, 0, ',');
                if ($headers === false) {
                    $isvalid = false;
                    $invalidfiles[] = $file;
                    $errormessages[] = "Failed to read headers from CSV file: $file";
                    continue;
                }

                $detecteddatatypes = array_fill(0, count($headers), 'unknown');

                while (($row = fgetcsv($handle, 0, ',')) !== false) {
                    $row = array_slice($row, 0, count($headers));
                    foreach ($row as $index => $value) {
                        if (isset($headers[$index])) {
                            $detectedtype = self::determine_data_type(
                                $value,
                                $expecteddatatypes[$headers[$index]] ?? []
                            );

                            if ($detecteddatatypes[$index] === 'unknown' ||
                                $detecteddatatypes[$index] === csv_client_const_helper::DATATYPE_NULL ||
                                $detecteddatatypes[$index] !== false) {
                                $detecteddatatypes[$index] = $detectedtype;
                            }
                        }
                    }
                }

                fclose($handle);

                $fileisvalid = true;
                foreach ($headers as $index => $header) {
                    $expectedtypes = $expecteddatatypes[$header] ?? [get_string('na', 'enrol_oneroster')];
                    $detectedtype = $detecteddatatypes[$index];
                    if (!in_array($detectedtype, (array)$expectedtypes, true)) {
                        $errormessages[] = get_string(
                            'validation',
                            'enrol_oneroster',
                            (object)['header' => $header, 'file' => $file]
                        );
                        $isvalid = false;
                        $fileisvalid = false;
                    }
                }

                if (!$fileisvalid) {
                    $invalidfiles[] = $file;
                }
            }
        }

        return [
            'is_valid' => $isvalid,
            'invalid_files' => $invalidfiles,
            'error_messages' => $errormessages
        ];
    }

    /**
     * Determine the data type of a value.
     *
     * @param string $value The value to determine the data type of.
     * @param array $expectedtypes The expected data types for the value.
     * @return string The detected data type.
     */
    public static function determine_data_type($value, $expectedtypes): string {
        if (trim($value) === '') {
            return csv_client_const_helper::DATATYPE_NULL;
        }

        foreach ((array)$expectedtypes as $expectedtype) {
            if (isset(static::get_validator()[$expectedtype]) &&
                call_user_func([self::class, static::get_validator()[$expectedtype]], $value)) {
                    return $expectedtype;
            }
        }
        return 'unknown';
    }

    /**
     * Check if a value is of type role enum.
     *
     * @param string $value The value to check.
     * @return bool True if the value is of type role enum, false otherwise.
     */
    public static function is_enrollment_role_enum(string $value): bool {
        return in_array(strtolower($value), csv_client_const_helper::VALID_ENROLLMENT_ROLES, true);
    }

    /**
     * Check if a value is of type role type enum.
     *
     * @param mixed $value The value to check.
     * @return bool True if the value is of type role type enum, false otherwise.
     */
    public static function is_role_type_enum($value): bool {
        return in_array(strtolower($value), csv_client_const_helper::VALID_ROLE_TYPE, true);
    }
    /**
     * Check if a value is of type role enum.
     *
     * @param mixed $value The value to check.
     * @return bool True if the value is of type role enum, false otherwise.
     */
    public static function is_role_enum($value): bool {
        return in_array(strtolower($value), csv_client_const_helper::VALID_ROLE_ROLES, true);
    }

    /**
     * Check if a value is of type org type enum.
     *
     * @param mixed $value The value to check.
     * @return bool True if the value is of type org type enum, false otherwise.
     */
    public static function is_org_type_enum($value): bool {
        return in_array(strtolower($value), csv_client_const_helper::VALID_ORG_TYPES, true);
    }

    /**
     * Check if a value is of type role user enum.
     *
     * @param mixed $value The value to check.
     * @return bool True if the value is of type role user enum, false otherwise.
     */
    public static function is_role_user_enum($value): bool {
        return in_array(strtolower($value), csv_client_const_helper::VALID_ROLES_USERS, true);
    }

    /**
     * Check if a value is in the sex enum.
     *
     * @param mixed $value The value to check.
     * @return bool True if the value is of type sex enum, false otherwise.
     */
    public static function is_sex_enum($value): bool {
        return in_array(strtolower($value), csv_client_const_helper::VALID_DEMOGRAPHICS_SEX, true);

    }
    /**
     * Ensure a demographic value is 'true' or 'false'.
     *
     * @param mixed $value The value to check.
     * @return bool True if the value is 'true' or 'false', false otherwise.
     */
    public static function is_demographic_enum($value): bool {
        return in_array(strtolower($value), csv_client_const_helper::VALID_DEMOGRAPHICS_ENUM, true);
    }
    /**
     * Check if a birthDate value is in acceptable format.
     *
     * @param mixed $value The value to check.
     * @return bool True if the value is in YYYY-MM-DD format.
     */
    public static function is_valid_birth_date ($value): bool {
        // Check if the value matches the v1.0 format (YYYY-MM-DD).
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return true;
        }
        return false;
                    csv_client_const_helper::DATATYPE_NULL
                ],
            ],
        ];

        return array_merge($parent_datatypes, $v1p2_datatypes);
    }


    /**
     * Check if the value is a valid role type enum.
     *
     * @param string $value The value to check.
     * @return bool True if the value is a valid role type enum, false otherwise.
     */
    public static function is_role_type_enum(string $value): bool {
        return in_array($value, csv_client_const_helper::VALID_ROLE_TYPE, true);
    }

    /**
     * Check if the value is a valid role role enum.
     *
     * @param string $value The value to check.
     * @return bool True if the value is a valid role role enum, false otherwise.
     */
    public static function is_role_role_enum(string $value): bool {
        return in_array($value, csv_client_const_helper::VALID_ROLE_ROLES, true);
    }
}
