<?php

namespace enrol_oneroster\tests\local\v1p2;
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and modify
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
 * Class statusinfo_test.
 *
 * @package    enrol_oneroster
 * @copyright  QUT Capstone Team - Abhinav Gandham, Harrison Dyba, Jonathon Foo, Khushi Patel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use enrol_oneroster\local\v1p2\statusinfo_relations\statusInfo;
use enrol_oneroster\local\v1p2\statusinfo_relations\codeMajor;
use enrol_oneroster\local\v1p2\statusinfo_relations\severity;
use enrol_oneroster\local\v1p2\statusinfo_relations\codeMinor;
use enrol_oneroster\local\v1p2\statusinfo_relations\codeMinorField;
use enrol_oneroster\local\v1p2\statusinfo_relations\codeMinorValues;

class statusinfo_test extends \advanced_testcase {

    /**
     * Method that tests the creation of a success statusInfo object type.
     */
    public function test_statusinfo_success_creation() {
        $statusInfo = statusInfo::success('Operation completed successfully');

        $this->assertEquals(codeMajor::success, $statusInfo->getCodeMajor());
        $this->assertEquals(severity::status, $statusInfo->getSeverity());
        $this->assertEquals('Operation completed successfully', $statusInfo->getDescription());
        $this->assertNull($statusInfo->getCodeMinor());
    }

    /**
     * Method that tests the conversion of a success statusInfo object type to an array.
     */
    public function test_statusinfo_success_to_array() {
        $statusInfo = statusInfo::success('Test success');
        $responseArray = $statusInfo->toArray();

        $expected = [
            'imsx_codeMajor' => 'success',
            'imsx_severity' => 'status',
            'imsx_description' => 'Test success'
        ];

        $this->assertEquals($expected, $responseArray);
    }

    /**
     * Method that tests the creation of a failure statusInfo object type.
     */
    public function test_statusinfo_failure_creation() {
        $codeMinor = new codeMinor(
            new codeMinorField('TargetEndSystem', codeMinorValues::forbidden)
        );

        $statusInfo = statusInfo::failure(severity::error, $codeMinor, 'Access denied');

        $this->assertEquals(codeMajor::failure, $statusInfo->getCodeMajor());
        $this->assertEquals(severity::error, $statusInfo->getSeverity());
        $this->assertEquals('Access denied', $statusInfo->getDescription());
        $this->assertInstanceOf(codeMinor::class, $statusInfo->getCodeMinor());
    }

    /**
     * Method that tests the conversion of a failure statusInfo object type to an array.
     */
    public function test_statusinfo_failure_to_array() {
        $codeMinor = new codeMinor(
            new codeMinorField('TargetEndSystem', codeMinorValues::forbidden)
        );

        $statusInfo = statusInfo::failure(severity::error, $codeMinor, 'Access denied');
        $responseArray= $statusInfo->toArray();

        $expected = [
            'imsx_codeMajor' => 'failure',
            'imsx_severity' => 'error',
            'imsx_description' => 'Access denied',
            'imsx_CodeMinor' => [
                'imsx_codeMinorField' => [
                    [
                        'imsx_codeMinorFieldName' => 'codeMinor',
                        'imsx_codeMinorFieldValue' => 'forbidden'
                    ]
                ]
            ]
        ];

        $this->assertEquals($expected, $responseArray);
    }

    /**
     * Method that tests the creation of a processing statusInfo object type.
     */
    public function test_statusinfo_processing() {
        $statusInfo = statusInfo::processing('Request is being processed');

        $this->assertEquals(codeMajor::processing, $statusInfo->getCodeMajor());
        $this->assertEquals(severity::status, $statusInfo->getSeverity());
        $this->assertEquals('Request is being processed', $statusInfo->getDescription());
    }

    /**
     * Method that tests the creation of a unsupported statusInfo object type.
     */
    public function test_statusinfo_unsupported() {
        $statusInfo = statusInfo::unsupported('Feature not supported');

        $this->assertEquals(codeMajor::unsupported, $statusInfo->getCodeMajor());
        $this->assertEquals(severity::warning, $statusInfo->getSeverity());
        $this->assertEquals('Feature not supported', $statusInfo->getDescription());
    }

    public function test_statusinfo_without_description() {
        $statusInfo = statusInfo::success();

        $this->assertEquals(codeMajor::success, $statusInfo->getCodeMajor());
        $this->assertEquals(severity::status, $statusInfo->getSeverity());
        $this->assertNull($statusInfo->getDescription());

        $responseArray = $statusInfo->toArray();
        $this->assertArrayNotHasKey('imsx_description', $responseArray);
    }

    /**
     * Method that tests the creation of a statusInfo object type without a code minor.
     */
    public function test_statusinfo_without_code_minor() {
        $statusInfo = statusInfo::success('Test');
        $responseArray= $statusInfo->toArray();

        $this->assertArrayNotHasKey('imsx_CodeMinor', $responseArray);
    }
}
