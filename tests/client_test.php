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

/**
 * One Roster tests for the client_helper class.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers  enrol_oneroster\client_helper
 */


 class client_csv_testcase extends advanced_testcase {

    public function test_csv_client(): void {

        $this->resetAfterTest(true);

        // Include the helper file
        require_once(__DIR__ . '/../csv_data_helper.php');

        // Setup fixtures here.

        // Get data from the helper
        $manifest = csv_data_helper::get_manifest_data();
        $users = csv_data_helper::get_users_data();
        $classes = csv_data_helper::get_classes_data();
        $courses = csv_data_helper::get_courses_data();
        $orgs = csv_data_helper::get_orgs_data();
        $enrolments = csv_data_helper::get_enrolments_data();
        $academicsessions = csv_data_helper::get_academicSessions_data();

        
        $csvclient = client_helper::get_csv_client();
        $csvclient->synchronise();
       
        // Assert final state here
        // $schools = $DB->get_records();
        // $this->assertCount(3, $schools);;
     
  } 
}

 