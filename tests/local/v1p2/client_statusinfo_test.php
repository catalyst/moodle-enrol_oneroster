<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and modify
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
 * Class client_statusinfo_test.
 *
 * @package    enrol_oneroster
 * @copyright  QUT Capstone Team - Abhinav Gandham, Harrison Dyba, Jonathon Foo, Khushi Patel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use enrol_oneroster\local\v1p2\oauth2_client;
use enrol_oneroster\local\v1p2\statusinfo_relations\status_info;

// Ensure all the statusinfo_relations classes and enums are loaded
require_once(__DIR__ . '/../../../classes/local/v1p2/statusinfo_relations/status_info.php');

class client_statusinfo_test extends \advanced_testcase {
    /**
     * Test validation of incoming status info with invalid credentials scenario.
     */
    public function test_execute_with_invalid_credentials() {
        $this->resetAfterTest();

        // Instead of making a real HTTP request, test the validation logic directly
        $client = new oauth2_client('test', 'test', 'test', 'test');

        // Create a mock response that would come from a failed request
        $mockInvalidResponse = (object) [
            'imsx_statusInfo' => (object) [
                'imsx_codeMajor' => 'failure',
                'imsx_severity' => 'error',
                'imsx_CodeMinor' => (object) [
                    'imsx_codeMinorField' => [
                        (object) [
                            'imsx_codeMinorFieldName' => 'TargetEndSystem',
                            'imsx_codeMinorFieldValue' => 'unauthorisedrequest'
                        ]
                    ]
                ],
                'imsx_description' => 'Authentication failed'
            ]
        ];

        // Use reflection to test the validation method directly
        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('validate_status_info');
        $method->setAccessible(true);

        // Test that the validation passes for a properly structured failure response
        $isValid = $method->invoke($client, $mockInvalidResponse);
        $this->assertTrue($isValid, 'Valid status info should pass validation');
    }

    /**
     * Test validation of incoming status info with success scenario.
     */
    public function test_execute_with_status_info_success_scenario() {
        $this->resetAfterTest();

        // Test the validation logic directly instead of making HTTP requests
        $client = new oauth2_client('test', 'test', 'test', 'test');

        // Create a mock response with valid status info
        $mockResponse = (object) [
            'imsx_statusInfo' => (object) [
                'imsx_codeMajor' => 'success',
                'imsx_severity' => 'status',
                'imsx_description' => 'Operation successful'
            ],
            'users' => [
                (object) ['sourcedId' => 'user1', 'email' => 'user1@example.com'],
                (object) ['sourcedId' => 'user2', 'email' => 'user2@example.com']
            ]
        ];

        // Use reflection to test the validation method directly
        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('validate_status_info');
        $method->setAccessible(true);

        // Test that the validation passes for a properly structured success response
        $isValid = $method->invoke($client, $mockResponse);
        $this->assertTrue($isValid, 'Valid status info should pass validation');
    }

    /**
     * Test validation with invalid code major.
     */
    public function test_execute_with_invalid_code_major() {
        $this->resetAfterTest();

        $client = new oauth2_client('test', 'test', 'test', 'test');

        // Creating the mock data for this test
        $mockInvalidResponse = (object) [
            'imsx_statusInfo' => (object) [
                'imsx_codeMajor' => 'invalid_value',
                'imsx_severity' => 'status'
            ]
        ];

        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('validate_status_info');
        $method->setAccessible(true);

        $this->expectException(\moodle_exception::class);
        $this->expectExceptionMessage(status_info::invalid_code_major_message);
        $method->invoke($client, $mockInvalidResponse);
    }

    /**
     * Test validation with invalid severity.
     */
    public function test_execute_with_invalid_severity() {
        $this->resetAfterTest();

        $client = new oauth2_client('test', 'test', 'test', 'test');

        // Creating the mock data for this test
        $mockInvalidResponse = (object) [
            'imsx_statusInfo' => (object) [
                'imsx_codeMajor' => 'success',
                'imsx_severity' => 'invalid_value'
            ]
        ];

        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('validate_status_info');
        $method->setAccessible(true);

        $this->expectException(\moodle_exception::class);
        $this->expectExceptionMessage(status_info::invalid_severity_message);
        $method->invoke($client, $mockInvalidResponse);
    }

    /**
     * Test validation with invalid code minor structure.
     */
    public function test_execute_with_invalid_code_minor() {
        $this->resetAfterTest();

        $client = new oauth2_client('test', 'test', 'test', 'test');

        $mockInvalidResponse = (object) [
            'imsx_statusInfo' => (object) [
                'imsx_codeMajor' => 'failure',
                'imsx_severity' => 'error',
                'imsx_CodeMinor' => 'invalid_value'
            ]
        ];

        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('validate_status_info');
        $method->setAccessible(true);

        $this->expectException(\moodle_exception::class);
        $this->expectExceptionMessage(status_info::invalid_code_minor_structure_message);
        $method->invoke($client, $mockInvalidResponse);
    }
}
