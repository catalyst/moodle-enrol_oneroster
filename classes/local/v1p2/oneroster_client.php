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
 * One Roster Enrolment Client.
 *
 * @package    enrol_oneroster
 * @copyright  QUT Capstone Team - Abhinav Gandham, Harrison Dyba, Jonathon Foo, Kushi Patel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster\local\v1p2;

use DateTime;
use context_user;
use core_course_category;
use core_user;
use enrol_oneroster\local\converter;

// Client and associated features.
use enrol_oneroster\local\interfaces\container as container_interface;
use enrol_oneroster\local\interfaces\rostering_endpoint as rostering_endpoint_interface;

// Entities which represent Moodle objects.
use enrol_oneroster\local\interfaces\course_representation;
use enrol_oneroster\local\interfaces\coursecat_representation;
use enrol_oneroster\local\interfaces\user_representation;
use enrol_oneroster\local\interfaces\enrollment_representation;

use enrol_oneroster\local\v1p2\endpoints\rostering as rostering_endpoint;
use enrol_oneroster\local\entities\school as school_entity;
use enrol_oneroster\local\entities\user as user_entity;
use enrol_oneroster\local\v1p1\oneroster_client as client_version_one;
use moodle_url;
use stdClass;

/**
 * One Roster v1p2 client.
 *
 * @package    enrol_oneroster
 * @copyright  QUT Capstone Team - Abhinav Gandham, Harrison Dyba, Jonathon Foo, Kushi Patel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait oneroster_client {
    use client_version_one;
    // Add new methods or override methods from v1p1 trait here.
}
