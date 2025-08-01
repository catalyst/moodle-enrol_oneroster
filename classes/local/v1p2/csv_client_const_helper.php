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
 * This class contains constants that are used throughout the OneRoster CSV client.
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
        self::FILE_ENROLLMENTS => self::HEADER_ENROLLMENTS,
        self::FILE_ORGS => self::HEADER_ORGS,
        self::FILE_USERS => self::HEADER_USERS,
        self::FILE_ROLES => self:: HEADER_ROLES,
    ];
}
