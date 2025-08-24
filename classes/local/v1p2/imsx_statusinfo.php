<?php
namespace enrol_oneroster\classes\local\v1p2;
require_once(__DIR__ . '/enums.php');
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
 * Enum classes assumed to exist:
 * codeMajor, severity, minorFieldValue
 */

class imsx_statusinfo {

    public codeMajor $codeMajor;
    public severity $severity;
    public string $description;
    public ?minorFieldValue $codeMinor;

   public function __construct(object $data) {

    // codeMajor
    try {
        $this->codeMajor = codeMajor::from($data->codeMajor ?? '');
    } catch (\ValueError | \TypeError $e) {
        throw new \moodle_exception('Invalid or missing codeMajor');
    }

    // severity
    try {
        $this->severity = severity::from($data->severity ?? '');
    } catch (\ValueError | \TypeError $e) {
        throw new \moodle_exception('Invalid or missing severity');
    }

    // description
    $this->description = $data->description ?? '';

    // codeMinor
    if (isset($data->codeMinor->field)) {
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