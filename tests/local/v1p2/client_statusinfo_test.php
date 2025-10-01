<?php

use enrol_oneroster\local\v1p2\oauth2_client;
use enrol_oneroster\local\v1p2\container;
use enrol_oneroster\local\v1p2\endpoints\rostering;
use enrol_oneroster\local\v1p2\statusinfo_relations\statusInfo;

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
        $method = $reflection->getMethod('validateStatusInfo');
        $method->setAccessible(true);

        // Test that the validation passes for a properly structured failure response
        $isValid = $method->invoke($client, $mockInvalidResponse);
        $this->assertTrue($isValid, 'Valid status info should pass validation');
    }

    public function test_execute_with_status_info_success_scenario() {
        $this->resetAfterTest();

        // Create a mock client that returns success
        $client = new oauth2_client('test', 'test', 'test', 'test');
        $container = new container($client);
        $endpoint = new rostering($container);
        $command = new \enrol_oneroster\local\command($endpoint, '/users', 'GET', 'Get all users', ['users'], null, null, []);

        // Mock a successful response by directly testing the createSuccessResponseWithStatusInfo method
        $mockResponse = (object) [
            'users' => [
                (object) ['sourcedId' => 'user1', 'email' => 'user1@example.com'],
                (object) ['sourcedId' => 'user2', 'email' => 'user2@example.com']
            ]
        ];

        // Use reflection to test the private method
        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('createSuccessResponseWithStatusInfo');
        $method->setAccessible(true);

        $result = $method->invoke($client, $mockResponse, $command);

        // Test success structure - convert to array for easier testing
        $resultArray = (array) $result;
        $this->assertArrayHasKey('imsx_statusInfo', $resultArray);
        $this->assertArrayHasKey('imsx_codeMajor', $resultArray['imsx_statusInfo']);
        $this->assertEquals('success', $resultArray['imsx_statusInfo']['imsx_codeMajor']);
        $this->assertArrayHasKey('imsx_severity', $resultArray['imsx_statusInfo']);
        $this->assertEquals('status', $resultArray['imsx_statusInfo']['imsx_severity']);
        $this->assertArrayHasKey('imsx_description', $resultArray['imsx_statusInfo']);
        $this->assertArrayHasKey('users', $resultArray);
        $this->assertCount(2, $resultArray['users']);
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
        $method = $reflection->getMethod('validateStatusInfo');
        $method->setAccessible(true);

        $this->expectException(\moodle_exception::class);
        $this->expectExceptionMessage(statusInfo::invalidCodeMajorMessage);
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
        $method = $reflection->getMethod('validateStatusInfo');
        $method->setAccessible(true);

        $this->expectException(\moodle_exception::class);
        $this->expectExceptionMessage(statusInfo::invalidSeverityMessage);
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
        $method = $reflection->getMethod('validateStatusInfo');
        $method->setAccessible(true);

        $this->expectException(\moodle_exception::class);
        $this->expectExceptionMessage(statusInfo::invalidCodeMinorStructureMessage);
        $method->invoke($client, $mockInvalidResponse);
    }


    public function test_status_info_factory_methods() {
        $this->resetAfterTest();

        // Test success factory method
        $successStatus = \enrol_oneroster\local\v1p2\statusinfo_relations\statusInfo::success('Users retrieved successfully');
        $successArray = $successStatus->toArray();

        $this->assertEquals('success', $successArray['imsx_codeMajor']);
        $this->assertEquals('status', $successArray['imsx_severity']);
        $this->assertEquals('Users retrieved successfully', $successArray['imsx_description']);
        $this->assertNull($successArray['imsx_CodeMinor']);

        // Test failure factory method
        $codeMinor = new \enrol_oneroster\local\v1p2\statusinfo_relations\codeMinor(
            new \enrol_oneroster\local\v1p2\statusinfo_relations\codeMinorField(
                'TargetEndSystem',
                \enrol_oneroster\local\v1p2\statusinfo_relations\codeMinorValues::invaliddata
            )
        );

        $failureStatus = \enrol_oneroster\local\v1p2\statusinfo_relations\statusInfo::failure(
            \enrol_oneroster\local\v1p2\statusinfo_relations\severity::error,
            $codeMinor,
            'Invalid request parameters'
        );
        $failureArray = $failureStatus->toArray();

        $this->assertEquals('failure', $failureArray['imsx_codeMajor']);
        $this->assertEquals('error', $failureArray['imsx_severity']);
        $this->assertEquals('Invalid request parameters', $failureArray['imsx_description']);
        $this->assertArrayHasKey('imsx_CodeMinor', $failureArray);
    }

    public function test_default_response_success() {
        $this->resetAfterTest();

        // Test successful response wrapper
        $data = [
            ['sourcedId' => 'user1', 'email' => 'user1@example.com'],
            ['sourcedId' => 'user2', 'email' => 'user2@example.com']
        ];

        $response = \enrol_oneroster\local\v1p2\responses\default_response::success(
            $data,
            'users',
            'Users retrieved successfully'
        );

        $result = $response->toArray();

        // Test structure
        $this->assertArrayHasKey('imsx_statusInfo', $result);
        $this->assertArrayHasKey('users', $result);
        $this->assertEquals('success', $result['imsx_statusInfo']['imsx_codeMajor']);
        $this->assertEquals('status', $result['imsx_statusInfo']['imsx_severity']);
        $this->assertEquals('Users retrieved successfully', $result['imsx_statusInfo']['imsx_description']);
        $this->assertCount(2, $result['users']);
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

        // Use reflection to test the createErrorResponseWithStatusInfo method directly
        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('createErrorResponseWithStatusInfo');
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

    public function test_getCodeMinorForStatus_function() {
        $this->resetAfterTest();

        $client = new oauth2_client('test', 'test', 'test', 'test');

        // Use reflection to test the private method
        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('getCodeMinorForStatus');
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
            $method = $reflection->getMethod('createErrorResponseWithStatusInfo');
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