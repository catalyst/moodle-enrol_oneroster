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

use enrol_oneroster\csv_client_helper;
use enrol_oneroster\csv_client_const_helper;

/**
 * One Roster tests for the client_helper class.
 *
 * @package    enrol_oneroster
 * @copyright  Gustavo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \enrol_oneroster\local\csv_client_helper
 */
class process_csv_file_check_test extends \advanced_testcase {
    /**
     * Path to the test directory.
     *
     * @var string
     */
    private $testdir;

    /**
     * Path to the manifest.csv file.
     *
     * @var string
     */
    private $manifestpath;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void {
        $this->testdir = make_temp_directory('csvtest_dir');
        $this->manifestpath = $this->testdir . DIRECTORY_SEPARATOR . 'manifest.csv';

        // Creating manifest.csv.
        $manifestcontent = [
            ['propertyName', 'value'],
            ['file.academicSessions', 'bulk'],
            ['file.classes', 'bulk'],
            ['file.enrollments', 'bulk'],
            ['file.orgs', 'bulk'],
            ['file.users', 'bulk'],
        ];
        $handle = fopen($this->manifestpath, 'w');
        foreach ($manifestcontent as $line) {
            fputcsv($handle, $line);
        }
        fclose($handle);

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
        $handle = fopen($this->testdir . DIRECTORY_SEPARATOR . 'academicSessions.csv', 'w');
        foreach ($academicsessionscontent as $line) {
            fputcsv($handle, $line);
        }
        fclose($handle);

        // Creating classes.csv.
        $classescontent = [
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
        $handle = fopen($this->testdir . DIRECTORY_SEPARATOR . 'classes.csv', 'w');
        foreach ($classescontent as $line) {
            fputcsv($handle, $line);
        }
        fclose($handle);

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
        $handle = fopen($this->testdir . DIRECTORY_SEPARATOR . 'enrollments.csv', 'w');
        foreach ($enrollmentscontent as $line) {
            fputcsv($handle, $line);
        }
        fclose($handle);

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
        $handle = fopen($this->testdir . DIRECTORY_SEPARATOR . 'orgs.csv', 'w');
        foreach ($orgscontent as $line) {
            fputcsv($handle, $line);
        }
        fclose($handle);

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
        $handle = fopen($this->testdir . DIRECTORY_SEPARATOR . 'users.csv', 'w');
        foreach ($userscontent as $line) {
            fputcsv($handle, $line);
        }
        fclose($handle);
    }

    /**
     * Test the check_manifest_and_files method.
     *
     * @covers \enrol_oneroster\local\csv_client_helper::check_manifest_and_files
     */
    public function test_check_manifest_and_files_all_files_present() {
        $result = csv_client_helper::check_manifest_and_files($this->manifestpath, $this->testdir);
        $expected = [
            'missing_files' => [],
            'invalid_headers' => []
        ];
        $this->assertEquals($expected, $result, 'All files should be present and have valid headers.');
    }

    /**
     * Test the check_manifest_and_files method with a missing file.
     *
     * @covers \enrol_oneroster\local\csv_client_helper::check_manifest_and_files
     */
    public function test_check_manifest_and_files_missing_file() {
        unlink($this->testdir . DIRECTORY_SEPARATOR . 'users.csv');
        $result = csv_client_helper::check_manifest_and_files($this->manifestpath, $this->testdir);
        $this->assertEqualsCanonicalizing(
            ['users.csv'],
            $result['missing_files'],
            'users.csv should be reported as missing.'
        );
        $this->assertEmpty($result['invalid_headers'], 'No headers should be invalid.');
    }

    /**
     * Test the validate_csv_headers function with valid headers.
     *
     * @covers \enrol_oneroster\local\csv_client_helper::validate_csv_headers
     */
    public function test_validate_csv_headers_valid_headers() {
        // Test Users headers.
        $filepath = $this->testdir . DIRECTORY_SEPARATOR . 'users.csv';
        $result = csv_client_helper::validate_csv_headers($filepath);
        $this->assertTrue($result, 'Headers should be valid.');

        // Test Academic Sessions headers.
        $filepath = $this->testdir . DIRECTORY_SEPARATOR . 'academicSessions.csv';
        $result = csv_client_helper::validate_csv_headers($filepath);
        $this->assertTrue($result, 'Headers should be valid.');

        // Test Orgs headers.
        $filepath = $this->testdir . DIRECTORY_SEPARATOR . 'orgs.csv';
        $result = csv_client_helper::validate_csv_headers($filepath);
        $this->assertTrue($result, 'Headers should be valid.');

        // Test Enrollments headers.
        $filepath = $this->testdir . DIRECTORY_SEPARATOR . 'enrollments.csv';
        $result = csv_client_helper::validate_csv_headers($filepath);
        $this->assertTrue($result, 'Headers should be valid.');

        // Test Classes headers.
        $filepath = $this->testdir . DIRECTORY_SEPARATOR . 'classes.csv';
        $result = csv_client_helper::validate_csv_headers($filepath);
        $this->assertTrue($result, 'Headers should be valid.');
    }

    /**
     * Test the validate_csv_headers function with invalid headers.
     *
     * @covers \enrol_oneroster\local\csv_client_helper::validate_csv_headers
     */
    public function test_validate_csv_headers_invalid_headers() {
        $filepath = $this->testdir . DIRECTORY_SEPARATOR . 'invalid_users.csv';
        $invalidheaders = ['wrongHeader1', 'wrongHeader2', 'wrongHeader3'];
        $handle = fopen($filepath, 'w');
        fputcsv($handle, $invalidheaders);
        fclose($handle);

        $result = csv_client_helper::validate_csv_headers($filepath);
        $this->assertFalse($result, 'Headers should be invalid.');

        $filepath = $this->testdir . DIRECTORY_SEPARATOR . 'invalid_classes.csv';
        $invalidheaders = ['sourcedId', 'status', 'dateLastModified', 'title'];
        $handle = fopen($filepath, 'w');
        fputcsv($handle, $invalidheaders);
        fclose($handle);

        $result = csv_client_helper::validate_csv_headers($filepath);
        $this->assertFalse($result, 'Headers should be invalid due to missing columns.');
    }

    /**
     * Test the extract_csvs_to_arrays function.
     *
     * @covers \enrol_oneroster\local\csv_client_helper::extract_csvs_to_arrays
     */
    public function test_extract_csvs_to_arrays() {
        $result = csv_client_helper::extract_csvs_to_arrays($this->testdir);

        $this->assertArrayHasKey('academicSessions', $result);
        $this->assertCount(2, $result['academicSessions']);
        $this->assertEquals('as-trm-222-1234', $result['academicSessions'][0]['sourcedId']);

        $this->assertArrayHasKey('classes', $result);
        $this->assertCount(2, $result['classes']);
        $this->assertEquals('cls-222-123456', $result['classes'][0]['sourcedId']);

        $this->assertArrayHasKey('enrollments', $result);
        $this->assertCount(2, $result['enrollments']);
        $this->assertEquals('enr-t-222-12345-123456', $result['enrollments'][0]['sourcedId']);

        $this->assertArrayHasKey('orgs', $result);
        $this->assertCount(3, $result['orgs']);
        $this->assertEquals('org-sch-222-3456', $result['orgs'][0]['sourcedId']);

        $this->assertArrayHasKey('users', $result);
        $this->assertCount(3, $result['users']);
        $this->assertEquals('usr-222-123456', $result['users'][0]['sourcedId']);
    }

    /**
     * Test the display_missing_and_invalid_files function.
     *
     * @covers \enrol_oneroster\local\csv_client_helper::display_missing_and_invalid_files
     */
    public function test_display_missing_and_invalid_files() {
        $missingfiles = [
            'missing_files' => ['users.csv', 'classes.csv'],
            'invalid_headers' => ['enrollments.csv']
        ];

        $this->expectOutputString(
            'The following required files are missing: users.csv, classes.csv<br>' .
            'The following files have invalid or missing headers: enrollments.csv<br>'
        );

        csv_client_helper::display_missing_and_invalid_files($missingfiles);
    }

    /**
     * Test the getHeader function.
     *
     * @covers \enrol_oneroster\local\csv_client_helper::getHeader
     */
    public function test_get_header() {
        $result = csv_client_helper::getHeader('academicSessions.csv');
        $this->assertEquals(csv_client_const_helper::HEADER_ACADEMIC_SESSIONS, $result);

        $result = csv_client_helper::getHeader('invalid.csv');
        $this->assertEquals([], $result);
    }
}
