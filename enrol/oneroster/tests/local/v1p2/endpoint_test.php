<?php
namespace enrol_oneroster\local\v1p2;
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

/**
 * One Roster Enrolment Client Unit tests.
 *
 * @package    enrol_oneroster
 * @copyright  QUT Capstone Team - Abhinav Gandham, Harrison Dyba, Jonathon Foo, Kushi Patel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
use advanced_testcase;
use enrol_oneroster\local\v1p2\client;

defined('MOODLE_INTERNAL') || die();

/**
 * Tests for the endpoint class in OneRoster v1p2.
 *
 * @covers \enrol_oneroster\local\v1p2\endpoint
 */
class endpoint_test extends advanced_testcase {

    /**
     * Test get_classes_for_user_returns_expected_format
     *
     * @copyright  Khushi
     */
    public function test_get_classes_for_user_returns_expected_format() {
        $this->resetAfterTest(true);

        
        $mockclient = $this->getMockBuilder(client::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['execute'])
            ->getMock();

        
        $mockclient->method('execute')->willReturn([
            'userid' => 1001,
            'statuscodeMajor' => 'success',
            'classes' => ['class1', 'class2']
        ]);

               $endpoint = new class($mockclient) extends endpoint {
            private $mockclient;

            public function __construct($mockclient) {
                $this->mockclient = $mockclient;
            }

            protected function get_client(): client {
                return $this->mockclient;
            }
        };

       
        $result = $endpoint->get_classes_for_user(1001);

        $this->assertIsArray($result);
        $this->assertEquals(['class1', 'class2'], $result);
    }
}
