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

use enrol_oneroster\local\v1p2\statusinfo_relations\statusInfo;
use enrol_oneroster\local\v1p2\statusinfo_relations\severity;
use enrol_oneroster\local\v1p2\statusinfo_relations\codeMinor;
use enrol_oneroster\local\v1p2\statusinfo_relations\codeMajor;


class default_response {
    // Properties
    private statusInfo $imsx_statusInfo;
    private ?array $data = null;
    private ?string $collectionName = null;

    public function __construct(
        statusInfo $imsx_statusInfo,
        ?array $data = null,
        ?string $collectionName = null
    ) {
        $this->imsx_statusInfo = $imsx_statusInfo;
        $this->data = $data;
        $this->collectionName = $collectionName;
    }

    public function getstatusInfo(): statusInfo {
        return $this->imsx_statusInfo;
    }

    public function getData(): ?array {
        return $this->data;
    }

    public function getCollectionName(): ?string {
        return $this->collectionName;
    }

    /**
     * Method that converts the default response object type to an array.
     *
     * @return array The array representation of the default response object type.
     */
    public function toArray(): array {
        $result = [
            'imsx_statusInfo' => $this->imsx_statusInfo->toArray(),
        ];
        if ($this->getData() !== null && $this->getCollectionName() !== null) {
            $result[$this->collectionName] = $this->data;
        }
        return $result;
    }

    /**
     * Encoding the default response array to a JSON string.
     *
     * @return string The JSON string representation of the default response object type.
     */
    public function toJSON(): string {
        return json_encode($this->toArray());
    }

     /**
     * Function that creates the success default response object type.
     *
     * @return default_response The success default response object type.
     */
    public static function success(
        ?array $data = null,
        ?string $collectionName = null,
        ?string $description = null
    ): self {
        return new self(
            statusInfo::success($description),
            $data,
            $collectionName
        );
    }

     /**
     * Function that creates the failure default response object type.
     *
     * @return default_response The failure default response object type.
     */
    public static function failure(
        severity $severity,
        codeMinor $codeMinor,
        ?string $description = null
    ) : self {
        return new self(
            statusInfo::failure($severity, $codeMinor, $description),
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
            statusInfo::processing($description),
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
            statusInfo::unsupported($description),
            null,
            null
        );
    }

    /**
     * Function that checks if the default response object is a valid response.
     *
     * @return bool True if the default response object is a valid response, false otherwise.
     */
    public function isInfoValid(): bool {
        // Checks if there is no status info.
        if ($this->imsx_statusInfo === null) {
            return false;
        }
        // Checks if there is no data or collection name.
        if ($this->data === null || $this->collectionName === null) {
            return false;
        }
        // Checks if the status info is a failure type and if there is no data
        if ($this->imsx_statusInfo->getCodeMajor() === codeMajor::failure && $this->data !== null) {
            return false;
        }
        // Checks if the status info is a success type and if there is no collection name.
        if ($this->imsx_statusInfo->getCodeMajor() === codeMajor::success &&
        $this->data !== null && $this->collectionName === null) {
            return false;
        }

        return true;

    }




}