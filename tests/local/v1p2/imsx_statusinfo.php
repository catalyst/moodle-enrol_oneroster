<?php
namespace enrol_oneroster\local\v1p2;

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
use moodle_exception;

class imsx_statusinfo {
    /** @var codeMajor */
    public $codeMajor;
    /** @var codeMinor */
    public $codeMinor;
    /** @var string */
    public $description;
    /** @var severity */
    public $severity;

    public function __construct(object $data) {
        if (empty($data->codeMajor)) {
            throw new moodle_exception('Missing codeMajor');
        }
        if (!codeMajor::is_valid($data->codeMajor)) {
            throw new moodle_exception('Invalid codeMajor: ' . $data->codeMajor);
        }

        if (empty($data->severity) || !severity::is_valid($data->severity)) {
            throw new moodle_exception('Invalid severity: ' . $data->severity);
        }

        if (empty($data->codeMinor) || empty($data->codeMinor->fieldName) || empty($data->codeMinor->fieldValue)) {
            throw new moodle_exception('Invalid codeMinor');
        }

        if (!minorFieldValue::is_valid($data->codeMinor->fieldValue)) {
            throw new moodle_exception('Invalid codeMinor fieldValue: ' . $data->codeMinor->fieldValue);
        }

        $this->codeMajor   = codeMajor::from($data->codeMajor);
        $this->description = $data->description ?? '';
        $this->severity    = severity::from($data->severity);

        $this->codeMinor = new codeMinor(
            $data->codeMinor->fieldName,
            $data->codeMinor->fieldValue
        );
    }
}
