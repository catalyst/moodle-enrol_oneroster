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
 * class statusInfo.
 * Main status info object that combines all the status information into a centralised unit.
 * @package    enrol_oneroster
 * @copyright  QUT Capstone Team - Abhinav Gandham, Harrison Dyba, Jonathon Foo Khushi Patel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster\local\v1p2\statusinfo_relations;

class statusInfo {
    private codeMajor $codeMajor;
    private ?codeMinor $codeMinor;
    private severity $severity;
    private ?string $description;

    public function __construct(
        codeMajor $codeMajor,
        ?codeMinor $codeMinor = null,
        severity $severity,
        ?string $description = null
    ) {
        $this->codeMajor = $codeMajor;
        $this->codeMinor = $codeMinor;
        $this->severity = $severity;
        $this->description = $description;
    }

    public function getCodeMajor(): codeMajor {
        return $this->codeMajor;
    }

    public function getCodeMinor(): ?codeMinor {
        return $this->codeMinor;
    }

    public function getSeverity(): severity {
        return $this->severity;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function toArray(): array {
        $result = [
            'imsx_codeMajor' => $this->codeMajor->value,
            'imsx_severity' => $this->severity->value,
        ];

        if ($this->codeMinor !== null) {
            $result['imsx_CodeMinor'] = $this->codeMinor->toArray();
        }

        if ($this->description !== null) {
            $result['imsx_description'] = $this->description;
        }

        return $result;
    }

    public static function success(?string $description = null): self {
        return new self(
            codeMajor::success,
            null,
            severity::status,
            $description
        );
    }

    public static function failure(severity $severity, codeMinor $codeMinor, ?string $description = null) : self {
        return new self(
            codeMajor::failure,
            $codeMinor,
            $severity,
            $description
        );
    }

    public static function processing(?string $description = null): self {
        return new self(
            codeMajor::processing,
            null,
            severity::status,
            $description
        );
    }

    public static function unsupported(?string $description = null): self {
        return new self(
            codeMajor::unsupported,
            null,
            severity::warning,
            $description
        );
    }

}