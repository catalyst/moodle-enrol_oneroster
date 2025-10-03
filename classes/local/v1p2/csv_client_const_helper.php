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
     * Header field for the user sourced ID.
     */
    const HEADER_USERPROFILE_SOURCEDID = 'userProfileSourcedId';

    /**
     * Header field for the org sourced ID.
     */
    const HEADER_ORG_SOURCEDID = 'orgSourcedId';

    /**
     * Header field for the role type.
     */
    const HEADER_ROLE_TYPE = 'roleType';

    /**
     * File name for roles.
     */
    const FILE_ROLES = 'roles.csv';

    /**
     * Datatype constant for an role type enumeration.
     */
    const DATATYPE_ENUM_ROLE_TYPE = 'enum_role_type';

    /**
     * Datatype constant for an legacy role type enumeration.
     */
    const DATATYPE_ENUM_ROLE_ROLE = 'enum_role';

    /**
     * Valid roles for enrollments.
     *
     * @var array
     */
    const VALID_ENROLLMENT_ROLES = ['administrator', 'proctor', 'student', 'teacher'];

    /**
     * Valid roles for Role.
     *
     * @var array
     */
    const VALID_ROLE_ROLES = ['aide' , 'counselor' , 'districtAdministrator' , 'guardian' ,
                            'parent' , 'principal' , 'proctor' , 'relative' , 'siteAdministrator'
                            , 'student' , 'systemAdministrator' , 'teacher'];

    /**
     * Valid role types.
     *
     * @var array
     */
    const VALID_ROLE_TYPE =[ 'primary', 'secondary'];


    /**
   * Id field for the vendor id.
   */
   const HEADER_VENDOR_ID = 'vendorId';

   /**
    * Id field for the application id.
    */
   const HEADER_APPLICATION_ID = 'applicationId';

   /**
    * Type field for the user profile.
    */
   const HEADER_PROFILE_TYPE = 'profileType';

   /**
    * Credential type field for the credential type.
    */
   const HEADER_CREDENTIAL_TYPE = 'credentialType';

   /**
    * The description field for the user profile.
    */
   const HEADER_DESCRIPTION = 'description';

   /**
    * File name for the user profiles.
    */
   const FILE_USERPROFILES = 'userprofiles.csv';

   /**
    * User master identifier field for users.
    */
   const HEADER_MASTER_IDENTIFIER = 'userMasterIdentifier';

   /**
    *  Preferred given name field for users.
    */
   const HEADER_PREFERRED_GIVEN_NAME = 'preferredGiveName';
   
   /**
    *  Preferred middle name field for users.
    */
   const HEADER_PREFERRED_MIDDLE_NAME = 'preferredMiddeName';

   /**
    *  Preferred family name field for users.
    */
   const HEADER_PREFERRED_FAMILY_NAME = 'preferredFamiyName';

   /**
    *  Primary sourced ID field for users.
    */
   const HEADER_PRIMARY_ORG_SOURCED_ID = 'primaryOrgSourcedId';

   /**
    *  Pronouns field for users.
    */
   const HEADER_PRONOUNS = 'pronouns';
   
   /**
     * File name for courses.
     */
   const FILE_COURSES = 'courses.csv';

   /**
    *  School year sourced ID field for courses.
    */
   const HEADER_SCHOOL_YEAR_SOURCED_ID = 'schoolYearSourcedId';
   
   /**
    *  Course Ccode field for courses.
    */
   const HEADER_COURSE_CODE = 'courseCode';

    /**
     * Header fields for courses
     *
     *
     * @var array
     */
    const HEADER_COURSES = [
      self::HEADER_SOURCEDID,
      self::HEADER_STATUS,
      self::HEADER_DATE_LAST_MODIFIED,
      self::HEADER_SCHOOL_YEAR_SOURCED_ID,
      self::HEADER_TITLE,
      self::HEADER_COURSE_CODE,
      self::HEADER_GRADES,
      self::HEADER_ORG_SOURCEDID,
      self::HEADER_SUBJECTS,
      self::HEADER_SUBJECT_CODES,

   ];

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
     * Header fields for user profiles
     *
     *
     * @var array
     */
    const HEADER_USERS = [
      self::HEADER_SOURCEDID,
      self::HEADER_STATUS,
      self::HEADER_DATE_LAST_MODIFIED,
      self::HEADER_ENABLED_USER,
      self::HEADER_USERNAME,
      self::HEADER_USERIDS,
      self::HEADER_GIVEN_NAME,
      self::HEADER_FAMILY_NAME,
      self::HEADER_MIDDLE_NAME,
      self::HEADER_IDENTIFIER,
      self::HEADER_EMAIL,
      self::HEADER_SMS,
      self::HEADER_PHONE,
      self::HEADER_AGENT_SOURCEDIDS,
      self::HEADER_GRADES,
      self::HEADER_PASSWORD,
      self::HEADER_MASTER_IDENTIFIER,
      self::HEADER_PREFERRED_GIVEN_NAME,
      self::HEADER_PREFERRED_MIDDLE_NAME,
      self::HEADER_PREFERRED_FAMILY_NAME,
      self::HEADER_PRIMARY_ORG_SOURCED_ID,
      self::HEADER_PRONOUNS
   ];

   /**
     * Header fields for the roles file.
     *
     * @var array
     */
    const HEADER_ROLES = [
        self::HEADER_SOURCEDID,
        self::HEADER_STATUS,
        self::HEADER_DATE_LAST_MODIFIED,
        self::HEADER_USER_SOURCEDID,
        self::HEADER_ROLE_TYPE,
        self::HEADER_ROLE,
        self::HEADER_BEGIN_DATE,
        self::HEADER_END_DATE,
        self::HEADER_ORG_SOURCEDID,
        self::HEADER_USERPROFILE_SOURCEDID
    ];

   /**
     * Required files and their corresponding headers.
     *
     * @var array
     */
    const REQUIRED_FILES = [
        self::FILE_ACADEMIC_SESSIONS => self::HEADER_ACADEMIC_SESSIONS,
        self::FILE_CLASSES => self::HEADER_CLASSES,
        self::FILE_COURSES => self::HEADER_COURSES,
        self::FILE_ENROLLMENTS => self::HEADER_ENROLLMENTS,
        self::FILE_ORGS => self::HEADER_ORGS,
        self::FILE_USERS => self::HEADER_USERS,
        self::FILE_USERPROFILES => self::HEADER_USERPROFILES,
        self::FILE_ROLES => self:: HEADER_ROLES,
    ];
}
