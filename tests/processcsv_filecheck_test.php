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

namespace enrol_oneroster\tests;
use enrol_oneroster\OneRosterHelper;
global $CFG;

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/enrol/oneroster/oneroster_helper.php');

/**
 * One Roster tests for the client_helper class.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers  enrol_oneroster\OneRosterHelper
 */
class processcsv_test extends \advanced_testcase {
    private $testDir;
    private $manifestPath;
    
    /**
     * Set up the test environment.
     */
    protected function setUp(): void
    {
        $this->testDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'testDir';
        if (!file_exists($this->testDir)) {
            mkdir($this->testDir);
        }
        $this->manifestPath = $this->testDir . DIRECTORY_SEPARATOR . 'manifest.csv';
        $manifest_content = [
            ['propertyName', 'value'],
            ['manifest.version', 1],
            ['oneroster.version', 1.1],
            ['file.academicSessions', 'bulk'],
            ['file.categories', 'absent'],
            ['file.classes', 'bulk'],
            ['file.classResources', 'absent'],
            ['file.courses', 'bulk'],
            ['file.courseResources', 'absent'],
            ['file.demographics', 'absent'],
            ['file.enrollments', 'bulk'],
            ['file.lineItems', 'absent'],
            ['file.orgs', 'bulk'],
            ['file.resources', 'absent'],
            ['file.results', 'absent'],
            ['file.users', 'bulk'],
        ];

        $handle = fopen($this->manifestPath, 'w');
        foreach ($manifest_content as $line) {
            fputcsv($handle, $line);
        }
        fclose($handle);

        file_put_contents($this->testDir . DIRECTORY_SEPARATOR . 'users.csv', 'User data');
        file_put_contents($this->testDir . DIRECTORY_SEPARATOR . 'enrollments.csv', 'Enrollment data');
        file_put_contents($this->testDir . DIRECTORY_SEPARATOR . 'courses.csv', 'Course data');
        file_put_contents($this->testDir . DIRECTORY_SEPARATOR . 'classes.csv', 'Class data');
        file_put_contents($this->testDir . DIRECTORY_SEPARATOR . 'academicSessions.csv', 'Academic session data');
        file_put_contents($this->testDir . DIRECTORY_SEPARATOR . 'orgs.csv', 'Org data');
    }

    /**
     * Tear down the test environment.
     */
    protected function tearDown(): void
    {
        if (file_exists($this->testDir)) {
            OneRosterHelper::delete_directory($this->testDir);
        }
    }

    /**
     * Test the check_manifest_and_files method.
     */
    public function testCheckManifestAndFiles_allFilesPresent()
    {
        $missing_files = OneRosterHelper::check_manifest_and_files($this->manifestPath, $this->testDir);
        $this->assertEmpty($missing_files, 'No files should be missing');
    }

    /**
     * Test the check_manifest_and_files method with a missing file.
     */
    public function testCheckManifestAndFiles_missingFile()
    {
        unlink($this->testDir . DIRECTORY_SEPARATOR . 'users.csv');
        $missing_files = OneRosterHelper::check_manifest_and_files($this->manifestPath, $this->testDir);
        $this->assertEqualsCanonicalizing(['users.csv'], $missing_files, 'users.csv should be reported as missing');
    }
}
