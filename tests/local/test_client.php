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

namespace enrol_oneroster\tests\local;

use enrol_oneroster\local\interfaces\client as client_interface;
use enrol_oneroster\local\interfaces\rostering_client as rostering_client_interface;
use enrol_oneroster\local\command;
use enrol_oneroster\local\interfaces\container as container_interface;
use enrol_oneroster\local\interfaces\filter;
use progress_trace;
use stdClass;

/**
 * Concrete test client that implements all required interfaces.
 *
 * @package    enrol_oneroster
 * @copyright  QUT Capstone Team - Abhinav Gandham, Harrison Dyba, Jonathon Foo, Khushi Patel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_client implements client_interface, rostering_client_interface {

    private $trace;
    private $container;

    public function set_trace(progress_trace $trace): void {
        $this->trace = $trace;
    }

    public function get_trace(): progress_trace {
        return $this->trace ?? new \null_progress_trace();
    }

    public function execute(command $command, filter $filter = null): stdClass {
        return (object) ['response' => 'mock response'];
    }

    public function get_container(): container_interface {
        return $this->container ?? new \stdClass();
    }

    public function synchronise(?int $onlysincetime = null): void {
        // Mock implementation
    }

    public function authenticate(): void {
        // Mock implementation
    }

    public function get_rostering_endpoint(): \enrol_oneroster\local\interfaces\rostering_endpoint {
        return new \stdClass();
    }

    public function sync_roster(?int $onlysincetime = null): void {
        // Mock implementation
    }

    public function fetch_organisation_list(): iterable {
        return [];
    }
}
