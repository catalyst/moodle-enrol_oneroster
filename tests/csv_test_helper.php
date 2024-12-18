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
use \enrol_oneroster\local\csv_client_const_helper;

/**
 * Helper class for tests that involve CSV files.
 * @copyright  Gustavo Amorim De Almeida, Ruben Cooper, Josh Bateson, Brayden Porter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package    enrol_oneroster
 */
class csv_test_helper {
    /**
     * Creates a CSV file with the given content.
     *
     * @param string $filepath The path where the CSV file will be created.
     * @param array $content An array of arrays representing CSV rows.
     */
    public static function createcsvfiles(string $filepath, array $content): void {
        $handle = fopen($filepath, 'w');
        foreach ($content as $line) {
            fputcsv($handle, $line);
        }
        fclose($handle);
    }

    /**
     * Sets up the test environment by creating necessary CSV files.
     *
     * @param string $testdir The directory where the CSV files will be created.
     */
    public static function setupcsvfiles(string $testdir): void {
        // Creating manifest.csv.
        $manifestcontent = [
            ['propertyName', 'value'],
            ['file.academicSessions', 'bulk'],
            ['file.classes', 'bulk'],
            ['file.enrollments', 'bulk'],
            ['file.orgs', 'bulk'],
            ['file.users', 'bulk'],
        ];
        self::createcsvfiles($testdir . DIRECTORY_SEPARATOR . 'manifest.csv', $manifestcontent);

        // Creating academicSessions.csv.
        $academicsessionscontent = [
            csv_client_const_helper::HEADER_ACADEMIC_SESSIONS,
            [
                'as-trm-222-1234', 'active', '2023-05-01T18:25:43.511Z', 'Session Title',
                'term', '2022-09-01', '2022-12-24', 'as-syr-222-2023', '2023'
            ],
            [
                'as-grp-222-2345', '', '', 'Session Title', 'gradingPeriod',
                '2022-10-02', '2022-12-24', 'as-trm-222-1234', '2023'
            ]
        ];
        self::createcsvfiles($testdir . DIRECTORY_SEPARATOR . 'academicSessions.csv', $academicsessionscontent);

        // Creating classes.csv.
        $classecontent = [
            csv_client_const_helper::HEADER_CLASSES,
            [
                'cls-222-123456', 'active', '2023-05-01T18:25:43.511Z', 'Introduction to Physics',
                '09,10,11', 'crs-222-2023-456-12345', 'Phys 100 - 1', 'Scheduled',
                'Room 2-B', 'org-sch-222-456', 'as-trm-222-1234',
                'Science, Physics, Biology', 'PHY123, ASV120', '1'
            ],
            [
                'cls-222-123478', 'tobedeleted', '2023-05-01T18:25:43.511Z', 'History - 2',
                '10', 'crs-222-2023-456-23456', '2', 'Scheduled',
                'Room 18-C', 'org-sch-222-456', 'as-trm-222-1234',
                'History', 'HIS123', '1,2,3'
            ]
        ];
        self::createcsvfiles($testdir . DIRECTORY_SEPARATOR . 'classes.csv', $classecontent);

        // Creating enrollments.csv.
        $enrollmentscontent = [
            csv_client_const_helper::HEADER_ENROLLMENTS,
            [
                'enr-t-222-12345-123456', 'active', '2023-05-01T18:25:43.511Z', 'cls-222-12345',
                'org-sch-222-456', 'usr-222-123456', 'teacher', 'FALSE',
                '2022-03-15', '2022-06-15'
            ],
            [
                'enr-s-222-12345-987654', '', '', 'cls-222-12345', 'org-sch-222-456',
                'usr-222-987654', 'student', 'FALSE', '2022-03-16', '2022-06-16'
            ]
        ];
        self::createcsvfiles($testdir . DIRECTORY_SEPARATOR . 'enrollments.csv', $enrollmentscontent);

        // Creating orgs.csv.
        $orgscontent = [
            csv_client_const_helper::HEADER_ORGS,
            [
                'org-sch-222-3456', 'active', '2023-05-01T18:25:43.511Z', 'Upper School',
                'school', 'US', 'org-dpt-222-456'
            ],
            [
                'org-sch-222-456', '', '', 'History Department', 'department',
                'History', 'org-sch-222-3456'
            ],
            [
                'org-sch-222-7654', 'tobedeleted', '2023-05-01T18:25:43.511Z', 'US History',
                'department', 'US History', 'org-sch-222-3456'
            ]
        ];
        self::createcsvfiles($testdir . DIRECTORY_SEPARATOR . 'orgs.csv', $orgscontent);

        // Creating users.csv.
        $userscontent = [
            csv_client_const_helper::HEADER_USERS,
            [
                'usr-222-123456', 'active', '2023-05-01', 'TRUE', 'org-sch-222-456',
                'teacher', 'john.doe', '', 'John', 'Doe', 'Michael', '123456',
                'john.doe@myschool.com', '6037778888', '6032221111', 'usr-222-66778900',
                '11', 'Password1*'
            ],
            [
                'usr-222-66778899', '', '', 'TRUE', 'org-sch-222-456',
                'student', 'mary.jones', '{LDAP:12}', 'Mary', 'Jones', 'Jane',
                '66778899', 'mary.jones@myschool.com', '6031234567', '6031234567',
                'usr-222-66778900', '12', 'Password1*'
            ],
            [
                'usr-222-66778900', 'active', '2023-05-01', 'TRUE', 'org-sch-222-456',
                'parent', 'thomas.joness', '{LDAP:12},{LTI:15},{Fed:23}', 'Thomas',
                'Jones', 'Paul', '66778900', 'thomas.jones@testemail.com',
                '6039876543', '6039876543', 'usr-222-66778899', '10', 'Password1*'
            ]
        ];
        self::createcsvfiles($testdir . DIRECTORY_SEPARATOR . 'users.csv', $userscontent);
    }
}
