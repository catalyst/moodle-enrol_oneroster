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
namespace enrol_oneroster\tests\local\v1p2;

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/../../process_csv_file_check_test.php');

use enrol_oneroster\process_csv_file_check_test as file_check_test_version_one;
use enrol_oneroster\local\v1p2\csv_client_helper;

/**
 * One Roster tests for the client_helper class.
 *
 * @package    enrol_oneroster
 * @copyright  QUT Capstone Team - Abhinav Gandham, Harrison Dyba, Jonathon Foo, Khushi Patel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \enrol_oneroster\local\v1p2\csv_client_helper
 */
class process_csv_file_check_test extends file_check_test_version_one {
    /**
     * Set up the test environment.
     */
    protected function setUp(): void {
        parent::setUp();
    }

    /**
     * Test the validate_csv_headers function with valid headers.
     *
     * @covers \enrol_oneroster\local\v1p2\csv_client_helper::validate_csv_headers
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

        // Test User Profiles headers.
        $filepath = $this->testdir . DIRECTORY_SEPARATOR . 'userprofiles.csv';
        $result = csv_client_helper::validate_csv_headers($filepath);
        $this->assertTrue($result, 'Headers should be valid.');
    }
}
