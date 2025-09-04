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
 * Class default_response_test.
 *
 * @package    enrol_oneroster
 * @copyright  QUT Capstone Team - Abhinav Gandham, Harrison Dyba, Jonathon Foo, Khushi Patel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
use enrol_oneroster\local\v1p2\responses\default_response;
use enrol_oneroster\local\v1p2\statusinfo_relations\statusInfo;
use enrol_oneroster\local\v1p2\statusinfo_relations\codeMinorField;
use enrol_oneroster\local\v1p2\statusinfo_relations\codeMajor;
use enrol_oneroster\local\v1p2\statusinfo_relations\severity;
use enrol_oneroster\local\v1p2\statusinfo_relations\codeMinor;
use enrol_oneroster\local\v1p2\statusinfo_relations\codeMinorValues;

class default_response_test extends \advanced_testcase {

    public function test_default_response_success() {
        $defaultResponse = default_response::success([], 'test', 'Test success');
        $this->assertEquals(codeMajor::success, $defaultResponse->getstatusInfo()->getCodeMajor());
        $this->assertEquals(severity::status, $defaultResponse->getstatusInfo()->getSeverity());
        $this->assertEquals('Test success', $defaultResponse->getstatusInfo()->getDescription());
        $this->assertEquals([], $defaultResponse->getData());
        $this->assertEquals('test', $defaultResponse->getCollectionName());
    }

    public function test_default_response_failure() {
        $codeMinor = new codeMinor(
            new codeMinorField('TargetEndSystem', codeMinorValues::forbidden)
        );
        $defaultResponse = default_response::failure(severity::error, $codeMinor, 'Test failure');
        $this->assertEquals(codeMajor::failure, $defaultResponse->getstatusInfo()->getCodeMajor());
        $this->assertEquals(severity::error, $defaultResponse->getstatusInfo()->getSeverity());
        $this->assertEquals('Test failure', $defaultResponse->getstatusInfo()->getDescription());
        $this->assertEquals(null, $defaultResponse->getData());
        $this->assertEquals(null, $defaultResponse->getCollectionName());
    }

    public function test_default_response_processing() {
        $defaultResponse = default_response::processing('Test processing');
        $this->assertEquals(codeMajor::processing, $defaultResponse->getstatusInfo()->getCodeMajor());
        $this->assertEquals(severity::status, $defaultResponse->getstatusInfo()->getSeverity());
        $this->assertEquals('Test processing', $defaultResponse->getstatusInfo()->getDescription());
        $this->assertEquals(null, $defaultResponse->getData());
        $this->assertEquals(null, $defaultResponse->getCollectionName());
    }

    public function test_default_response_unsupported() {
        $defaultResponse = default_response::unsupported('Test unsupported');
        $this->assertEquals(codeMajor::unsupported, $defaultResponse->getstatusInfo()->getCodeMajor());
        $this->assertEquals(severity::warning, $defaultResponse->getstatusInfo()->getSeverity());
        $this->assertEquals('Test unsupported', $defaultResponse->getstatusInfo()->getDescription());
        $this->assertEquals(null, $defaultResponse->getData());
        $this->assertEquals(null, $defaultResponse->getCollectionName());
    }

    public function test_default_response_to_array() {
        $defaultResponse = default_response::success([], 'test', 'Test success');
        $this->assertEquals([
            'imsx_statusInfo' => $defaultResponse->getstatusInfo()->toArray(),
            'test' => []
        ], $defaultResponse->toArray());
    }

    public function test_default_response_to_json() {
        $defaultResponse = default_response::success([], 'test', 'Test success');
        $this->assertEquals(json_encode([
            'imsx_statusInfo' => $defaultResponse->getstatusInfo()->toArray(),
            'test' => []
        ]), $defaultResponse->toJSON());
    }

}