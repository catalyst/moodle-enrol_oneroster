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
namespace enrol_oneroster\tests;

use enrol_oneroster\OneRosterHelper;

require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/../classes/local/csv_client_helper.php');

/**
 * One Roster tests for the client_helper class.
 *
 * @package    enrol_oneroster
 * @copyright  Gustavo Amorim De Almeida, Ruben Cooper, Josh Bateson, Brayden Porter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers  enrol_oneroster\OneRosterHelper
 */
class processcsv_test extends \advanced_testcase {
    private $testDir;
    private $manifestPath;
    
    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        $this->testDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'csvTestDir';
        if (!file_exists($this->testDir)) {
            mkdir($this->testDir);
        }
        $this->manifestPath = $this->testDir . DIRECTORY_SEPARATOR . 'manifest.csv';
        
        // Creating manifest.csv
        $manifest_content = [
            ['propertyName', 'value'],
            ['file.academicSessions', 'bulk'],
            ['file.classes', 'bulk'],
            ['file.enrollments', 'bulk'],
            ['file.orgs', 'bulk'],
            ['file.users', 'bulk'],
        ];
        $handle = fopen($this->manifestPath, 'w');
        foreach ($manifest_content as $line) {
            fputcsv($handle, $line);
        }
        fclose($handle);
        
        // Creating academicSessions.csv
        $academicSessions_content = [OneRosterHelper::HEADER_ACADEMIC_SESSIONS, ['session1', 'active', '2023-05-01', 'Session Title', 'term', '2023-01-01', '2023-12-31', 'parent1', '2023']];
        $handle = fopen($this->testDir . DIRECTORY_SEPARATOR . 'academicSessions.csv', 'w');
        foreach ($academicSessions_content as $line) {
            fputcsv($handle, $line);
        }
        fclose($handle);

        // Creating classes.csv
        $classes_content = [OneRosterHelper::HEADER_CLASSES, ['class1', 'active', '2023-05-01', 'Class Title', '10', 'course1', 'code1', 'type1', 'Room 101', 'school1', 'term1', 'Math', 'MTH101', '1']];
        $handle = fopen($this->testDir . DIRECTORY_SEPARATOR . 'classes.csv', 'w');
        foreach ($classes_content as $line) {
            fputcsv($handle, $line);
        }
        fclose($handle);


        // Creating enrollments.csv
        $enrollments_content = [OneRosterHelper::HEADER_ENROLLMENTS, ['enrollment1', 'active', '2023-05-01', 'class1', 'school1', 'user1', 'student', 'true', '2023-01-01', '2023-12-31']];
        $handle = fopen($this->testDir . DIRECTORY_SEPARATOR . 'enrollments.csv', 'w');
        foreach ($enrollments_content as $line) {
            fputcsv($handle, $line);
        }
        fclose($handle);

        // Creating orgs.csv
        $orgs_content = [OneRosterHelper::HEADER_ORGS, ['org1', 'active', '2023-05-01', 'Org Name', 'school', 'ID123', 'parentOrg']];
        $handle = fopen($this->testDir . DIRECTORY_SEPARATOR . 'orgs.csv', 'w');
        foreach ($orgs_content as $line) {
            fputcsv($handle, $line);
        }
        fclose($handle);

