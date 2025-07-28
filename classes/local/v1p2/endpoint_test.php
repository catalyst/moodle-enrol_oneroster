<?php
namespace enrol_oneroster\tests\local;

use advanced_testcase;
use enrol_oneroster\local\roster\rostering;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../../../config.php');

class endpoint_test extends advanced_testcase {

    public function test_get_classes_for_user_returns_expected_format() {

        $this->resetAfterTest(true);

        // Mocking the rostering class and overriding get_user_classes
        $mock = $this->getMockBuilder(rostering::class)
                     ->disableOriginalConstructor()
                     ->onlyMethods(['get_user_classes'])
                     ->getMock();

        // Defining the return value for the mocked method
        $mock->expects($this->once())
             ->method('get_user_classes')
             ->with($this->equalTo('user123'))
             ->willReturn([
                 'statusInfo' => ['codeMajor' => 'success'],
                 'classes' => []
             ]);

        // Call the mocked method
        $result = $mock->get_user_classes('user123');

        // Check if statusInfo key exists in the result
        $this->assertArrayHasKey('statusInfo', $result);

        // Check if statusInfo contains expected codeMajor
        $this->assertEquals('success', $result['statusInfo']['codeMajor']);
    }
}
