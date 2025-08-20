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
 * One Roster Enrolment Client.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * One Roster 1.1 Factory Manager.
 *
 * @package    enrol_oneroster
 * @copyright  QUT Capstone Team - Abhinav Gandham, Harrison Dyba, Jonathon Foo, Khushi Patel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class codeMinor {
    public string $fieldName;
    public minorFieldValue $codeMinorFieldValue;

    public function __construct(string $fieldName, string $fieldValue) {
        if (!minorFieldValue::is_valid($fieldValue)) {
            throw new \moodle_exception("Invalid codeMinor fieldValue: $fieldValue");
        }
        $this->fieldName = $fieldName;
        $this->codeMinorFieldValue = minorFieldValue::from($fieldValue);
    }
}
