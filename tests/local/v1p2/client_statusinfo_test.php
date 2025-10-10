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
use enrol_oneroster\local\v1p2\container;
use enrol_oneroster\local\v1p2\endpoints\rostering;
use enrol_oneroster\local\v1p2\statusinfo_relations\status_info;
use enrol_oneroster\local\v1p2\statusinfo_relations\severity;

// Ensure all the statusinfo_relations classes and enums are loaded
require_once(__DIR__ . '/../../../classes/local/v1p2/statusinfo_relations/status_info.php');

class client_statusinfo_test extends \advanced_testcase {
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


    public function test_status_info_factory_methods() {
        $this->resetAfterTest();

        // Test failure factory method
        $code_minor = new \enrol_oneroster\local\v1p2\statusinfo_relations\code_minor(
            new \enrol_oneroster\local\v1p2\statusinfo_relations\code_minor_field(
                'TargetEndSystem',
                \enrol_oneroster\local\v1p2\statusinfo_relations\code_minor_values::invaliddata
            )
        );

        $failure_status = \enrol_oneroster\local\v1p2\statusinfo_relations\status_info::failure(
            severity::error,
            $code_minor,
            'Invalid request parameters'
        );
        $failure_array = $failure_status->to_array();

        $this->assertEquals('failure', $failure_array['imsx_codeMajor']);
        $this->assertEquals('error', $failure_array['imsx_severity']);
        $this->assertEquals('Invalid request parameters', $failure_array['imsx_description']);
        $this->assertArrayHasKey('imsx_CodeMinor', $failure_array);
    }


    public function test_execute_with_error_status_info_creation() {
        $this->resetAfterTest();

        // Create a mock client that will throw an exception
        $client = new oauth2_client('test', 'test', 'test', 'test');
        $container = new container($client);
        $endpoint = new rostering($container);
        $command = new \enrol_oneroster\local\command($endpoint, '/users', 'GET', 'Get all users', ['users'], null, null, []);

        // Mock a response that will cause an exception (invalid JSON)
        $mockResponse = "invalid json response";

        // Use reflection to test the create_error_response_with_status_info method directly
        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('create_error_response_with_status_info');
        $method->setAccessible(true);

        // Create a mock exception with a specific code using reflection
        $exception = new \moodle_exception("Could not decode JSON token response: " . $mockResponse);
        $reflectionException = new \ReflectionClass($exception);
        $codeProperty = $reflectionException->getProperty('code');
        $codeProperty->setAccessible(true);
        $codeProperty->setValue($exception, 400);

        $result = $method->invoke($client, $exception, $command);

        // Test error structure - convert to array for easier testing
        $resultArray = (array) $result;
        $this->assertArrayHasKey('imsx_statusInfo', $resultArray);
        $this->assertEquals('failure', $resultArray['imsx_statusInfo']['imsx_codeMajor']);
        $this->assertEquals('error', $resultArray['imsx_statusInfo']['imsx_severity']);
        $this->assertArrayHasKey('imsx_CodeMinor', $resultArray['imsx_statusInfo']);
        $this->assertStringContainsString('Could not decode JSON', $resultArray['imsx_statusInfo']['imsx_description']);
    }

    public function test_get_code_minor_for_status_function() {
        $this->resetAfterTest();

        $client = new oauth2_client('test', 'test', 'test', 'test');

        // Use reflection to test the private method
        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('get_code_minor_for_status');
        $method->setAccessible(true);

        // Test various HTTP status codes
        $testCases = [
            400 => 'invaliddata',
            401 => 'unauthorisedrequest',
            403 => 'forbidden',
            404 => 'unknownobject',
            429 => 'server_busy',
            500 => 'internal_server_error',
            999 => 'internal_server_error' // default case
        ];

        foreach ($testCases as $statusCode => $expectedCodeMinor) {
            $result = $method->invoke($client, $statusCode);
            $this->assertArrayHasKey('imsx_codeMinorField', $result);
            $this->assertCount(1, $result['imsx_codeMinorField']);
            $this->assertEquals('TargetEndSystem', $result['imsx_codeMinorField'][0]['imsx_codeMinorFieldName']);
            $this->assertEquals($expectedCodeMinor, $result['imsx_codeMinorField'][0]['imsx_codeMinorFieldValue']);
        }
    }

    public function test_execute_with_http_error_creates_status_info() {
        $this->resetAfterTest();

        $client = new oauth2_client('test', 'test', 'test', 'test');
        $container = new container($client);
        $endpoint = new rostering($container);
        $command = new \enrol_oneroster\local\command($endpoint, '/users', 'GET', 'Get all users', ['users'], null, null, []);

        // Test different error scenarios
        $errorScenarios = [
            ['code' => 401, 'message' => 'Unauthorized', 'expectedCodeMinor' => 'unauthorisedrequest'],
            ['code' => 403, 'message' => 'Forbidden', 'expectedCodeMinor' => 'forbidden'],
            ['code' => 404, 'message' => 'Not Found', 'expectedCodeMinor' => 'unknownobject'],
            ['code' => 500, 'message' => 'Internal Server Error', 'expectedCodeMinor' => 'internal_server_error']
        ];

        foreach ($errorScenarios as $scenario) {
            $exception = new moodle_exception($scenario['message']);
            // Set the error code using reflection
            $reflectionException = new \ReflectionClass($exception);
            $codeProperty = $reflectionException->getProperty('code');
            $codeProperty->setAccessible(true);
            $codeProperty->setValue($exception, $scenario['code']);

            $reflection = new \ReflectionClass($client);
            $method = $reflection->getMethod('create_error_response_with_status_info');
            $method->setAccessible(true);

            $result = $method->invoke($client, $exception, $command);
            $resultArray = (array) $result;

            $this->assertArrayHasKey('imsx_statusInfo', $resultArray);
            $this->assertEquals('failure', $resultArray['imsx_statusInfo']['imsx_codeMajor']);
            $this->assertEquals('error', $resultArray['imsx_statusInfo']['imsx_severity']);
            $this->assertStringContainsString($scenario['message'], $resultArray['imsx_statusInfo']['imsx_description']);
            $this->assertArrayHasKey('imsx_CodeMinor', $resultArray['imsx_statusInfo']);
        }
    }
}
