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
 * One Roster Enrolment Client.
 *
 * @package    enrol_oneroster
 * @copyright  QUT Capstone Team - Abhinav Gandham, Harrison Dyba, Jonathon Foo, Kushi Patel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster\local\v1p2;

// Entities which represent Moodle objects.
use enrol_oneroster\local\v1p1\oneroster_client as client_version_one;
use enrol_oneroster\local\v1p2\responses\default_response;
use enrol_oneroster\local\v1p2\statusinfo_relations\status_info;
use enrol_oneroster\local\command;
use enrol_oneroster\local\interfaces\filter;
use enrol_oneroster\client_helper;
use BadMethodCallException;
use moodle_exception;
use moodle_url;
use stdClass;

/**
 * One Roster v1p2 client.
 *
 * @package    enrol_oneroster
 * @copyright  QUT Capstone Team - Abhinav Gandham, Harrison Dyba, Jonathon Foo, Kushi Patel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait oneroster_client
{
    use client_version_one;

    /**
     * Execute the supplied command.
     *
     * @param   command $command The command to execute
     * @param   filter $filter
     * @return  stdClass
     */
    public function execute(command $command, filter $filter = null): stdClass {
        try {
            $url = new moodle_url($command->get_url($this->baseurl));
            $params = $command->get_params();
            $method = $command->get_method();
            $sort = $command->get_sort();

            // Add pagination.
            if (array_key_exists('limit', $params)) {
                $url->param('limit', $params['limit']);
                unset($params['limit']);
            }

            if (array_key_exists('offset', $params)) {
                $url->param('offset', $params['offset']);
                unset($params['offset']);
            }

            // Sorting.
            if ($sort) {
                $url->param('sort', $sort);
                if ($sortorder = $command->get_sort_order()) {
                    $url->param('orderBy', $sortorder);
                }
            }

            // Filtering.
            if ($filter && !empty((string) $filter)) {
                $url->param('filter', (string) $filter);
            }

            // Fields.
            if (array_key_exists('fields', $params)) {
                $url->param('fields', (string) $params['fields']);
                unset($params['fields']);
            }

            if (!empty($params) && $method !== client_helper::POST) {
                throw new BadMethodCallException(sprintf(
                    'The http method called for %s is %s but it has to be POST' .
                    ' if you want to pass the JSON params %s',
                    $url,
                    $method,
                    json_encode($params)
                ));
            }

            $options = [
                'CURLOPT_CONNECTTIMEOUT' => 300,
            ];

            if ($method === client_helper::POST) {
                $result = $this->post($url->out(false), $params, $options);
            } else if ($method === client_helper::GET) {
                $result = $this->get($url->out(false), $params, $options);
            }

            $info = $this->get_request_info();
            \enrol_oneroster\local\exceptions\exception::check_and_throw_from_http_response($result, $info, $url, $params);

            $response = json_decode($result);

            if (is_null($response)) {
                throw new moodle_exception("Could not decode JSON token response: " . $result);
            }

            if (!empty($response->error)) {
                throw new moodle_exception($response->error . ' ' . $response->error_description);
            }

            // Check if response already has status info (from external API)
            if (isset($response->imsx_statusInfo)) {
                // Validate existing status info from external API
                if (!$this->validate_status_info($response)) {
                    throw new moodle_exception("Invalid status info found in the response");
                }
            } else {
                // Throw an exception if there is no status info in the response.
                throw new moodle_exception("No status info found in the response");
            }

            return (object) [
                'info' => $info,
                'response' => $response,
            ];
        } catch (\Exception $error) {
            // Create error status info for any exceptions
            $error_response = $this->create_error_response_with_status_info($error);
            return (object) [
                'info' => $this->get_request_info(),
                'response' => $error_response,
            ];
        }
    }

    /**
     * Function that validates the status info section of the response.
     * @param stdClass $response The response from the external API.
     * @return bool True if the status info is valid, false otherwise.
     */
    private function validate_status_info(stdClass $response): bool {
        // Check if the status info is an object.
        if (!is_object($response->imsx_statusInfo)) {
            return false;
        }

        // Transform the status info object into an array for further validation.
        $status_info = (array) $response->imsx_statusInfo;

        // Validate the code major, severity, and code minor.
        $code_major_valid = $this->validate_code_major($status_info);
        $severity_valid = $this->validate_severity($status_info);
        $code_minor_valid = $this->validate_code_minor($status_info);

        // If any of the validation fails, return false.
        if (!$code_major_valid || !$severity_valid || !$code_minor_valid) {
            return false;
        }

        return true;
    }

    /**
     * Function that validates the code major section of the status info object type.
     * @param array $status_info The status info object type.
     * @return bool True if the code major section is valid, false otherwise.
     */
    private function validate_code_major(array $status_info): bool {
        // Check required fields exist
        if (!isset($status_info['imsx_codeMajor'])) {
            return false;
        }

        // Validate code major values
        $valid_code_majors = status_info::valid_code_majors;
        if (!in_array($status_info['imsx_codeMajor'], $valid_code_majors)) {
            throw new moodle_exception(status_info::invalid_code_major_message);
        }

        return true;
    }

    /**
     * Function that validates the severity section of the status info object type.
     * @param array $status_info The status info object type.
     * @return bool True if the severity section is valid, false otherwise.
     */
    private function validate_severity(array $status_info): bool {
        // Validate severity if present
        if (isset($status_info['imsx_severity'])) {
            $valid_severities = status_info::valid_severities;
            if (!in_array($status_info['imsx_severity'], $valid_severities)) {
                throw new moodle_exception(status_info::invalid_severity_message);
            }
        }

        return true;
    }

    /**
     * Function that validates the code minor section of the status info object type.
     * @param array $status_info The status info object type.
     * @return bool True if the code minor section is valid, false otherwise.
     */
    private function validate_code_minor(array $status_info): bool {
        // Checking if the code major is failure.
        if ($status_info['imsx_codeMajor'] === 'failure') {
            // If code major is failure, checking if the code minor section is present.
            if (!isset($status_info['imsx_CodeMinor'])) {
                throw new moodle_exception(status_info::no_code_minor_meesage);
            }

            // Convert to array if it's an object (from JSON response)
            $code_minor = is_object($status_info['imsx_CodeMinor']) ? (array) $status_info['imsx_CodeMinor'] : $status_info['imsx_CodeMinor'];

            // If the code minor section is present, checking if the structure is valid.
            if (!is_array($code_minor) || !isset($code_minor['imsx_codeMinorField'])) {
                // If the structure is not valid, throw a moodle exception.
                throw new moodle_exception(status_info::invalid_code_minor_structure_message);
            }

            // Validate the actual code minor values
            $valid_code_minors = status_info::valid_code_minors;
            $code_minor_fields = $code_minor['imsx_codeMinorField'];

            if (is_array($code_minor_fields)) {
                foreach ($code_minor_fields as $field) {
                    // Convert to array if it's an object
                    $field_array = is_object($field) ? (array) $field : $field;
                    if (is_array($field_array) && isset($field_array['imsx_codeMinorFieldValue'])) {
                        if (!in_array($field_array['imsx_codeMinorFieldValue'], $valid_code_minors)) {
                            throw new moodle_exception(status_info::invalid_code_minor_message);
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * Function that creates an error response with status info using the default_response class.
     * @param error The exception that occurred
     * @param command The command that was executed
     * @return stdClass The error response with status info
     */
    private function create_error_response_with_status_info(\Exception $error): stdClass {
        $status_code = $error->getCode();
        $code_minor = $this->get_code_minor_for_status($status_code);

        // Convert array to proper codeMinor object
        $code_minor_field = new \enrol_oneroster\local\v1p2\statusinfo_relations\code_minor_field(
            $code_minor['imsx_codeMinorField'][0]['imsx_codeMinorFieldName'],
            \enrol_oneroster\local\v1p2\statusinfo_relations\code_minor_values::from($code_minor['imsx_codeMinorField'][0]['imsx_codeMinorFieldValue'])
        );

        $code_minor_obj = new \enrol_oneroster\local\v1p2\statusinfo_relations\code_minor($code_minor_field);

        // Create a failure response with status info
        $default_response = default_response::failure(
            \enrol_oneroster\local\v1p2\statusinfo_relations\severity::error,
            $code_minor_obj,
            $error->getMessage()
        );

        // Convert the response to an object to maintain consistency
        return (object) $default_response->to_array();
    }

    /**
     * Function that creates a specific code minor based on the http status code.
     * @param statusCode The http status code
     * @return array The code minor information array
     */
    private function get_code_minor_for_status(int $status_code): array {
        return match ($status_code) {
            400 => ['imsx_codeMinorField' => [
                ['imsx_codeMinorFieldName' => 'TargetEndSystem', 'imsx_codeMinorFieldValue' => 'invaliddata']
            ]],
            401 => ['imsx_codeMinorField' => [
                ['imsx_codeMinorFieldName' => 'TargetEndSystem', 'imsx_codeMinorFieldValue' => 'unauthorisedrequest']
            ]],
            403 => ['imsx_codeMinorField' => [
                ['imsx_codeMinorFieldName' => 'TargetEndSystem', 'imsx_codeMinorFieldValue' => 'forbidden']
            ]],
            404 => ['imsx_codeMinorField' => [
                ['imsx_codeMinorFieldName' => 'TargetEndSystem', 'imsx_codeMinorFieldValue' => 'unknownobject']
            ]],
            429 => ['imsx_codeMinorField' => [
                ['imsx_codeMinorFieldName' => 'TargetEndSystem', 'imsx_codeMinorFieldValue' => 'server_busy']
            ]],
            500 => ['imsx_codeMinorField' => [
                ['imsx_codeMinorFieldName' => 'TargetEndSystem', 'imsx_codeMinorFieldValue' => 'internal_server_error']
            ]],
            default => ['imsx_codeMinorField' => [
                ['imsx_codeMinorFieldName' => 'TargetEndSystem', 'imsx_codeMinorFieldValue' => 'internal_server_error']
            ]]
        };
    }
}
