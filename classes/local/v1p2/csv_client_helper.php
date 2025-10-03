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

/**
 * Class csv_client_helper
 *
 * Helper class for OneRoster plugin
 *
 * @package    enrol_oneroster
 * @copyright  QUT Capstone Team - Abhinav Gandham, Harrison Dyba, Jonathon Foo, Kushi Patel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class csv_client_helper extends csv_client_helper_version_one{
    /**
     * Get the expected data types for each file.
     *
     * @return array An array containing the expected data types for each file.
     */
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
           csv_client_const_helper::HEADER_APPLICATION_ID => csv_client_const_helper::DATATYPE_STRING,
           csv_client_const_helper::HEADER_DESCRIPTION => csv_client_const_helper::DATATYPE_STRING,
           csv_client_const_helper::HEADER_CREDENTIAL_TYPE => csv_client_const_helper::DATATYPE_STRING,
           csv_client_const_helper::HEADER_USERNAME => csv_client_const_helper::DATATYPE_STRING,
           csv_client_const_helper::HEADER_PASSWORD => csv_client_const_helper::DATATYPE_STRING
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
                csv_client_const_helper::HEADER_BEGIN_DATE => [
                    csv_client_const_helper::DATATYPE_DATE,
                    csv_client_const_helper::DATATYPE_NULL
                ],    
                csv_client_const_helper::HEADER_END_DATE => [
                    csv_client_const_helper::DATATYPE_DATE,
                    csv_client_const_helper::DATATYPE_NULL
                ],
                csv_client_const_helper::HEADER_ORG_SOURCEDID => csv_client_const_helper::DATATYPE_GUID,
                csv_client_const_helper::HEADER_USERPROFILE_SOURCEDID => csv_client_const_helper::DATATYPE_GUID
            ]
         ];
      $mergedarray = array_merge($data, $roles, $users, $courses);
      $totalmergeddata = array_merge($mergedarray, $userprofiledata);
      return $totalmergeddata;
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
              default:
                return [];
        }
      
        if ($data!= []) {
            return $data;
        }      
      
        return parent::get_header($filename);
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

}
