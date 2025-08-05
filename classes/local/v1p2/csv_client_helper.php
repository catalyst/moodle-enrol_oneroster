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
namespace enrol_oneroster\local\v1p1;

use enrol_oneroster\local\csv_client_const_helper;
use enrol_oneroster\local\v1p1\csv_client_helper as csv_client_helper_version_one;

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
    // Gets the already existing datatypes from the parent class.
    $parentdatatypes = parent::get_file_datatypes();
    // The new userprofile data that will be added to the datatypes.
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
     // Merges the parent datatypes with the new userprofile datatypes.
     return array_merge($parentdatatypes, $userprofiledata);
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
          csv_client_const_helper::FILE_ENROLLMENTS,
          csv_client_const_helper::FILE_ORGS,
          csv_client_const_helper::FILE_USERS,
          csv_client_const_helper::FILE_USERPROFILES
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
      $parentReturn = parent::get_header($filename);

      if ($parentReturn !== []) {
        return $parentReturn;
      };

      switch ($filename) {
          case csv_client_const_helper::FILE_USERPROFILES:
              return csv_client_const_helper::HEADER_USERPROFILES;
          default:
              return [];
      }
  }



}
