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

/**
 * This class defines the expected headers for each CSV file.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster;

class expected_csv_headers {
    
    // Define the expected headers for each CSV file
    const HEADER_MANIFEST =['manifest.version', 'oneroster.version','file.academicSessions','file.categories','file.classes','file.classResources','file.courses','file.courseResources','file.demographics','file.enrollments','file.lineItems','file.orgs','file.resources','file.results','file.users'];
    const HEADER_ACADEMIC_SESSIONS = ['sourcedId', 'status', 'dateLastModified', 'title', 'type', 'startDate', 'endDate', 'parentSourcedId', 'schoolYear'];
    const HEADER_CATEGORIES = ['sourcedId', 'status', 'dateLastModified', 'title'];
    const HEADER_CLASSES = ['sourcedId', 'status', 'dateLastModified', 'title', 'grades', 'courseSourcedId', 'classCode', 'classType', 'location', 'schoolSourcedId', 'termSourcedIds', 'subjects', 'subjectCodes', 'periods'];
    const HEADER_CLASS_RESOURCES = ['sourcedId', 'status', 'dateLastModified', 'title', 'classSourcedId', 'resourceSourcedId'];
    const HEADER_COURSES = ['sourcedId', 'status', 'dateLastModified', 'schoolYearSourcedId', 'title', 'courseCode', 'grades', 'orgSourcedId', 'subjects', 'subjectCodes'];
    const HEADER_COURSE_RESOURCES = ['sourcedId', 'status', 'dateLastModified', 'title', 'courseSourcedId', 'resourceSourcedId'];
    const HEADER_DEMOGRAPHICS = ['sourcedId', 'status', 'dateLastModified', 'birthDate', 'sex', 'americanIndianOrAlaskaNative', 'asian', 'blackOrAfricanAmerican', 'nativeHawaiianOrOtherPacificIslander', 'white', 'demographicRaceTwoOrMoreRaces', 'hispanicOrLatinoEthnicity', 'countryOfBirthCode', 'stateOfBirthAbbreviation', 'cityOfBirth', 'publicSchoolResidenceStatus'];
    const HEADER_ENROLLMENTS = ['sourcedId', 'status', 'dateLastModified', 'classSourcedId', 'schoolSourcedId', 'userSourcedId', 'role', 'primary', 'beginDate', 'endDate'];
    const HEADER_LINEITEMS = ['sourcedId', 'status', 'dateLastModified', 'title', 'description', 'assignDate', 'dueDate', 'classSourcedId', 'categorySourcedId', 'gradingPeriodSourcedId', 'resultValueMin', 'resultValueMax'];
    const HEADER_ORGS = ['sourcedId', 'status', 'dateLastModified', 'name', 'type', 'identifier', 'parentSourcedId'];
    const HEADER_USERS = ['sourcedId', 'status', 'dateLastModified', 'enabledUser', 'orgSourcedIds', 'role', 'username', 'userIds', 'givenName', 'familyName', 'middleName', 'identifier', 'email', 'sms', 'phone', 'agentSourcedIds', 'grades', 'password'];
    const HEADER_RESOURCES = ['sourcedId', 'status', 'dateLastModified', 'vendorResourceId', 'title', 'roles', 'importance', 'vendorId', 'applicationId'];
    const HEADER_RESULTS = ['sourcedId', 'status', 'dateLastModified', 'lineItemSourcedId', 'studentSourcedId', 'scoreStatus', 'score', 'resultDate', 'comment'];

    // Define the required files and their headers
    const REQUIRED_FILES = [
        'manifest.csv' => self::HEADER_MANIFEST,
        'academicSessions.csv' => self::HEADER_ACADEMIC_SESSIONS,
        'categories.csv' => self::HEADER_CATEGORIES,
        'classes.csv' => self::HEADER_CLASSES,
        'classResources.csv' => self::HEADER_CLASS_RESOURCES,
        'courses.csv' => self::HEADER_COURSES,
        'courseResources.csv' => self::HEADER_COURSE_RESOURCES,
        'demographics.csv' => self::HEADER_DEMOGRAPHICS,
        'enrollments.csv' => self::HEADER_ENROLLMENTS,
        'lineItems.csv' => self::HEADER_LINEITEMS,
        'orgs.csv' => self::HEADER_ORGS,
        'users.csv' => self::HEADER_USERS,
        'resources.csv' => self::HEADER_RESOURCES,
        'results.csv' => self::HEADER_RESULTS,
    ];
    
    // Get the header for a specific file
    public static function getHeader($file_name) {
        switch ($file_name) {
            case 'manifest.csv':
                return self::HEADER_MANIFEST;
            case 'academicSessions.csv':
                return self::HEADER_ACADEMIC_SESSIONS;
            case 'categories.csv':
                return self::HEADER_CATEGORIES;
            case 'classes.csv':
                return self::HEADER_CLASSES;
            case 'classResources.csv':
                return self::HEADER_CLASS_RESOURCES;
            case 'courses.csv':
                return self::HEADER_COURSES;
            case 'courseResources.csv':
                return self::HEADER_COURSE_RESOURCES;
            case 'demographics.csv':
                return self::HEADER_DEMOGRAPHICS;
            case 'enrollments.csv':
                return self::HEADER_ENROLLMENTS;
            case 'lineItems.csv':
                return self::HEADER_LINEITEMS;
            case 'orgs.csv':
                return self::HEADER_ORGS;
            case 'users.csv':
                return self::HEADER_USERS;
            case 'resources.csv':
                return self::HEADER_RESOURCES;
            case 'results.csv':
                return self::HEADER_RESULTS;
            default:
                return [];
        }
    }
}
