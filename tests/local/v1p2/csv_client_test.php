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
 * @copyright  QUT Capstone Team - Abhinav Gandham, Harrison Dyba, Jonathon Foo, Kushi Patel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace enrol_oneroster\tests\local\v1p2;

use advanced_testcase;
use enrol_oneroster\local\v1p2\csv_client_helper;
use enrol_oneroster\client_helper;
require_once(__DIR__ . '/csv_test_helper.php');
use PHPUnit\Framework\TestCase;

/**
 * One Roster tests for the client_helper class.
 *
 * @package    enrol_oneroster
 * @copyright  QUT Capstone Team - Abhinav Gandham, Harrison Dyba, Jonathon Foo, Kushi Patel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers  \enrol_oneroster\local\csv_client_helper
 */
class csv_client_test extends advanced_testcase {

    /**
     * Sets role mappings, runs test environment, runs syncronise on csv data, tests role mappings.
     */
    public function test_execute_full_data() {
        $this->resetAfterTest(true);
        $selectedorg = 'ORG_1';
        $zipfilepath = 'enrol/oneroster/tests/fixtures/csv_data/VRd118.zip';


        global $DB;
        set_config('role_mapping_teacher', 'student', 'enrol_oneroster');
        set_config('role_mapping_student', 'student', 'enrol_oneroster');
        set_config('role_mapping_aide', 'student', 'enrol_oneroster');
        set_config('role_mapping_proctor', 'student', 'enrol_oneroster');
        set_config('role_mapping_parent', 'student', 'enrol_oneroster');
        set_config('role_mapping_guardian', 'student', 'enrol_oneroster');
        set_config('role_mapping_relative', 'student', 'enrol_oneroster');
        set_config('role_mapping_counselor', 'student', 'enrol_oneroster');
        set_config('role_mapping_districtAdmin', 'student', 'enrol_oneroster');
        set_config('role_mapping_principal', 'student', 'enrol_oneroster');
        set_config('role_mapping_siteAdmin', 'student', 'enrol_oneroster');
        set_config('role_mapping_systemAdmin', 'student', 'enrol_oneroster');

        // Prepare the test environment.
        $csvclient = $this->prepare_test_environment($selectedorg, $zipfilepath);

        // Perform synchronization.
        $csvclient->synchronise();

        // Assert database records.
        $users = $DB->get_records('user');

        foreach ($users as $userid => $data){
            $this->assert_user_roles($userid);
        }

    }

    /**
     * Prepares the test environment by extracting and validating CSV data.
     *
     * @param string $org The organization ID to use.
     * @param string $filepath The path to the ZIP file containing CSV data.
     * @return \enrol_oneroster\local\csv_client The prepared CSV client.
     */
    private function prepare_test_environment($org, $filepath){
        global $DB;

        $uniqueid = uniqid();
        $tempdir = make_temp_directory('oneroster_csv/' . $uniqueid);

        // Extract ZIP file.
        $zip = new \ZipArchive();
        $res = $zip->open($filepath);
        $this->assertTrue($res === true, 'The ZIP file should open successfully.');
        $zip->extractTo($tempdir);
        $zip->close();

        $manifestpath = $tempdir . '/manifest.csv';

        // Check manifest and files.
        $missingfiles = csv_client_helper::check_manifest_and_files($manifestpath, $tempdir);
        
        $this->assertEmpty($missingfiles['missingfiles'], 'There should be no missing files according to the manifest.');
        $this->assertEmpty($missingfiles['invalidheaders'], 'There should be no invalid headers in the extracted CSV files.');
        
        // Validate CSV data types.
        $isvalid = csv_client_helper::validate_csv_data_types($tempdir);
        $this->assertArrayHasKey('is_valid', $isvalid);
        $this->assertTrue($isvalid['is_valid']);
        

        // Extract CSV data to arrays.
        $csvdata = csv_client_helper::extract_csvs_to_arrays($tempdir);
        $this->assertNotEmpty($csvdata, 'The extracted CSV data should not be empty.');

        // Validate user data and set configuration.
        //if (csv_client_helper::validate_user_data($csvdata) === true) {
        set_config('datasync_schools', $org, 'enrol_oneroster');
        
        //}
        // Initialize CSV client.
        $csvclient = client_helper::get_csv_client();
        $csvclient->set_org_id($org);
        // Set CSV data.
        $manifest = $csvdata['manifest'] ?? [];
        $users = $csvdata['users'] ?? [];
        $classes = $csvdata['classes'] ?? [];
        $courses = $csvdata['courses'] ?? [];
        $orgs = $csvdata['orgs'] ?? [];
        $enrollments = $csvdata['enrollments'] ?? [];
        $academicsessions = $csvdata['academicSessions'] ?? [];
        $roles = $csvdata['roles'] ?? [];
        $demographics = $csvdata['demographics'] ?? [];
        $userprofiles = $csvdata['userProfiles'] ?? [];

        $csvclient->versioned_set_data($manifest, $users, $classes, $courses, $orgs, $enrollments, $academicsessions, $roles, $demographics, $userprofiles);
        return $csvclient;
    }

    //assert_user_agents
    /**
     * check's that agents have been associated to a student
     *
     * @param string $studentId the student to check
     * @param array $the ID's of the agents associated with user
     */
    private function assert_user_agents($studentId, $agentIDs,){
        global $DB;
        $users = $DB->get_records('user', ['userid' => $studentId]);

    }
    /**
     * check's that agents have been associated to a student
     *
     * @param string $userID the user to check
     * @param array $roles that should be associated with user
     */

    private function assert_user_roles($userID){
        global $DB;
        
        $ras = $DB->get_records('role_assignments', ['userid'=>$userID]);

        $mapped = array_map(function($el) {
            global $DB;
            $role = $DB->get_record('roles', ['id' => $el->roleid]);
            return $role->shortname;
        }, $ras);

        //assert the oneroster roles to find which moodle keys you're looking to assert
        if (!empty($mapped)){
            $this->assertArrayHasKey('student', $mapped);
        };
    }
}
