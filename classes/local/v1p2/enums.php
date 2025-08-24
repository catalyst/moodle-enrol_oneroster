<?php
namespace enrol_oneroster\classes\local\v1p2;
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
 * @copyright  QUT Capstone Team - Abhinav Gandham, Harrison Dyba, Jonathon Foo, Kushi Patel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Major status codes
 */
enum codeMajor: string {
    case success = 'success';
    case processing = 'processing';
    case failure = 'failure';
    case unsupported = 'unsupported';
}

/**
 * Severity levels
 */
enum severity: string {
    case status = 'status';
    case warning = 'warning';
    case error = 'error';
}

/**
 * Minor field values
 */
enum minorFieldValue: string {
    case fullsuccess = 'fullsuccess';
    case invalid_filter = 'invalid_filter';
    // Add more if needed
}
