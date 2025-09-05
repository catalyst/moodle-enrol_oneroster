<?php
class client_statusinfo_test extends \advanced_testcase {
    public function test_execute_with_status_info_invalid_credentials() {
        $this->resetAfterTest();
        $client = new \enrol_oneroster\local\v1p1\oauth2_client('test', 'test', 'test', 'test');
        $container = new \enrol_oneroster\local\v1p1\container($client);
        $endpoint = new \enrol_oneroster\local\v1p1\endpoints\rostering($container);
        $command = new \enrol_oneroster\local\command($endpoint, '/users', 'GET', 'Get all users', ['users'], null, null, []);

        $result = $client->executeWithStatusInfo($command);

        $this->assertArrayHasKey('imsx_statusInfo', $result);

        // Test that we get a valid status info structure regardless of success/failure
        $this->assertArrayHasKey('imsx_codeMajor', $result['imsx_statusInfo']);
        $this->assertContains($result['imsx_statusInfo']['imsx_codeMajor'], ['success', 'failure', 'processing', 'unsupported']);

        // Print the result for debugging
        echo "\n=== Status Info Test Result ===\n";
        echo json_encode($result, JSON_PRETTY_PRINT) . "\n";
        echo "===============================\n";
    }

    public function test_execute_with_auto_status_info() {
        $this->resetAfterTest();
        $client = new \enrol_oneroster\local\v1p1\oauth2_client('test', 'test', 'test', 'test');

        // Enable auto status info
        $client->setAutoStatusInfo(true);
        $this->assertTrue($client->isAutoStatusInfoEnabled());

        $container = new \enrol_oneroster\local\v1p1\container($client);
        $endpoint = new \enrol_oneroster\local\v1p1\endpoints\rostering($container);
        $command = new \enrol_oneroster\local\command($endpoint, '/users', 'GET', 'Get all users', ['users'], null, null, []);

        // Use the new method that respects auto status info
        $result = $client->executeWithOptionalStatusInfo($command);

        $this->assertArrayHasKey('imsx_statusInfo', $result);
        $this->assertArrayHasKey('imsx_codeMajor', $result['imsx_statusInfo']);
        $this->assertContains($result['imsx_statusInfo']['imsx_codeMajor'], ['success', 'failure', 'processing', 'unsupported']);

        // Print the result for debugging
        echo "\n=== Auto Status Info Test Result ===\n";
        echo json_encode($result, JSON_PRETTY_PRINT) . "\n";
        echo "====================================\n";
    }

    public function test_execute_with_explicit_status_info_flag() {
        $this->resetAfterTest();
        $client = new \enrol_oneroster\local\v1p1\oauth2_client('test', 'test', 'test', 'test');
        $container = new \enrol_oneroster\local\v1p1\container($client);
        $endpoint = new \enrol_oneroster\local\v1p1\endpoints\rostering($container);
        $command = new \enrol_oneroster\local\command($endpoint, '/users', 'GET', 'Get all users', ['users'], null, null, []);

        // Use the new method with explicit flag
        $result = $client->executeWithOptionalStatusInfo($command, null, true);

        $this->assertArrayHasKey('imsx_statusInfo', $result);
        $this->assertArrayHasKey('imsx_codeMajor', $result['imsx_statusInfo']);
        $this->assertContains($result['imsx_statusInfo']['imsx_codeMajor'], ['success', 'failure', 'processing', 'unsupported']);

        // Print the result for debugging
        echo "\n=== Status Info Test Result ===\n";
        echo json_encode($result, JSON_PRETTY_PRINT) . "\n";
        echo "===============================\n";
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

        // Print the success result
        echo "\n=== SUCCESS Status Info Test Result ===\n";
        echo json_encode($result, JSON_PRETTY_PRINT) . "\n";
        echo "======================================\n";
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

        // Print both results
        echo "\n=== SUCCESS Factory Method Result ===\n";
        echo json_encode($successArray, JSON_PRETTY_PRINT) . "\n";
        echo "====================================\n";

        echo "\n=== FAILURE Factory Method Result ===\n";
        echo json_encode($failureArray, JSON_PRETTY_PRINT) . "\n";
        echo "====================================\n";
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

        // Print the result
        echo "\n=== Default Response SUCCESS Result ===\n";
        echo json_encode($result, JSON_PRETTY_PRINT) . "\n";
        echo "======================================\n";
    }

}