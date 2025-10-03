<?php

namespace enrol_oneroster\local\v1p2\responses;


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
 * class default_response.
 * Responsible for wrapping the status info response into a standardised response object.
 * @package    enrol_oneroster
 * @copyright  QUT Capstone Team - Abhinav Gandham, Harrison Dyba, Jonathon Foo Khushi Patel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use enrol_oneroster\local\v1p2\statusinfo_relations\status_info;
use enrol_oneroster\local\v1p2\statusinfo_relations\severity;
use enrol_oneroster\local\v1p2\statusinfo_relations\code_minor;
use enrol_oneroster\local\v1p2\statusinfo_relations\code_major;


class default_response {
    public function __construct(
        private status_info $imsx_statusInfo,
        private ?array $data = null,
        private ?string $collection_name = null
    ) {
    }

    public function get_status_info(): status_info {
        return $this->imsx_statusInfo;
    }

    public function get_data(): ?array {
        return $this->data;
    }

    public function get_collection_name(): ?string {
        return $this->collection_name;
    }

    /**
     * Method that converts the default response object type to an array.
     *
     * @return array The array representation of the default response object type.
     */
    public function to_array(): array {
        $result = [
            'imsx_statusInfo' => $this->imsx_statusInfo->to_array(),
        ];
        if ($this->get_data() !== null && $this->get_collection_name() !== null) {
            $result[$this->collection_name] = $this->data;
        }
        return $result;
    }

    /**
     * Encoding the default response array to a JSON string.
     *
     * @return string The JSON string representation of the default response object type.
     */
    public function to_json(): string {
        return json_encode($this->to_array());
    }

     /**
     * Function that creates the failure default response object type.
     *
     * @return default_response The failure default response object type.
     */
    public static function failure(
        severity $severity,
        code_minor $code_minor,
        ?string $description = null
    ) : self {
        return new self(
            status_info::failure($severity, $code_minor, $description),
            null,
            null
        );

    }

     /**
     * Function that creates the processing default response object type.
     *
     * @return default_response The processing default response object type.
     */
    public static function processing(
        ?string $description = null
    ) : self {
        return new self(
            status_info::processing($description),
            null,
            null
        );
    }

     /**
     * Function that creates the unsupported default response object type.
     *
     * @return default_response The unsupported default response object type.
     */
    public static function unsupported(
        ?string $description = null
    ) : self {
        return new self(
            status_info::unsupported($description),
            null,
            null
        );
    }

    /**
     * Function that checks if the default response object is a valid response.
     *
     * @return bool True if the default response object is a valid response, false otherwise.
     */
    public function is_info_valid(): bool {
        // Checks if there is no status info.
        if ($this->imsx_statusInfo === null) {
            return false;
        }
        // Checks if there is no data or collection name.
        if ($this->data === null || $this->collection_name === null) {
            return false;
        }
        // Checks if the status info is a failure type and if there is no data
        if ($this->imsx_statusInfo->get_code_major() === code_major::failure && $this->data !== null) {
            return false;
        }
        // Checks if the status info is a success type and if there is no collection name.
        if ($this->imsx_statusInfo->get_code_major() === code_major::success &&
        $this->data !== null && $this->collection_name === null) {
            return false;
        }

        return true;

    }
}