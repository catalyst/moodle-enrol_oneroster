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

    /**
     * Get the expected data types for each file.
     *
     * @return array An array containing the expected data types for each file.
     */
    public static function get_file_datatypes(): array {
        $parent_datatypes = parent::get_file_datatypes();

        // Add v1p2 specific file data types
        $v1p2_datatypes = [
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
                csv_client_const_helper::HEADER_DESCRIPTION => [
                    csv_client_const_helper::DATATYPE_STRING,
                    csv_client_const_helper::DATATYPE_NULL
                ],
                csv_client_const_helper::HEADER_CREDENTIAL_TYPE => csv_client_const_helper::DATATYPE_STRING,
                csv_client_const_helper::HEADER_USERNAME => csv_client_const_helper::DATATYPE_STRING,
                csv_client_const_helper::HEADER_PASSWORD => [
                    csv_client_const_helper::DATATYPE_PASSWORD,
                    csv_client_const_helper::DATATYPE_NULL
                ],
            ],
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
                    csv_client_const_helper::DATATYPE_NULL
                ],
            ],
        ];

        return array_merge($parent_datatypes, $v1p2_datatypes);
    }

    /**
     * Get the validator functions for each data type.
     *
     * @return array An array containing the validator functions for each data type.
     */
    public static function get_validator(): array {
        $parent_validators = parent::get_validator();

        // Add v1p2 specific validators
        $v1p2_validators = [
            csv_client_const_helper::DATATYPE_ENUM_ROLE_TYPE => 'is_role_type_enum',
            csv_client_const_helper::DATATYPE_ENUM_ROLE_ROLE => 'is_role_role_enum',
        ];

        return array_merge($parent_validators, $v1p2_validators);
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
