<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * One Roster tests for endpoint.
 *
 * @package    enrol_oneroster
 * @copyright  Khushi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster\local\v1p2;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../oneroster_testcase.php');



use enrol_oneroster\local\v1p2\endpoint;
use enrol_oneroster\local\v1p2\interfaces\container as container_interface;
use enrol_oneroster\local\oneroster_testcase;

class endpoint_test extends oneroster_testcase {

    public function test_get_container_returns_expected_mock() {
        // Mock the container interface.
        $mockcontainer = $this->getMockBuilder(container_interface::class)
                              ->disableOriginalConstructor()
                              ->getMock();

        // Create a partial mock of endpoint and override get_container.
        $endpoint = $this->getMockBuilder(endpoint::class)
                         ->setConstructorArgs([$mockcontainer])
                         ->onlyMethods(['get_container'])
                         ->getMock();

        $endpoint->method('get_container')->willReturn($mockcontainer);

        $this->assertInstanceOf(container_interface::class, $endpoint->get_container());
    }

    // Adding.
}
