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

use enrol_oneroster\local\v1p1\csv_client_const_helper as csv_client_const_helper_version_one;

/**
 * Class csv_client_const_helper.
 *
 * This class contains constants that are used throughout the OneRoster 1.2 CSV client.
 *
 * @package    enrol_oneroster
 * @copyright  QUT Capstone Team - Abhinav Gandham, Harrison Dyba, Jonathon Foo, Kushi Patel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class csv_client_const_helper extends csv_client_const_helper_version_one{
   /**
   * Id field for the vendor id
   */
   const HEADER_VENDOR_ID = 'vendorId';

   /**
    * Id field for the application id
    */
   const HEADER_APPLICATION_ID = 'applicationId';

   /**
    * Type field for the user profile
    */
   const HEADER_PROFILE_TYPE = 'profileType';

   /**
    * Credential type field for the credential type
    */
   const HEADER_CREDENTIAL_TYPE = 'credentialType';

   /**
    * The description field for the user profile
    */
   const HEADER_DESCRIPTION = 'description';

   /**
    * File name for the user profiles
    */
   const FILE_USERPROFILES = 'userprofiles.csv';

   /**
     * Header fields for user profiles
     *
     *
     * @var array
     */
    const HEADER_USERPROFILES = [
      self::HEADER_SOURCEDID,
      self::HEADER_STATUS,
      self::HEADER_DATE_LAST_MODIFIED,
      self::HEADER_USER_SOURCEDID,
      self::HEADER_PROFILE_TYPE,
      self::HEADER_VENDOR_ID,
      self::HEADER_APPLICATION_ID,
      self::HEADER_DESCRIPTION,
      self::HEADER_CREDENTIAL_TYPE,
      self::HEADER_USERNAME,
      self::HEADER_PASSWORD
   ];

   /**
     * Required files and their corresponding headers.
     *
     * @var array
     */
    const REQUIRED_FILES = [
      self::FILE_ACADEMIC_SESSIONS => self::HEADER_ACADEMIC_SESSIONS,
      self::FILE_CLASSES => self::HEADER_CLASSES,
      self::FILE_ENROLLMENTS => self::HEADER_ENROLLMENTS,
      self::FILE_ORGS => self::HEADER_ORGS,
      self::FILE_USERS => self::HEADER_USERS,
      self::FILE_USERPROFILES => self::HEADER_USERPROFILES
  ];
}
