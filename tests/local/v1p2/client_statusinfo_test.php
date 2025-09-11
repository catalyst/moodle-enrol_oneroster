<?php
class client_statusinfo_test extends \advanced_testcase {
    public function test_execute_with_invalid_credentials() {
        $this->resetAfterTest();

        // Instead of making a real HTTP request, test the validation logic directly
        $client = new \enrol_oneroster\local\v1p1\oauth2_client('test', 'test', 'test', 'test');

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
        $client = new \enrol_oneroster\local\v1p1\oauth2_client('test', 'test', 'test', 'test');
        $container = new \enrol_oneroster\local\v1p1\container($client);
        $endpoint = new \enrol_oneroster\local\v1p1\endpoints\rostering($container);
        $command = new \enrol_oneroster\local\command($endpoint, '/users', 'GET', 'Get all users', ['users'], null, null, []);

        // Mock a successful response by directly testing the wrapResponseWithStatusInfo method
        $mockResponse = (object) [
            'users' => [
                (object) ['sourcedId' => 'user1', 'email' => 'user1@example.com'],
                (object) ['sourcedId' => 'user2', 'email' => 'user2@example.com']
            ]
        ];

        // Use reflection to test the private method
        $reflection = new \ReflectionClass($client);
        $method = $reflection->getMethod('wrapResponseWithStatusInfo');
        $method->setAccessible(true);

        $result = $method->invoke($client, $mockResponse, $command);

        // Test success structure
        $this->assertArrayHasKey('imsx_statusInfo', $result);
        $this->assertArrayHasKey('imsx_codeMajor', $result['imsx_statusInfo']);
        $this->assertEquals('success', $result['imsx_statusInfo']['imsx_codeMajor']);
        $this->assertArrayHasKey('imsx_severity', $result['imsx_statusInfo']);
        $this->assertEquals('status', $result['imsx_statusInfo']['imsx_severity']);
        $this->assertArrayHasKey('imsx_description', $result['imsx_statusInfo']);
        $this->assertArrayHasKey('users', $result);
        $this->assertCount(2, $result['users']);
    }

    public function test_execute_with_invalid_code_major() {
        $this->resetAfterTest();

        $client = new \enrol_oneroster\local\v1p1\oauth2_client('test', 'test', 'test', 'test');

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
        $this->expectExceptionMessage('INVALID STRUCTURE: Invalid code major value found in the response, values must be either success, failure, processing, or unsupported');
        $method->invoke($client, $mockInvalidResponse);

    }

    public function test_execute_with_invalid_severity() {
        $this->resetAfterTest();

        $client = new \enrol_oneroster\local\v1p1\oauth2_client('test', 'test', 'test', 'test');

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
        $this->expectExceptionMessage('INVALID STRUCTURE: Invalid severity value found in the response, values must be either status, warning, or error');
        $method->invoke($client, $mockInvalidResponse);

    }

    public function test_execute_with_invalid_code_minor() {
        $this->resetAfterTest();

        $client = new \enrol_oneroster\local\v1p1\oauth2_client('test', 'test', 'test', 'test');

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
        $this->expectExceptionMessage('INVALID STRUCTURE: Invalid code minor value found in the response, values must be either fullsuccess, invalid_filter_field, invalid_selection_field, invaliddata, unauthorisedrequest, forbidden, server_busy, unknownobject, or internal_server_error');
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

}