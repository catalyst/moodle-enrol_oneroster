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
use enrol_oneroster\local\v1p2\statusinfo_relations\severity;
use enrol_oneroster\local\v1p2\statusinfo_relations\code_minor_field;
use enrol_oneroster\local\v1p2\statusinfo_relations\code_major;
use enrol_oneroster\local\v1p2\statusinfo_relations\code_minor;
use enrol_oneroster\local\v1p2\statusinfo_relations\code_minor_values;

// Ensure all the statusinfo_relations classes and enums are loaded
require_once(__DIR__ . '/../../../classes/local/v1p2/statusinfo_relations/status_info.php');

class default_response_test extends \advanced_testcase {
    public function test_default_response_failure() {
        $code_minor = new code_minor(
            new code_minor_field(code_minor_values::forbidden, 'TargetEndSystem')
        );
        $default_response = default_response::failure(severity::error, $code_minor, 'Test failure');
        $this->assertEquals(code_major::failure, $default_response->get_status_info()->get_code_major());
        $this->assertEquals(severity::error, $default_response->get_status_info()->get_severity());
        $this->assertEquals('Test failure', $default_response->get_status_info()->get_description());
        $this->assertEquals(null, $default_response->get_data());
        $this->assertEquals(null, $default_response->get_collection_name());
    }

    public function test_default_response_processing() {
        $default_response = default_response::processing('Test processing');
        $this->assertEquals(code_major::processing, $default_response->get_status_info()->get_code_major());
        $this->assertEquals(severity::status, $default_response->get_status_info()->get_severity());
        $this->assertEquals('Test processing', $default_response->get_status_info()->get_description());
        $this->assertEquals(null, $default_response->get_data());
        $this->assertEquals(null, $default_response->get_collection_name());
    }

    public function test_default_response_unsupported() {
        $default_response = default_response::unsupported('Test unsupported');
        $this->assertEquals(code_major::unsupported, $default_response->get_status_info()->get_code_major());
        $this->assertEquals(severity::warning, $default_response->get_status_info()->get_severity());
        $this->assertEquals('Test unsupported', $default_response->get_status_info()->get_description());
        $this->assertEquals(null, $default_response->get_data());
        $this->assertEquals(null, $default_response->get_collection_name());
    }
}
