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
 * Class csv_client_const_helper.
 *
 * This class contains constants that are used throughout the OneRoster CSV client.
 *
 * @package    enrol_oneroster
 * @copyright  Gustavo Amorim De Almeida, Ruben Cooper, Josh Bateson, Brayden Porter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class csv_client_const_helper {
    /**
     * Individual header constants.
     * Represents expected fields in CSV files.
     */
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

    /**
     * Supported CSV file names that contain specific data sets.
     */
    const FILE_MANIFEST = 'manifest.csv';
    const FILE_ACADEMIC_SESSIONS = 'academicSessions.csv';
    const FILE_CLASSES = 'classes.csv';
    const FILE_ENROLLMENTS = 'enrollments.csv';
    const FILE_ORGS = 'orgs.csv';
    const FILE_USERS = 'users.csv';

    /**
     * Datatype constants used for validation.
     */
    const DATATYPE_NULL = 'null';
    const DATATYPE_GUID = 'guid';
    const DATATYPE_INT = 'int';
    const DATATYPE_DATETIME = 'datetime';
    const DATATYPE_DATE = 'date';
    const DATATYPE_YEAR = 'year';
    const DATATYPE_ENUM_STATUS = 'enum_status';
    const DATATYPE_ENUM_TYPE = 'enum_type';
    const DATATYPE_ARRAY_GUID = 'array_guid';
    const DATATYPE_ARRAY_GRADE = 'array_grade';
    const DATATYPE_GRADE = 'grade';
    const DATATYPE_STRING_EMAIL = 'string_email';
    const DATATYPE_ARRAY_USERIDS = 'array_userIds';
    const DATATYPE_ENUM_ROLE_USER = 'enum_role_user';
    const DATATYPE_ENUM_TYPE_ENROL = 'enum_type_enrol';
    const DATATYPE_ENUM_PRIMARY = 'enum_primary';
    const DATATYPE_ENUM_CLASS_TYPE = 'enum_class_type';
    const DATATYPE_ENUM_ORG_TYPE = 'enum_org_type';
    const DATATYPE_ARRAY_SUBJECTS = 'array_subjects';
    const DATATYPE_ARRAY_SUBJECT_CODES = 'array_subjectCodes';
    const DATATYPE_ARRAY_PERIODS = 'array_periods';
    const DATATYPE_PASSWORD = 'password';
    const DATATYPE_STRING = 'string';

    /**
     * Enum constants used for validation
     */
    const VALID_CLASS_TYPES = ['homeroom', 'scheduled'];
    const VALID_ROLES = ['administrator', 'proctor', 'student', 'teacher'];
    const VALID_PRIMARY_VALUES = ['true', 'false'];
    const VALID_ORG_TYPES = ['department', 'school', 'district', 'local', 'state', 'national'];
    const VALID_ROLES_USERS = [
        'administrator', 'aide', 'guardian', 'parent', 'proctor',
        'relative', 'student', 'teacher'
    ];

    /**
     * Valid grade codes from the Common Education Data Standards
     * Reference: https://ceds.ed.gov/CEDSElementDetails.aspx?TermId=7100.
     */
    const valid_grade_codes = [
        'IT', 'PR', 'PK', 'TK', 'KG', '01', '02', '03', '04', '05', '06',
        '07', '08', '09', '10', '11', '12', '13', 'PS', 'UG', 'Other'
    ];

    /**
     * Header constants for each file
     */
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

    /**
     * Required files and headers
     */
    const REQUIRED_FILES = [
        self::FILE_ACADEMIC_SESSIONS => self::HEADER_ACADEMIC_SESSIONS,
        self::FILE_CLASSES => self::HEADER_CLASSES,
        self::FILE_ENROLLMENTS => self::HEADER_ENROLLMENTS,
        self::FILE_ORGS => self::HEADER_ORGS,
        self::FILE_USERS => self::HEADER_USERS,
    ];
}
