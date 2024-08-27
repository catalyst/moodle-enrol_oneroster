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

namespace enrol_oneroster;

/**
 * Class csv_data_helper
 *
 * This class provides sample data for the CSV files that are used in the unit tests.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class csv_data_helper {
    /**
     * Returns the manifest data.
     *
     * @return array
     */
    public static function get_manifest_data() {
        return array(
            array(
                'propertyName' => "manifest.version",
                'value' => "1.0",
            ),
            array(
                'propertyName' => "oneroster.version",
                'value' => "1.1",
            ),
            array(
                'propertyName' => "file.academicSessions",
                'value' => "bulk",
            ),
            array(
                'propertyName' => "file.categories",
                'value' => "bulk",
            ),
            array(
                'propertyName' => "file.classes",
                'value' => "bulk",
            ),
            array(
                'propertyName' => "file.classResources",
                'value' => "absent",
            ),
            array(
                'propertyName' => "file.courses",
                'value' => "bulk",
            ),
            array(
                'propertyName' => "file.courseResources",
                'value' => "absent",
            ),
            array(
                'propertyName' => "file.demographics",
                'value' => "bulk",
            ),
            array(
                'propertyName' => "file.enrollments",
                'value' => "bulk",
            ),
            array(
                'propertyName' => "file.lineItems",
                'value' => "bulk",
            ),
            array(
                'propertyName' => "file.orgs",
                'value' => "bulk",
            ),
            array(
                'propertyName' => "file.resources",
                'value' => "absent",
            ),
            array(
                'propertyName' => "file.results",
                'value' => "bulk",
            ),
            array(
                'propertyName' => "file.users",
                'value' => "bulk",
            ),
            array(
                'propertyName' => "source.systemCode",
                'value' => "absent",
            ),
            array(
                'propertyName' => "source.systemName",
                'value' => "absent",
            ),
        );
    }

    /**
     * Returns the academic sessions data.
     *
     * @return array
     */
    public static function get_academicsessions_data() {
        return array(
            array(
                'sourcedId' => 'as-trm-222-1234', // GUID, Required
                'status' => null,                // Enumeration, Null for Bulk mode
                'dateLastModified' => null,      // DateTime, Null for Bulk mode
                'title' => '1st Semester',        // String, Required
                'type' => 'term',                 // Enumeration, Required
                'startDate' => '2022-09-01',      // Date, Required
                'endDate' => '2022-12-24',        // Date, Required
                'parentSourcedId' => 'as-syr-222-2023', // GUID, Optional
                'schoolYear' => '2023',           // Year, Required

            ),
            array(
                'sourcedId' => 'as-grp-222-2345', // GUID, Required
                'status' => null,                // Enumeration, Null for Bulk mode
                'dateLastModified' => null,      // DateTime, Null for Bulk mode
                'title' => '2st Quarter',         // String, Required
                'type' => 'gradingPeriod',        // Enumeration, Required
                'startDate' => '2022-10-01',      // Date, Required
                'endDate' => '2022-12-24',        // Date, Required
                'parentSourcedId' => 'as-trm-222-1234', // GUID, Optional
                'schoolYear' => '2023',           // Year, Required
            )
        );
    }

    /**
     * Returns the categories data.
     *
     * @return array
     */
    public static function get_categories_data() {
        return array(
            array(
                'sourcedId' => 'lic-222-543', // GUID, Required
                'status' => null,                // Enumeration, Null for Bulk mode
                'dateLastModified' => null,      // DateTime, Null for Bulk mode
                'title' => 'Homework',        // String, Required
            ),
            array(
                'sourcedId' => 'lic-222-544', // GUID, Required
                'status' => null,                // Enumeration, Null for Bulk mode
                'dateLastModified' => null,      // DateTime, Null for Bulk mode
                'title' => 'Project',         // String, Required
            )
        );
    }

    /**
     * Returns the classes data.
     *
     * @return array
     */
    public static function get_classes_data() {
        return array(
            array(
                'sourcedId' => 'cls-222-123456', // GUID, Required
                'status' => null,                // Enumeration, Null for Bulk mode
                'dateLastModified' => null,      // DateTime, Null for Bulk mode
                'title' => 'Introduction to Physics', // String, Required
                'grades' => array('10', '11'),   // List of Strings, Optional
                'courseSourcedId' => 'crs-222-2023-456-12345', // GUID Reference, Required
                'classCode' => 'Phys 100 - 1',   // String, Optional
                'classType' => 'Scheduled',      // Enumeration, Required
                'location' => 'Room 2-B',        // String, Optional
                'schoolSourcedId' => 'org-sch-222-456', // GUID Reference, Required
                'termSourcedIds' => array(       // List of GUID References, Required
                    'as-trm-222-1234'
                ),
                'subjects' => array('Science'),  // List of Strings, Optional
                'periods' => array('B'),         // List of Strings, Optional
            ),
            array(
                'sourcedId' => 'cls-222-123478', // GUID, Required
                'status' => null,                // Enumeration, Null for Bulk mode
                'dateLastModified' => null,      // DateTime, Null for Bulk mode
                'title' => 'History - 2',        // String, Required
                'grades' => array('09', '10', '11'), // List of Strings, Optional
                'courseSourcedId' => 'crs-222-2023-456-23456', // GUID Reference, Required
                'classCode' => '2',              // String, Optional
                'classType' => 'Scheduled',      // Enumeration, Required
                'location' => 'Room 18-C',       // String, Optional
                'schoolSourcedId' => 'org-sch-222-456', // GUID Reference, Required
                'termSourcedIds' => array(       // List of GUID References, Required
                    'as-trm-222-1234'
                ),
                'subjects' => array('History'),  // List of Strings, Optional
                'periods' => array('D'),         // List of Strings, Optional
            )
        );
    }

    /**
     * Returns the courses data.
     *
     * @return array
     */
    public static function get_courses_data() {
        return array(
            array(
                'sourcedId' => 'crs-222-456-12345', // GUID, Required
                'status' => null,                   // Enumeration, Null for Bulk mode
                'dateLastModified' => null,         // DateTime, Null for Bulk mode
                'schoolYearSourcedId' => 'as-syr-222-2023', // GUID Reference, Optional
                'title' => 'Introduction to Physics', // String, Required
                'courseCode' => 'Phys 100',         // String, Optional
                'grades' => array('09', '10'),      // List of Strings, Optional
                'orgSourcedId' => 'org-sch-222-456', // GUID Reference, Required
                'subjects' => array('Science'),     // List of Strings, Optional
                'subjectCodes' => null,             // List of Strings, Optional (null for now)
            ),
            array(
                'sourcedId' => 'crs-222-123654',    // GUID, Required
                'status' => null,                   // Enumeration, Null for Bulk mode
                'dateLastModified' => null,         // DateTime, Null for Bulk mode
                'schoolYearSourcedId' => 'as-syr-222-2023', // GUID Reference, Optional
                'title' => 'History',               // String, Required
                'courseCode' => '2',                // String, Optional
                'grades' => array('09', '10', '11'), // List of Strings, Optional
                'orgSourcedId' => 'org-sch-222-456', // GUID Reference, Required
                'subjects' => array('History'),     // List of Strings, Optional
                'subjectCodes' => null,             // List of Strings, Optional (null for now)
            )
        );
    }

    /**
     * Returns the demographics data.
     *
     * @return array
     */
    public static function get_demographics_data() {
        return array(
            array(
                'sourcedId' => 'usr-222-66778899',  // GUID Reference, Required
                'status' => null,                  // Enumeration, Null for Bulk mode
                'dateLastModified' => null,        // DateTime, Null for Bulk mode
                'birthDate' => '2006-07-26',       // Date, Optional
                'sex' => 'male',                   // Enumeration, Optional
                'americanIndianOrAlaskaNative' => 'false', // Enumeration, Optional
                'asian' => 'false',                // Enumeration, Optional
                'blackOrAfricanAmerican' => 'false', // Enumeration, Optional
                'nativeHawaiianOrOtherPacificIslander' => 'false', // Enumeration, Optional
                'white' => 'true',                 // Enumeration, Optional
                'demographicRaceTwoOrMoreRaces' => 'false', // Enumeration, Optional
                'hispanicOrLatinoEthnicity' => 'false', // Enumeration, Optional
                'cityOfBirth' => 'Concord',        // String, Optional
                'countryOfBirthCode' => null,      // String, Optional
                'stateOfBirthAbbreviation' => null, // String, Optional
                'publicSchoolResidenceStatus' => null, // String, Optional
            ),
            array(
                'sourcedId' => 'usr-222-66778896',  // GUID Reference, Required
                'status' => null,                  // Enumeration, Null for Bulk mode
                'dateLastModified' => null,        // DateTime, Null for Bulk mode
                'birthDate' => '2006-08-01',       // Date, Optional
                'sex' => 'female',                 // Enumeration, Optional
                'americanIndianOrAlaskaNative' => 'false', // Enumeration, Optional
                'asian' => 'true',                 // Enumeration, Optional
                'blackOrAfricanAmerican' => 'false', // Enumeration, Optional
                'nativeHawaiianOrOtherPacificIslander' => 'false', // Enumeration, Optional
                'white' => 'true',                 // Enumeration, Optional
                'demographicRaceTwoOrMoreRaces' => 'true', // Enumeration, Optional
                'hispanicOrLatinoEthnicity' => 'false', // Enumeration, Optional
                'cityOfBirth' => 'Boston',         // String, Optional
                'countryOfBirthCode' => null,      // String, Optional
                'stateOfBirthAbbreviation' => null, // String, Optional
                'publicSchoolResidenceStatus' => null, // String, Optional
            )
        );
    }

    /**
     * Returns the enrollments data.
     *
     * @return array
     */
    public static function get_enrollments_data() {
        return array(
            array(
                'sourcedId' => 'enr-s-222-12345-987654', // GUID, Required
                'status' => null,                        // Enumeration, Null for Bulk mode
                'dateLastModified' => null,              // DateTime, Null for Bulk mode
                'classSourcedId' => 'cls-222-12345',     // GUID Reference, Required
                'schoolSourcedId' => 'org-sch-222-456',  // GUID Reference, Required
                'userSourcedId' => 'usr-222-987654',     // GUID Reference, Required
                'role' => 'student',                     // Enumeration, Required
                'primary' => 'false',                    // Enumeration, Optional
                'beginDate' => '2022-03-15T01:30:00.0000000+00:00', // Date, Optional
                'endDate' => '2022-06-15T01:30:00.0000000+00:00'    // Date, Optional
            ),
            array(
                'sourcedId' => 'enr-t-222-12345-123456', // GUID, Required
                'status' => null,                        // Enumeration, Null for Bulk mode
                'dateLastModified' => null,              // DateTime, Null for Bulk mode
                'classSourcedId' => 'cls-222-12345',     // GUID Reference, Required
                'schoolSourcedId' => 'org-sch-222-456',  // GUID Reference, Required
                'userSourcedId' => 'usr-222-123456',     // GUID Reference, Required
                'role' => 'teacher',                     // Enumeration, Required
                'primary' => 'true',                     // Enumeration, Optional
                'beginDate' => '2022-03-15T01:30:00.0000000+00:00', // Date, Optional
                'endDate' => '2022-06-15T01:30:00.0000000+00:00'    // Date, Optional
            )
        );
    }

    /**
     * Returns the line items data.
     *
     * @return array
     */
    public static function get_line_items_data() {
        return array(
            array(
                'sourcedId' => 'external-lineitem-sourcedId-1', // GUID, Required
                'status' => null,                               // Enumeration, Null for Bulk mode
                'dateLastModified' => null,                     // DateTime, Null for Bulk mode
                'title' => 'Assignment Title 1',                // String, Required
                'description' => 'Assignment Description for Assignment 1', // String, Optional
                'assignDate' => '2022-09-26T01:30:00.0000000+00:00', // Date, Required
                'dueDate' => '2022-09-29T01:30:00.0000000+00:00',    // Date, Required
                'classSourcedId' => 'cls-222-12345',            // GUID Reference, Required
                'categorySourcedId' => 'lic-222-345',           // GUID Reference, Required
                'gradingPeriodSourcedId' => 'as-grp-222-6789',  // GUID Reference, Required
                'resultValueMin' => 0.0,                        // Float, Required
                'resultValueMax' => 10.0                        // Float, Required
            ),
            array(
                'sourcedId' => 'external-lineitem-sourcedId-2', // GUID, Required
                'status' => null,                               // Enumeration, Null for Bulk mode
                'dateLastModified' => null,                     // DateTime, Null for Bulk mode
                'title' => 'Assignment Title 2',                // String, Required
                'description' => 'Assignment Description for Assignment 2', // String, Optional
                'assignDate' => '2022-09-26T01:30:00.0000000+00:00', // Date, Required
                'dueDate' => '2022-09-29T01:30:00.0000000+00:00',    // Date, Required
                'classSourcedId' => 'cls-222-12345',            // GUID Reference, Required
                'categorySourcedId' => 'lic-222-345',           // GUID Reference, Required
                'gradingPeriodSourcedId' => 'as-grp-222-6789',  // GUID Reference, Required
                'resultValueMin' => 0.0,                        // Float, Required
                'resultValueMax' => 10.0                        // Float, Required
            )
        );
    }

    /**
     * Returns the orgs data.
     *
     * @return array
     */
    public static function get_orgs_data() {
        return array(
            array(
                'sourcedId' => 'org-sch-222-3456',    // GUID, Required
                'status' => null,                    // Enumeration, Null for Bulk mode
                'dateLastModified' => null,          // DateTime, Null for Bulk mode
                'name' => 'Upper School',            // String, Required
                'type' => 'school',                  // Enumeration, Required
                'identifier' => 'US',                // String, Optional
                'parentSourcedId' => 'org-dpt-222-456',           // GUID Reference, Optional
            ),
            array(
                'sourcedId' => 'org_sch-222-7654',   // GUID, Required
                'status' => null,                    // Enumeration, Null for Bulk mode
                'dateLastModified' => null,          // DateTime, Null forBulk mode
                'name' => 'US History',              // String, Required
                'type' => 'department',              // Enumeration, Required
                'identifier' => 'US History',        // String, Optional
                'parentSourcedId' => 'org-sch-222-3456', // GUID Reference, Optional
            )
        );
    }

    /**
     * Returns the results data.
     *
     * @return array
     */
    public static function get_results_data() {
        return array(
            array(
                'sourcedId' => 'res-12345',               // GUID, Required
                'status' => null,                         // Enumeration, Null for Bulk mode
                'dateLastModified' => null,               // DateTime, Null for Bulk mode
                'lineItemSourcedId' => 'lineitem-12345',  // GUID Reference, Required
                'studentSourcedId' => 'usr-222-12345678', // GUID Reference, Required
                'scoreStatus' => 'fully graded',          // Enumeration, Required
                'score' => 88.0,                          // Float, Required
                'scoreDate' => '2022-03-15T01:30:00.0000000+00:00', // Date, Required
                'comment' => 'Result comment'             // String, Optional
            ),
            array(
                'sourcedId' => 'res-12346',               // GUID, Required
                'status' => null,                         // Enumeration, Null for Bulk mode
                'dateLastModified' => null,               // DateTime, Null for Bulk mode
                'lineItemSourcedId' => 'lineitem-12346',  // GUID Reference, Required
                'studentSourcedId' => 'usr-222-12345677', // GUID Reference, Required
                'scoreStatus' => 'fully graded',          // Enumeration, Required
                'score' => 88.0,                          // Float, Required
                'scoreDate' => '2022-03-15T01:30:00.0000000+00:00', // Date, Required
                'comment' => 'Result comment'             // String, Optional
            )
        );
    }

    /**
     * Returns the users data.
     *
     * @return array
     */
    public static function get_users_data() {
        return array(
            array(
                'sourcedId' => 'usr-222-123456',         // GUID, Required
                'status' => null,                        // Enumeration, Null for Bulk mode
                'dateLastModified' => null,              // DateTime, Null for Bulk mode
                'enabledUser' => 'true',                 // Enumeration, Required
                'orgSourcedIds' => array('org-sch-222-456'), // List of GUID References, Required
                'role' => 'teacher',                     // Enumeration, Required
                'username' => 'john.doe@myschool.com',   // String, Required
                'userIds' => null,                       // List of Strings, Optional
                'givenName' => 'John',                   // String, Required
                'familyName' => 'Doe',                   // String, Required
                'middleName' => 'Michael',               // String, Optional
                'identifier' => '123456',                // String, Optional
                'email' => 'john.doe@myschool.com',      // String, Optional
                'sms' => '6037778888',                   // String, Optional
                'phone' => '6032221111',                 // String, Optional
                'agentSourcedIds' => null,               // List of GUID References, Optional
                'grades' => null,                        // String, Optional (for students)
                'password' => null,                      // String, Optional
            ),
            array(
                'sourcedId' => 'usr-222-66778899',       // GUID, Required
                'status' => null,                        // Enumeration, Null for Bulk mode
                'dateLastModified' => null,              // DateTime, Null for Bulk mode
                'enabledUser' => 'true',                 // Enumeration, Required
                'orgSourcedIds' => array('org-sch-222-456'), // List of GUID References, Required
                'role' => 'student',                    // Enumeration, Required
                'username' => 'mary.jones@myschool.com', // String, Required
                'userIds' => array(                     // List of Strings, Optional
                    array('type' => 'stateId', 'identifier' => 'NH-345-678'),
                    array('type' => 'StudentId', 'identifier' => '100021')
                ),
                'givenName' => 'Mary',                   // String, Required
                'familyName' => 'Jones',                 // String, Required
                'middleName' => 'Jane',                  // String, Optional
                'identifier' => '66778899',              // String, Optional
                'email' => 'mary.jones@myschool.com',    // String, Optional
                'sms' => '6031234567',                   // String, Optional
                'phone' => '6031234567',                 // String, Optional
                'agentSourcedIds' => array('usr-222-66778900'), // List of GUID References, Optional
                'grades' => '12',                        // String, Optional
                'password' => null,                      // String, Optional
            ),
            array(
                'sourcedId' => 'usr-222-66778900',       // GUID, Required
                'status' => null,                        // Enumeration, Null for Bulk mode
                'dateLastModified' => null,              // DateTime, Null for Bulk mode
                'enabledUser' => 'true',                 // Enumeration, Required
                'orgSourcedIds' => array('org-sch-222-456'), // List of GUID References, Required
                'role' => 'parent',                     // Enumeration, Required
                'username' => 'thomas.jones@myschool.com', // String, Required
                'userIds' => null,                       // List of Strings, Optional
                'givenName' => 'Thomas',                 // String, Required
                'familyName' => 'Jones',                 // String, Required
                'middleName' => 'Paul',                  // String, Optional
                'identifier' => '66778900',              // String, Optional
                'email' => 'thomas.jones@testemail.com', // String, Optional
                'sms' => '6039876543',                   // String, Optional
                'phone' => '6039876543',                 // String, Optional
                'agentSourcedIds' => array('usr-222-66778899'), // List of GUID References, Optional
                'grades' => null,                        // String, Optional
                'password' => null,                      // String, Optional
            )
        );
    }
}