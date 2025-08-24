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

class minor

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/lib/moodlelib.php');
require_once(__DIR__ . '/codeMajor.php');
require_once(__DIR__ . '/severity.php');
require_once(__DIR__ . '/minorFieldValue.php');
require_once(__DIR__ . '/codeMinor.php');

/**
 * IMSX Status Info implementation for OneRoster.
 *
 * @package    enrol_oneroster
 */
class imsx_statusinfo {

    /** @var codeMajor */
    public codeMajor $codeMajor;

    /** @var severity */
    public severity $severity;

    /** @var string */
    public string $description;

    /** @var codeMinor */
    public codeMinor $codeMinor;

    /**
     * Constructor.
     *
     * @param \stdClass $data Input status info data
     * @throws \moodle_exception if validation fails
     */
    public function __construct(\stdClass $data) {
        // Validate codeMajor.
        if (empty($data->codeMajor)) {
            throw new \moodle_exception('Missing codeMajor in imsx_statusinfo');
        }

        try {
            $this->codeMajor = codeMajor::from($data->codeMajor);
        } catch (\ValueError $e) {
            throw new \moodle_exception('Invalid codeMajor: ' . $data->codeMajor);
        }

        // Validate severity.
        if (empty($data->severity)) {
            throw new \moodle_exception('Missing severity in imsx_statusinfo');
        }

        try {
            $this->severity = severity::from($data->severity);
        } catch (\ValueError $e) {
            throw new \moodle_exception('Invalid severity: ' . $data->severity);
        }

        // Description is optional.
        $this->description = $data->description ?? '';

        // Validate codeMinor.
        if (empty($data->codeMinor) || !is_object($data->codeMinor)) {
            throw new \moodle_exception('Missing or invalid codeMinor in imsx_statusinfo');
        }
        $this->codeMinor = new codeMinor($data->codeMinor);
    }
}
