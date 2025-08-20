<?php
namespace enrol_oneroster\tests\local\v1p2;
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
 * @copyright  QUT Capstone Team - Abhinav Gandham, Harrison Dyba, Jonathon Foo, Khushi Patel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */



namespace enrol_oneroster\tests\local\v1p2;

use enrol_oneroster\local\v1p2\imsx_statusinfo;
use enrol_oneroster\local\v1p2\codeMajor;
use enrol_oneroster\local\v1p2\severity;
use enrol_oneroster\local\v1p2\codeMinor;
use enrol_oneroster\local\v1p2\minorFieldValue;



final class imsx_statusinfo_test extends \enrol_oneroster\local\oneroster_testcase {

    public function test_construct_success() {
        $data = (object)[
            'codeMajor' => 'success',
            'codeMinor' => (object)[
                'fieldName' => 'filter',
                'fieldValue' => 'fullsuccess',
            ],
            'description' => 'Request completed successfully',
            'severity' => 'status',
        ];

        $status = new imsx_statusinfo($data);

        // ✅ Expect the actual class, not the test class
        $this->assertInstanceOf(imsx_statusinfo::class, $status);
        $this->assertEquals(codeMajor::success, $status->codeMajor);
        $this->assertEquals('Request completed successfully', $status->description);
        $this->assertEquals(severity::status, $status->severity);
        $this->assertEquals('filter', $status->codeMinor->fieldname);
        $this->assertEquals(minorFieldValue::fullsuccess, $status->codeMinor->codeMinorFieldValue);
    }

    public function test_missing_codeMajor_throws_exception() {
        $this->expectException(\moodle_exception::class);

        $data = (object)[
            'codeMinor' => (object)[
                'fieldName' => 'filter',
                'fieldValue' => 'fullsuccess',
            ],
            'description' => 'Missing codeMajor field',
            'severity' => 'status',
        ];

        // ✅ Instantiate the class under test
        new imsx_statusinfo($data);
    }

    public function test_invalid_codeMajor_throws_exception() {
        $this->expectException(\moodle_exception::class);

        $data = (object)[
            'codeMajor' => 'invalid_value',
            'codeMinor' => (object)[
                'fieldName' => 'filter',
                'fieldValue' => 'fullsuccess',
            ],
            'description' => 'Invalid codeMajor test',
            'severity' => 'status',
        ];

        // ✅ Instantiate the class under test
        //new imsx_statusinfo($data);
        $status = new \enrol_oneroster\local\v1p2\imsx_statusinfo($data);

    }
}
