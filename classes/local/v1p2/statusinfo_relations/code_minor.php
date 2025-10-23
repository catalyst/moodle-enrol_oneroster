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
 * Class codeMinor.
 * Combines multiple code minor fields into a single object.
 * @package    enrol_oneroster
 * @copyright  QUT Capstone Team - Abhinav Gandham, Harrison Dyba, Jonathon Foo, Khushi Patel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster\local\v1p2\statusinfo_relations;

class code_minor {
    private array $code_minor_fields = [];

    public function __construct(code_minor_field ...$fields) {
        $this->code_minor_fields = $fields;
    }

   public function getFields(): array {
        return $this->code_minor_fields;
    }

    public function to_array(): array {
        return [
            'imsx_codeMinorField' => array_map(fn($field) => [
                'imsx_codeMinorFieldName' => 'codeMinor',
                'imsx_codeMinorFieldValue' => $field->getFieldValue()->value
            ],
            $this->code_minor_fields
        ),
    ];
    }

   public function create_code_minor(code_minor_field $field): self {
        return new self($field);
    }

}

/**
 * Class codeMinorField.
 * Represents a single field within the code minor object.
 * @package    enrol_oneroster
 * @copyright  QUT Capstone Team - Abhinav Gandham, Harrison Dyba, Jonathon Foo, Kushi Patel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class code_minor_field {
    public function __construct(
        private code_minor_values $field_value,
        private string $fieldName = 'TargetEndSystem'
    ) {
    }

    public function getFieldName(): string {
        return $this->fieldName;
    }

    public function getFieldValue(): code_minor_values {
        return $this->field_value;
    }

    public function to_array(): array {
        return [
            'imsx_codeMinorFieldName' => $this->getFieldName(),
            'imsx_codeMinorFieldValue' => $this->getFieldValue()->value,
        ];
    }
}

/**
 * enum codeMinorValues.
 * Defines the possible code minor values for any OneRoster operation.
 * @package    enrol_oneroster
 * @copyright  QUT Capstone Team - Abhinav Gandham, Harrison Dyba, Jonathon Foo, Kushi Patel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
enum code_minor_values: string {
    case fullsuccess = 'fullsuccess';
    case invalid_filter_field = 'invalid_filter_field';
    case invalid_selection_field = 'invalid_selection_field';
    case invaliddata = 'invaliddata';
    case unauthorisedrequest = 'unauthorisedrequest';
    case forbidden = 'forbidden';
    case server_busy = 'server_busy';
    case unknownobject = 'unknownobject';
    case internal_server_error = 'internal_server_error';
}
