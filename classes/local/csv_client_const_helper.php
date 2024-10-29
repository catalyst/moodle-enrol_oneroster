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
     * Header field for the sourced ID.
     */
    const HEADER_SOURCEDID = 'sourcedId';

    /**
     * Header field for the status.
     */
    const HEADER_STATUS = 'status';

    /**
     * Header field for the date last modified.
     */
    const HEADER_DATE_LAST_MODIFIED = 'dateLastModified';

    /**
     * Header field for the title.
     */
    const HEADER_TITLE = 'title';

    /**
     * Header field for the type.
     */
    const HEADER_TYPE = 'type';

    /**
     * Header field for the start date.
     */
    const HEADER_START_DATE = 'startDate';

    /**
     * Header field for the end date.
     */
    const HEADER_END_DATE = 'endDate';

    /**
     * Header field for the parent sourced ID.
     */
    const HEADER_PARENT_SOURCEDID = 'parentSourcedId';

    /**
     * Header field for the school year.
     */
    const HEADER_SCHOOL_YEAR = 'schoolYear';

    /**
     * Header field for grades.
     */
    const HEADER_GRADES = 'grades';

    /**
     * Header field for the course sourced ID.
     */
    const HEADER_COURSE_SOURCEDID = 'courseSourcedId';

    /**
     * Header field for the class code.
     */
    const HEADER_CLASS_CODE = 'classCode';

    /**
     * Header field for the class type.
     */
    const HEADER_CLASS_TYPE = 'classType';

    /**
     * Header field for the location.
     */
    const HEADER_LOCATION = 'location';

    /**
     * Header field for the school sourced ID.
     */
    const HEADER_SCHOOL_SOURCEDID = 'schoolSourcedId';

    /**
     * Header field for term sourced IDs.
     */
    const HEADER_TERM_SOURCEDIDS = 'termSourcedIds';

    /**
     * Header field for subjects.
     */
    const HEADER_SUBJECTS = 'subjects';

    /**
     * Header field for subject codes.
     */
    const HEADER_SUBJECT_CODES = 'subjectCodes';

    /**
     * Header field for periods.
     */
    const HEADER_PERIODS = 'periods';

    /**
     * Header field for the class sourced ID.
     */
    const HEADER_CLASS_SOURCEDID = 'classSourcedId';

    /**
     * Header field for the user sourced ID.
     */
    const HEADER_USER_SOURCEDID = 'userSourcedId';

    /**
     * Header field for the role.
     */
    const HEADER_ROLE = 'role';

    /**
     * Header field for the primary indicator.
     */
    const HEADER_PRIMARY = 'primary';

    /**
     * Header field for the begin date.
     */
    const HEADER_BEGIN_DATE = 'beginDate';

    /**
     * Header field for the end date.
     */
    const HEADER_END_DATE_ENROLLMENT = 'endDate';

    /**
     * Header field for the identifier.
     */
    const HEADER_IDENTIFIER = 'identifier';

    /**
     * Header field for the name.
     */
    const HEADER_NAME = 'name';

    /**
     * Header field indicating if the user is enabled.
     */
    const HEADER_ENABLED_USER = 'enabledUser';

    /**
     * Header field for organization sourced IDs.
     */
    const HEADER_ORG_SOURCEDIDS = 'orgSourcedIds';

    /**
     * Header field for the username.
     */
    const HEADER_USERNAME = 'username';

    /**
     * Header field for user IDs.
     */
    const HEADER_USERIDS = 'userIds';

    /**
     * Header field for the given name.
     */
    const HEADER_GIVEN_NAME = 'givenName';

    /**
     * Header field for the family name.
     */
    const HEADER_FAMILY_NAME = 'familyName';

    /**
     * Header field for the middle name.
     */
    const HEADER_MIDDLE_NAME = 'middleName';

    /**
     * Header field for the email.
     */
    const HEADER_EMAIL = 'email';

    /**
     * Header field for SMS number.
     */
    const HEADER_SMS = 'sms';

    /**
     * Header field for the phone number.
     */
    const HEADER_PHONE = 'phone';

    /**
     * Header field for agent sourced IDs.
     */
    const HEADER_AGENT_SOURCEDIDS = 'agentSourcedIds';

    /**
     * Header field for the password.
     */
    const HEADER_PASSWORD = 'password';

    // ... Continue documenting other constants in the same manner.

    /**
     * File name for the manifest.
     */
    const FILE_MANIFEST = 'manifest.csv';

    /**
     * File name for academic sessions.
     */
    const FILE_ACADEMIC_SESSIONS = 'academicSessions.csv';

    /**
     * File name for classes.
     */
    const FILE_CLASSES = 'classes.csv';

    /**
     * File name for enrollments.
     */
    const FILE_ENROLLMENTS = 'enrollments.csv';

    /**
     * File name for organizations.
     */
    const FILE_ORGS = 'orgs.csv';

    /**
     * File name for users.
     */
    const FILE_USERS = 'users.csv';

    // Datatype constants used for validation.

    /**
     * Datatype constant for a null value.
     */
    const DATATYPE_NULL = 'null';

    /**
     * Datatype constant for a GUID.
     */
    const DATATYPE_GUID = 'guid';

    /**
     * Datatype constant for an integer.
     */
    const DATATYPE_INT = 'int';

    /**
     * Datatype constant for a datetime.
     */
    const DATATYPE_DATETIME = 'datetime';

    /**
     * Datatype constant for a date.
     */
    const DATATYPE_DATE = 'date';

    /**
     * Datatype constant for a year.
     */
    const DATATYPE_YEAR = 'year';

    /**
     * Datatype constant for a status enumeration.
     */
    const DATATYPE_ENUM_STATUS = 'enum_status';

    /**
     * Datatype constant for a type enumeration.
     */
    const DATATYPE_ENUM_TYPE = 'enum_type';

    /**
     * Datatype constant for an array of GUIDs.
     */
    const DATATYPE_ARRAY_GUID = 'array_guid';

    /**
     * Datatype constant for an array of grades.
     */
    const DATATYPE_ARRAY_GRADE = 'array_grade';

    /**
     * Datatype constant for a grade.
     */
    const DATATYPE_GRADE = 'grade';

    /**
     * Datatype constant for an email string.
     */
    const DATATYPE_STRING_EMAIL = 'string_email';

    /**
     * Datatype constant for an array of user IDs.
     */
    const DATATYPE_ARRAY_USERIDS = 'array_userIds';

    /**
     * Datatype constant for a user role enumeration.
     */
    const DATATYPE_ENUM_ROLE_USER = 'enum_role_user';

    /**
     * Datatype constant for an enrollment type enumeration.
     */
    const DATATYPE_ENUM_TYPE_ENROL = 'enum_type_enrol';

    /**
     * Datatype constant for a primary enumeration.
     */
    const DATATYPE_ENUM_PRIMARY = 'enum_primary';

    /**
     * Datatype constant for a class type enumeration.
     */
    const DATATYPE_ENUM_CLASS_TYPE = 'enum_class_type';

    /**
     * Datatype constant for an organization type enumeration.
     */
    const DATATYPE_ENUM_ORG_TYPE = 'enum_org_type';

    /**
     * Datatype constant for an array of subjects.
     */
    const DATATYPE_ARRAY_SUBJECTS = 'array_subjects';

    /**
     * Datatype constant for an array of subject codes.
     */
    const DATATYPE_ARRAY_SUBJECT_CODES = 'array_subjectCodes';

    /**
     * Datatype constant for an array of periods.
     */
    const DATATYPE_ARRAY_PERIODS = 'array_periods';

    /**
     * Datatype constant for a password.
     */
    const DATATYPE_PASSWORD = 'password';

    /**
     * Datatype constant for a string.
     */
    const DATATYPE_STRING = 'string';

    // Enum constants used for validation.

    /**
     * Valid class types.
     *
     * @var array
     */
    const VALID_CLASS_TYPES = ['homeroom', 'scheduled'];

    /**
     * Valid roles.
     *
     * @var array
     */
    const VALID_ROLES = ['administrator', 'proctor', 'student', 'teacher'];

    /**
     * Valid primary values.
     *
     * @var array
     */
    const VALID_PRIMARY_VALUES = ['true', 'false'];

    /**
     * Valid organization types.
     *
     * @var array
     */
    const VALID_ORG_TYPES = ['department', 'school', 'district', 'local', 'state', 'national'];

    /**
     * Valid user roles.
     *
     * @var array
     */
    const VALID_ROLES_USERS = [
        'administrator', 'aide', 'guardian', 'parent', 'proctor',
        'relative', 'student', 'teacher'
    ];

    /**
     * Valid grade codes from the Common Education Data Standards.
     * Reference: https://ceds.ed.gov/CEDSElementDetails.aspx?TermId=7100.
     *
     * @var array
     */
    const VALID_GRADE_CODES = [
        'IT', 'PR', 'PK', 'TK', 'KG', '01', '02', '03', '04', '05', '06',
        '07', '08', '09', '10', '11', '12', '13', 'PS', 'UG', 'Other'
    ];

    // Header constants for each file.

    /**
     * Header fields for the academic sessions file.
     *
     * @var array
     */
    const HEADER_ACADEMIC_SESSIONS = [
        self::HEADER_SOURCEDID,
        self::HEADER_STATUS,
        self::HEADER_DATE_LAST_MODIFIED,
        self::HEADER_TITLE,
        self::HEADER_TYPE,
        self::HEADER_START_DATE,
        self::HEADER_END_DATE,
        self::HEADER_PARENT_SOURCEDID,
        self::HEADER_SCHOOL_YEAR
    ];

    /**
     * Header fields for the classes file.
     *
     * @var array
     */
    const HEADER_CLASSES = [
        self::HEADER_SOURCEDID,
        self::HEADER_STATUS,
        self::HEADER_DATE_LAST_MODIFIED,
        self::HEADER_TITLE,
        self::HEADER_GRADES,
        self::HEADER_COURSE_SOURCEDID,
        self::HEADER_CLASS_CODE,
        self::HEADER_CLASS_TYPE,
        self::HEADER_LOCATION,
        self::HEADER_SCHOOL_SOURCEDID,
        self::HEADER_TERM_SOURCEDIDS,
        self::HEADER_SUBJECTS,
        self::HEADER_SUBJECT_CODES,
        self::HEADER_PERIODS
    ];

    /**
     * Header fields for the enrollments file.
     *
     * @var array
     */
    const HEADER_ENROLLMENTS = [
        self::HEADER_SOURCEDID,
        self::HEADER_STATUS,
        self::HEADER_DATE_LAST_MODIFIED,
        self::HEADER_CLASS_SOURCEDID,
        self::HEADER_SCHOOL_SOURCEDID,
        self::HEADER_USER_SOURCEDID,
        self::HEADER_ROLE,
        self::HEADER_PRIMARY,
        self::HEADER_BEGIN_DATE,
        self::HEADER_END_DATE_ENROLLMENT
    ];

    /**
     * Header fields for the organizations file.
     *
     * @var array
     */
    const HEADER_ORGS = [
        self::HEADER_SOURCEDID,
        self::HEADER_STATUS,
        self::HEADER_DATE_LAST_MODIFIED,
        self::HEADER_NAME,
        self::HEADER_TYPE,
        self::HEADER_IDENTIFIER,
        self::HEADER_PARENT_SOURCEDID
    ];

    /**
     * Header fields for the users file.
     *
     * @var array
     */
    const HEADER_USERS = [
        self::HEADER_SOURCEDID,
        self::HEADER_STATUS,
        self::HEADER_DATE_LAST_MODIFIED,
        self::HEADER_ENABLED_USER,
        self::HEADER_ORG_SOURCEDIDS,
        self::HEADER_ROLE,
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
    ];
}
