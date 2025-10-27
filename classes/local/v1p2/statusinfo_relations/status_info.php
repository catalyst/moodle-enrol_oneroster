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


namespace enrol_oneroster\local\v1p2\statusinfo_relations;

/**
 * enum codeMajor.
 * Defines the four possible code major status codes for any OneRoster operation.
 * @package    enrol_oneroster
 * @copyright  QUT Capstone Team - Abhinav Gandham, Harrison Dyba, Jonathon Foo, Khushi Patel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
enum code_major: string
{
    case success = 'success';
    case processing = 'processing';
    case failure = 'failure';
    case unsupported = 'unsupported';
}

/**
 * enum severity.
 * Describes the severity level of the status info response.
 * @package    enrol_oneroster
 * @copyright  QUT Capstone Team - Abhinav Gandham, Harrison Dyba, Jonathon Foo, Khushi Patel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
enum severity: string
{
    case status = 'status';
    case warning = 'warning';
    case error = 'error';
}
/**
 * class statusInfo.
 * Main status info object that combines all the status information into a centralised unit.
 * @package    enrol_oneroster
 * @copyright  QUT Capstone Team - Abhinav Gandham, Harrison Dyba, Jonathon Foo Khushi Patel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class status_info
{
    /** Constants for containing relevant error messages. */
    const valid_code_majors = ['success', 'failure', 'processing', 'unsupported'];
    const valid_severities = ['status', 'warning', 'error'];
    const valid_code_minors = ['fullsuccess', 'invalid_filter_field', 'invalid_selection_field', 'invaliddata', 'unauthorisedrequest', 'forbidden', 'server_busy', 'unknownobject', 'internal_server_error'];
    const invalid_code_major_message = 'INVALID STRUCTURE: Invalid code major value found in the response, values must be either success, failure, processing, or unsupported';
    const invalid_severity_message = 'INVALID STRUCTURE: Invalid severity value found in the response, values must be either status, warning, or error';
    const no_code_minor_message = 'INVALID STRUCTURE: Failure status info must have a code minor';
    const invalid_code_minor_structure_message = 'INVALID STRUCTURE: Invalid code minor structure found in the response';
    const invalid_code_minor_message = 'INVALID STRUCTURE: Invalid code minor value found in the response, values must be either fullsuccess, invalid_filter_field, invalid_selection_field, invaliddata, unauthorisedrequest, forbidden, server_busy, unknownobject, or internal_server_error';

    public function __construct(
        private code_major $code_major,
        private severity $severity,
        private ?code_minor $code_minor = null,
        private ?string $description = null
    ) {}

    /**
     * Method that returns the code major of the status info object type.
     *
     * @return code_major The code major of the status info object type.
     */
    public function get_code_major(): code_major {
        return $this->code_major;
    }

    /**
     * Method that returns the code minor of the status info object type.
     *
     * @return code_minor The code minor of the status info object type.
     */
    public function get_code_minor(): ?code_minor {
        return $this->code_minor;
    }

    /**
     * Method that returns the severity of the status info object type.
     *
     * @return severity The severity of the status info object type.
     */
    public function get_severity(): severity {
        return $this->severity;
    }

    /**
     * Method that returns the description of the status info object type.
     *
     * @return string|null The description of the status info object type.
     */
    public function get_description(): ?string {
        return $this->description;
    }

    /**
     * Method that converts the status info object type to an array.
     *
     * @return array The array representation of the status info object type.
     */
    public function to_array(): array {
        $result = [
            'imsx_codeMajor' => $this->code_major->value,
            'imsx_severity' => $this->severity->value,
        ];

        if ($this->code_minor !== null) {
            $result['imsx_CodeMinor'] = $this->code_minor->to_array();
        }

        if ($this->description !== null) {
            $result['imsx_description'] = $this->description;
        }

        return $result;
    }

}
