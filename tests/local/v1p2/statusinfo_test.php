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

use enrol_oneroster\local\v1p2\statusinfo_relations\status_info;
use enrol_oneroster\local\v1p2\statusinfo_relations\severity;
use enrol_oneroster\local\v1p2\statusinfo_relations\code_major;
use enrol_oneroster\local\v1p2\statusinfo_relations\code_minor;
use enrol_oneroster\local\v1p2\statusinfo_relations\code_minor_field;
use enrol_oneroster\local\v1p2\statusinfo_relations\code_minor_values;

class statusinfo_test extends \advanced_testcase
{
    /**
     * Method that tests the creation of a failure statusInfo object type.
     */
    public function test_statusinfo_failure_creation() {
        $code_minor = new code_minor(
            new code_minor_field('TargetEndSystem', code_minor_values::forbidden)
        );

        $status_info = status_info::failure(severity::error, $code_minor, 'Access denied');

        $this->assertEquals(code_major::failure, $status_info->get_code_major());
        $this->assertEquals(severity::error, $status_info->get_severity());
        $this->assertEquals('Access denied', $status_info->get_description());
        $this->assertInstanceOf(code_minor::class, $status_info->get_code_minor());
    }

    /**
     * Method that tests the conversion of a failure statusInfo object type to an array.
     */
    public function test_statusinfo_failure_to_array() {
        $code_minor = new code_minor(
            new code_minor_field('TargetEndSystem', code_minor_values::forbidden)
        );

        $status_info = status_info::failure(severity::error, $code_minor, 'Access denied');
        $response_array = $status_info->to_array();

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

        $this->assertEquals($expected, $response_array);
    }

    /**
     * Method that tests the creation of a processing statusInfo object type.
     */
    public function test_statusinfo_processing() {
        $status_info = status_info::processing('Request is being processed');

        $this->assertEquals(code_major::processing, $status_info->get_code_major());
        $this->assertEquals(severity::status, $status_info->get_severity());
        $this->assertEquals('Request is being processed', $status_info->get_description());
    }

    /**
     * Method that tests the creation of a unsupported statusInfo object type.
     */
    public function test_statusinfo_unsupported() {
        $status_info = status_info::unsupported('Feature not supported');

        $this->assertEquals(code_major::unsupported, $status_info->get_code_major());
        $this->assertEquals(severity::warning, $status_info->get_severity());
        $this->assertEquals('Feature not supported', $status_info->get_description());
    }
}
