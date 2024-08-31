<?php
namespace enrol_oneroster\tests;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/enrol/oneroster/oneroster_helper.php');

use enrol_oneroster\OneRosterHelper;

class processcsv_test extends \advanced_testcase {
    private $testDir;
    private $manifestPath;

    protected function setUp(): void
    {
        // Create a temporary directory for testing
        $this->testDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'testDir';
        if (!file_exists($this->testDir)) {
            mkdir($this->testDir);
        }

        // Create a manifest.csv file
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

        // Create required files listed in the manifest
        file_put_contents($this->testDir . DIRECTORY_SEPARATOR . 'users.csv', 'User data');
        file_put_contents($this->testDir . DIRECTORY_SEPARATOR . 'enrollments.csv', 'Enrollment data');
        file_put_contents($this->testDir . DIRECTORY_SEPARATOR . 'courses.csv', 'Course data');
        file_put_contents($this->testDir . DIRECTORY_SEPARATOR . 'classes.csv', 'Class data');
        file_put_contents($this->testDir . DIRECTORY_SEPARATOR . 'academicSessions.csv', 'Academic session data');
        file_put_contents($this->testDir . DIRECTORY_SEPARATOR . 'orgs.csv', 'Org data');
    }

    protected function tearDown(): void
    {
        // Clean up after the test, if the directory still exists
        if (file_exists($this->testDir)) {
            OneRosterHelper::delete_directory($this->testDir);
        }
    }

    public function testCheckManifestAndFiles_allFilesPresent()
    {
        // Assert that there are no missing files
        $missing_files = OneRosterHelper::check_manifest_and_files($this->manifestPath, $this->testDir);
        $this->assertEmpty($missing_files, 'No files should be missing');
    }

    public function testCheckManifestAndFiles_missingFile()
    {
        // Remove the users.csv file to simulate a missing file
        unlink($this->testDir . DIRECTORY_SEPARATOR . 'users.csv');
        
        // Assert that the users.csv file is reported as missing
        $missing_files = OneRosterHelper::check_manifest_and_files($this->manifestPath, $this->testDir);
        $this->assertEqualsCanonicalizing(['users.csv'], $missing_files, 'users.csv should be reported as missing');
    }
}