        // Creating users.csv
        $users_content = [OneRosterHelper::HEADER_USERS, ['user1', 'active', '2023-05-01', 'true', 'org1', 'student', 'jdoe', 'ID456', 'John', 'Doe', 'M', 'ID789', 'jdoe@example.com', '', '', 'agent1', '10', 'password123']];
        $handle = fopen($this->testDir . DIRECTORY_SEPARATOR . 'users.csv', 'w');
        foreach ($users_content as $line) {
            fputcsv($handle, $line);
        }
        fclose($handle);
    }

    /**
     * Tear down the test environment.
     */
    protected function tearDown(): void
    {
        if (file_exists($this->testDir)) {
            array_map('unlink', glob($this->testDir . DIRECTORY_SEPARATOR . '*'));
            rmdir($this->testDir);
        }
    }

    /**
     * Test the check_manifest_and_files method.
     */
    public function testCheckManifestAndFiles_allFilesPresent(){
    $result = OneRosterHelper::check_manifest_and_files($this->manifestPath, $this->testDir);
    $expected = [
        'missing_files' => [],
        'invalid_headers' => []
    ];
    $this->assertEquals($expected, $result, 'All files should be present and have valid headers.');
    }

    /**
     * Test the check_manifest_and_files method with a missing file.
     */
    public function testCheckManifestAndFiles_missingFile()
    {
        unlink($this->testDir . DIRECTORY_SEPARATOR . 'users.csv');
        $result = OneRosterHelper::check_manifest_and_files($this->manifestPath, $this->testDir);
        $this->assertEqualsCanonicalizing(['users.csv'], $result['missing_files'], 'users.csv should be reported as missing');
        $this->assertEmpty($result['invalid_headers'], 'No headers should be invalid');
    }

    /**
     * Test the validate_csv_headers function with valid headers.
     */
    public function testValidateCsvHeaders_validHeaders()
    {
        // Test Users headers
        $file_path = $this->testDir . DIRECTORY_SEPARATOR . 'users.csv';
        $result = OneRosterHelper::validate_csv_headers($file_path);
        $this->assertTrue($result, 'Headers should be valid.');
        // Test Academic Sessions headers
        $file_path = $this->testDir . DIRECTORY_SEPARATOR . 'academicSessions.csv';
        $result = OneRosterHelper::validate_csv_headers($file_path);
        $this->assertTrue($result, 'Headers should be valid.');
        // Test Orgs headers
        $file_path = $this->testDir . DIRECTORY_SEPARATOR . 'orgs.csv';
        $result = OneRosterHelper::validate_csv_headers($file_path);
        $this->assertTrue($result, 'Headers should be valid.');
        // Test Enrollments headers
        $file_path = $this->testDir . DIRECTORY_SEPARATOR . 'enrollments.csv';
        $result = OneRosterHelper::validate_csv_headers($file_path);
        $this->assertTrue($result, 'Headers should be valid.');
        // Test Classes headers
        $file_path = $this->testDir . DIRECTORY_SEPARATOR . 'classes.csv';
        $result = OneRosterHelper::validate_csv_headers($file_path);
        $this->assertTrue($result, 'Headers should be valid.');
    }

    /**
     * Test the validate_csv_headers function with invalid headers.
     */
    public function testValidateCsvHeaders_invalidHeaders()
    {
        $file_path = $this->testDir . DIRECTORY_SEPARATOR . 'invalid_users.csv';
        $invalid_headers = ['wrongHeader1', 'wrongHeader2', 'wrongHeader3'];
        $handle = fopen($file_path, 'w');
        fputcsv($handle, $invalid_headers);
        fclose($handle);
        
        $result = OneRosterHelper::validate_csv_headers($file_path);
        $this->assertFalse($result, 'Headers should be invalid.');
        
        $file_path = $this->testDir . DIRECTORY_SEPARATOR . 'invalid_classes.csv';
        $invalid_headers = ['sourcedId', 'status', 'dateLastModified', 'title']; 
        $handle = fopen($file_path, 'w');
        fputcsv($handle, $invalid_headers);
        fclose($handle);
        
        $result = OneRosterHelper::validate_csv_headers($file_path);
        $this->assertFalse($result, 'Headers should be invalid due to missing columns.');
    }

    /**
     * Test the extract_csvs_to_arrays function.
     */
    public function testExtractCsvsToArrays()
    {
        $result = OneRosterHelper::extract_csvs_to_arrays($this->testDir);
        
        $this->assertArrayHasKey('academicSessions', $result);
        $this->assertCount(1, $result['academicSessions']);
        $this->assertEquals('session1', $result['academicSessions'][0]['sourcedId']);
        
        $this->assertArrayHasKey('classes', $result);
        $this->assertCount(1, $result['classes']);
        $this->assertEquals('class1', $result['classes'][0]['sourcedId']);
        
        $this->assertArrayHasKey('enrollments', $result);
        $this->assertCount(1, $result['enrollments']);
        $this->assertEquals('enrollment1', $result['enrollments'][0]['sourcedId']);
        
        $this->assertArrayHasKey('orgs', $result);
        $this->assertCount(1, $result['orgs']);
        $this->assertEquals('org1', $result['orgs'][0]['sourcedId']);
        
        $this->assertArrayHasKey('users', $result);
        $this->assertCount(1, $result['users']);
        $this->assertEquals('user1', $result['users'][0]['sourcedId']);
    }

    /**
     * Test the display_missing_and_invalid_files function.
     */
    public function testDisplayMissingAndInvalidFiles()
    {
        $missing_files = [
            'missing_files' => ['users.csv', 'classes.csv'],
            'invalid_headers' => ['enrollments.csv']
        ];

        $this->expectOutputString(
            'The following required files are missing: users.csv, classes.csv<br>' .
            'The following files have invalid or missing headers: enrollments.csv<br>'
        );

        OneRosterHelper::display_missing_and_invalid_files($missing_files);
    }

    /**
     * Test the getHeader function.
     */
    public function testGetHeader()
    {
        $result = OneRosterHelper::getHeader('academicSessions.csv');
        $this->assertEquals(OneRosterHelper::HEADER_ACADEMIC_SESSIONS, $result);

        $result = OneRosterHelper::getHeader('invalid.csv');
        $this->assertEquals([], $result);
    }
}
