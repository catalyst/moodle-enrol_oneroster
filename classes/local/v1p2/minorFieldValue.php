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
 * @copyright  QUT Capstone Team - Abhinav Gandham, Harrison Dyba, Jonathon Foo, Khushi Patel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class minorFieldValue {
    const FULLSUCCESS = 'fullsuccess';
    const PARTIALSUCCESS = 'partialsuccess';
    const INVALIDDATA = 'invaliddata';

    public function __construct(object $data) {
    // codeMajor
    if (empty($data->codeMajor) || !is_string($data->codeMajor)) {
        throw new \moodle_exception('Missing or invalid codeMajor value');
    }
    try {
        $this->codeMajor = codeMajor::from($data->codeMajor);
    } catch (\ValueError $e) {
        throw new \moodle_exception('Invalid codeMajor value');
    }

    // severity
    if (empty($data->severity) || !is_string($data->severity)) {
        throw new \moodle_exception('Missing or invalid severity value');
    }
    try {
        $this->severity = severity::from($data->severity);
    } catch (\ValueError $e) {
        throw new \moodle_exception('Invalid severity value');
    }

    $this->description = $data->description ?? '';

    if (isset($data->codeMinor->field) && is_string($data->codeMinor->field)) {
        try {
            $this->codeMinor = minorFieldValue::from($data->codeMinor->field);
        } catch (\ValueError $e) {
            throw new \moodle_exception('Invalid codeMinor value');
        }
    } else {
        $this->codeMinor = null;
    }
}

}
