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

/**
 * One Roster Enrolment Client Unit tests.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster;

use advanced_testcase;
use enrol_oneroster\local\command;
use enrol_oneroster\client_helper;
use enrol_oneroster\local\endpoint;
require_once(__DIR__ . '/../csv_data_helper.php');

/**
 * One Roster tests for the client_helper class.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers  enrol_oneroster\client_helper
 * @covers  enrol_oneroster\local\v1p1\oneroster_client
 * @covers  enrol_oneroster\local\csv_client
 */

 class client_csv_testcase extends advanced_testcase {

    /**
     * Test Synchronise method.to check the data is inserted into the database.
     */
    public function test_execute(): void {
        ob_start();

        global $DB;
        $this->resetAfterTest(true);

        $manifest = csv_data_helper::get_manifest_data();
        $users = csv_data_helper::get_users_data();
        $classes = csv_data_helper::get_classes_data();
        $orgs = csv_data_helper::get_orgs_data();
        $enrollments = csv_data_helper::get_enrollments_data();
        $academicSessions = csv_data_helper::get_academicsessions_data();

        $csvclient = client_helper::get_csv_client();

        $csvclient->set_data(
            $manifest,
            $users,
            $classes,
            $orgs,
            $enrollments,
            $academicSessions,
        );

        $csvclient->synchronise();

        $course = $DB->get_records('course');
        $user = $DB->get_records('user');
        $enrol = $DB->get_records('enrol');

        $this->assertCount(2, $course);
        $this->assertCount(2, $user);
        $this->assertCount(4, $enrol);

        ob_end_clean();
    }
}

 