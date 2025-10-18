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
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace enrol_oneroster;
use enrol_oneroster\local\v1p1\csv_client_helper;

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
     * Test the synchronise method with the full data set.
     *
     * @covers \enrol_oneroster\local\csv_client
     */
    public function test_execute_full_data() {
        $this->resetAfterTest(true);
        $selectedorg = 'org-sch-222-456';
        $zipfilepath = 'enrol/oneroster/tests/fixtures/csv_data/Test_full_data_set.zip';

        // Prepare the test environment.
        $csvclient = $this->prepare_test_environment($selectedorg, $zipfilepath);

        // Perform synchronization.
        $csvclient->synchronise();

        // Assert database records.
        $this->assert_database_records(3, 8, 8); // Expected counts for courses, users, enrolments.
    }

    /**
     * Test the synchronise method with the minimal data set.
     *
     * @covers \enrol_oneroster\local\csv_client
     */
    public function test_execute_minimal_data() {
        $this->resetAfterTest(true);
        $selectedorg = 'org-sch-222-456';
        $zipfilepath = 'enrol/oneroster/tests/fixtures/csv_data/Test_minimal_data_set.zip';

        // Prepare the test environment.
        $csvclient = $this->prepare_test_environment($selectedorg, $zipfilepath);

        // Perform synchronization.
        $csvclient->synchronise();

        // Assert database records.
        $this->assert_database_records(3, 2, 8); // Expected counts for courses, users, enrolments.
    }

    /**
     * Prepares the test environment by extracting and validating CSV data.
     *
     * @param string $selectedorg The organization ID to use.
     * @param string $zipfilepath The path to the ZIP file containing CSV data.
     * @return \enrol_oneroster\local\csv_client The prepared CSV client.
     */
    private function prepare_test_environment(string $selectedorg, string $zipfilepath) {
        global $DB;

        $uniqueid = uniqid();
        $tempdir = make_temp_directory('oneroster_csv/' . $uniqueid);

        // Extract ZIP file.
        $zip = new \ZipArchive();
        $res = $zip->open($zipfilepath);
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
        if (csv_client_helper::validate_user_data($csvdata) === true) {
            set_config('datasync_schools', $selectedorg, 'enrol_oneroster');
        }

        // Initialize CSV client.
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

        return $csvclient;
    }

    /**
     * Asserts the database records after synchronization.
     *
     * @param int $expectedcourses The expected number of courses.
     * @param int $expectedusers The expected number of users.
     * @param int $expectedenrolments The expected number of enrolments.
     */
    private function assert_database_records(int $expectedcourses, int $expectedusers, int $expectedenrolments) {
        global $DB;

        $courses = $DB->get_records('course');
        $users = $DB->get_records('user');
        $enrolments = $DB->get_records('enrol');

        // Check courses.
        foreach ($courses as $course) {
            $this->assertArrayHasKey('id', (array)$course);
            $this->assertArrayHasKey('fullname', (array)$course);
            $this->assertIsString($course->fullname, 'Course fullname should be a string.');
        }

        // Check users.
        foreach ($users as $user) {
            $this->assertArrayHasKey('id', (array)$user);
            $this->assertArrayHasKey('username', (array)$user);
            $this->assertIsString($user->username, 'Username should be a string.');
        }

        // Check enrolments.
        foreach ($enrolments as $enrol) {
            $this->assertArrayHasKey('courseid', (array)$enrol);
            $this->assertArrayHasKey('enrol', (array)$enrol);
            $courseid = (int)$enrol->courseid;
            $this->assertIsInt($courseid, 'Course ID should be an integer.');
        }

        // Assertions for record counts.
        $this->assertCount($expectedcourses, $courses, "There should be exactly {$expectedcourses} course records.");
        $this->assertCount($expectedusers, $users, "There should be exactly {$expectedusers} user records.");
        $this->assertCount($expectedenrolments, $enrolments, "There should be exactly {$expectedenrolments} enrolment records.");
    }
}
