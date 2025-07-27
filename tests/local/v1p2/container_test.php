<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * One Roster tests for container.
 *
 * @package    enrol_oneroster
 * @copyright  Khushi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster\local\v1p2;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../oneroster_testcase.php');

use enrol_oneroster\local\oneroster_testcase;
use enrol_oneroster\local\v1p2\interfaces\client as client_interface;

class container_test extends oneroster_testcase {

    /**
     * Test  created from the container.
     */
    public function test_get_client() {
        $container = new container();
        $client = $container->get_client();

        $this->assertInstanceOf(client_interface::class, $client);
    }

    // Add more .
}
