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
 * @copyright  Gustavo Amorim De Almeida, Ruben Cooper, Josh Bateson, Brayden Porter
 */
namespace enrol_oneroster;
use enrol_oneroster\csv_client_helper;

/**
 * One Roster tests for the client_helper class.
 *
 * @package    enrol_oneroster
 * @copyright  Gustavo Amorim De Almeida, Ruben Cooper, Josh Bateson, Brayden Porter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 * @covers  \enrol_oneroster\local\csv_client_helper
 */
class csv_client_test extends \advanced_testcase {
    /**
     * Test Synchronise method to check the data is inserted into the database.
     * This test uses the full data set.
     * 
     * @covers \enrol_oneroster\local\csv_client
     */
    public function test_execute_full_data() {
        global $DB;
        $this->resetAfterTest(true);
        $selectedorg = 'org-sch-222-456';

        $uniqueid = uniqid();
        $tempdir = make_temp_directory('oneroster_csv/' . $uniqueid);
        $zipfilepath = 'enrol/oneroster/tests/fixtures/csv_data/Test_full_data_set.zip';

        $zip = new \ZipArchive();
        $res = $zip->open($zipfilepath);
        $this->assertTrue($res === true, 'The ZIP file should open successfully.');
        $zip->extractTo($tempdir);
        $zip->close();

        $manifestpath = $tempdir . '/manifest.csv';

        $missingfiles = csv_client_helper::check_manifest_and_files($manifestpath, $tempdir);
        $this->assertEmpty($missingfiles['missing_files'], 'There should be no missing files according to the manifest.');
        $this->assertEmpty($missingfiles['invalid_headers'], 'There should be no invalid headers in the extracted CSV files.');

        $isvalid = csv_client_helper::validate_csv_data_types($tempdir);
        $this->assertArrayHasKey('is_valid', $isvalid);
        $this->assertTrue($isvalid['is_valid']);

        $csvdata = csv_client_helper::extract_csvs_to_arrays($tempdir);
        $this->assertNotEmpty($csvdata, 'The extracted CSV data should not be empty.');

        if (csv_client_helper::validate_user_data($csvdata) === true) {
            set_config('datasync_schools',  $selectedorg, 'enrol_oneroster');
        }

        $csvclient = client_helper::get_csv_client();

        $csvclient->set_org_id($selectedorg);

        // Set CSV data.
        $manifest = $csvdata['manifest'] ?? [];
        $users = $csvdata['users'] ?? [];
        $classes = $csvdata['classes'] ?? [];
        $orgs = $csvdata['orgs'] ?? [];
        $enrollments = $csvdata['enrollments'] ?? [];
        $academicsessions = $csvdata['academicSessions'] ?? [];

        $csvclient->set_data($manifest, $users, $classes, $orgs, $enrollments, $academicsessions);

        $csvclient->synchronise();

        $course = $DB->get_records('course');
        $user = $DB->get_records('user');
        $enrol = $DB->get_records('enrol');

        // Check courses.
        foreach ($course as $course) {
            $this->assertArrayHasKey('id', (array)$course);
            $this->assertArrayHasKey('fullname', (array)$course);
            $this->assertIsString($course->fullname, 'Course fullname should be a string.');
        }

        // Check users.
        foreach ($user as $user) {
            $this->assertArrayHasKey('id', (array)$user);
            $this->assertArrayHasKey('username', (array)$user);
            $this->assertIsString($user->username, 'Username should be a string.');
        }

        // Check enrollments.
        foreach ($enrol as $enrol) {
            $this->assertArrayHasKey('courseid', (array)$enrol);
            $this->assertArrayHasKey('enrol', (array)$enrol);
            $courseid = (int) $enrol->courseid;
            $this->assertIsInt($courseid, 'Course ID should be an integer.');
        }

        // Assertions for record counts.
        $this->assertCount(3, $course, 'There should be exactly 3 course records.');
        $this->assertCount(8, $user, 'There should be exactly 7 user records.');
        $this->assertCount(8, $enrol, 'There should be exactly 8 enrolment records.');
    }

    /**
     * Test Synchronise method to check the data is inserted into the database.
     * This test uses the minimal data set.
     * 
     * @covers \enrol_oneroster\local\csv_client
     */
    public function test_execute_minimal_data() {
        global $DB;
        $this->resetAfterTest(true);
        $selectedorg = 'org-sch-222-456';

        $uniqueid = uniqid();
        $tempdir = make_temp_directory('oneroster_csv/' . $uniqueid);
        $zipfilepath = 'enrol/oneroster/tests/fixtures/csv_data/Test_minimal_data_set.zip';

        $zip = new \ZipArchive();
        $res = $zip->open($zipfilepath);
        $this->assertTrue($res === true, 'The ZIP file should open successfully.');
        $zip->extractTo($tempdir);
        $zip->close();

        $manifestpath = $tempdir . '/manifest.csv';

        $missingfiles = csv_client_helper::check_manifest_and_files($manifestpath, $tempdir);
        $this->assertEmpty($missingfiles['missing_files'], 'There should be no missing files according to the manifest.');
        $this->assertEmpty($missingfiles['invalid_headers'], 'There should be no invalid headers in the extracted CSV files.');

        $isvalid = csv_client_helper::validate_csv_data_types($tempdir);
        $this->assertArrayHasKey('is_valid', $isvalid);
        $this->assertTrue($isvalid['is_valid']);

        $csvdata = csv_client_helper::extract_csvs_to_arrays($tempdir);
        $this->assertNotEmpty($csvdata, 'The extracted CSV data should not be empty.');

        if (csv_client_helper::validate_user_data($csvdata) === true) {
            set_config('datasync_schools',  $selectedorg, 'enrol_oneroster');
        }

        $csvclient = client_helper::get_csv_client();

        $csvclient->set_org_id($selectedorg);

        // Set CSV data.
        $manifest = $csvdata['manifest'] ?? [];
        $users = $csvdata['users'] ?? [];
        $classes = $csvdata['classes'] ?? [];
        $orgs = $csvdata['orgs'] ?? [];
        $enrollments = $csvdata['enrollments'] ?? [];
        $academicsessions = $csvdata['academicSessions'] ?? [];

        $csvclient->set_data($manifest, $users, $classes, $orgs, $enrollments, $academicsessions);

        $csvclient->synchronise();

        $course = $DB->get_records('course');
        $user = $DB->get_records('user');
        $enrol = $DB->get_records('enrol');

        // Check courses.
        foreach ($course as $course) {
            $this->assertArrayHasKey('id', (array)$course);
            $this->assertArrayHasKey('fullname', (array)$course);
            $this->assertIsString($course->fullname, 'Course fullname should be a string.');
        }

        // Check users.
        foreach ($user as $user) {
            $this->assertArrayHasKey('id', (array)$user);
            $this->assertArrayHasKey('username', (array)$user);
            $this->assertIsString($user->username, 'Username should be a string.');
        }

        // Check enrollments.
        foreach ($enrol as $enrol) {
            $this->assertArrayHasKey('courseid', (array)$enrol);
            $this->assertArrayHasKey('enrol', (array)$enrol);
            $courseid = (int) $enrol->courseid;
            $this->assertIsInt($courseid, 'Course ID should be an integer.');
        }
        
        $this->assertCount(3, $course, 'There should be exactly 3 course records.');
        $this->assertCount(2, $user, 'There should be exactly 2 user records.');
        $this->assertCount(8, $enrol, 'There should be exactly 8 enrolment records.');
    }
}
