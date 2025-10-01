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

use DateTime;
use context_user;
use core_course_category;
use core_user;
use enrol_oneroster\local\converter;

// Client and associated features.
use enrol_oneroster\local\interfaces\container as container_interface;
use enrol_oneroster\local\interfaces\rostering_endpoint as rostering_endpoint_interface;

// Entities which represent Moodle objects.
use enrol_oneroster\local\interfaces\course_representation;
use enrol_oneroster\local\interfaces\coursecat_representation;
use enrol_oneroster\local\interfaces\user_representation;
use enrol_oneroster\local\interfaces\enrollment_representation;

use enrol_oneroster\local\v1p2\endpoints\rostering as rostering_endpoint;
use enrol_oneroster\local\entities\school as school_entity;
use enrol_oneroster\local\entities\user as user_entity;
use enrol_oneroster\local\v1p1\oneroster_client as client_version_one;
use enrol_oneroster\local\v1p2\responses\default_response;
use enrol_oneroster\local\v1p2\statusinfo_relations\statusInfo;
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
trait oneroster_client {
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
                if (!$this->validateStatusInfo($response)) {
                    throw new moodle_exception("Invalid status info found in the response");
                }
            } else {
                // Create our own status info for successful responses
                $response = $this->createSuccessResponseWithStatusInfo($response, $command);
            }

            return (object) [
                'info' => $info,
                'response' => $response,
            ];
        } catch (\Exception $e) {
            // Create error status info for any exceptions
            $errorResponse = $this->createErrorResponseWithStatusInfo($e, $command);
            return (object) [
                'info' => $this->get_request_info(),
                'response' => $errorResponse,
            ];
        }
    }

    private function validateStatusInfo(stdClass $response): bool {
        // Check if the status info is an object.
        if (!is_object($response->imsx_statusInfo)) {
            return false;
        }

        // Transform the status info object into an array for further validation.
        $statusInfo = (array) $response->imsx_statusInfo;

        // Validate the code major, severity, and code minor.
        $codeMajorValid = $this->validateCodeMajor($statusInfo);
        $severityValid = $this->validateSeverity($statusInfo);
        $codeMinorValid = $this->validateCodeMinor($statusInfo);

        // If any of the validation fails, return false.
        if (!$codeMajorValid || !$severityValid || !$codeMinorValid) {
            return false;
        }

        return true;
    }

    private function validateCodeMajor(array $statusInfo): bool {
        // Check required fields exist
        if (!isset($statusInfo['imsx_codeMajor'])) {
            return false;
        }

        // Validate code major values
        $validCodeMajors = statusInfo::validCodeMajors;
        if (!in_array($statusInfo['imsx_codeMajor'], $validCodeMajors)) {
            throw new moodle_exception(statusInfo::invalidCodeMajorMessage);
            return false;
        }

        return true;
    }
    private function validateSeverity(array $statusInfo): bool {
        // Validate severity if present
        if (isset($statusInfo['imsx_severity'])) {
            $validSeverities = statusInfo::validSeverities;
            if (!in_array($statusInfo['imsx_severity'], $validSeverities)) {
                throw new moodle_exception(statusInfo::invalidSeverityMessage);
                return false;
            }
        }

        return true;
    }

    private function validateCodeMinor(array $statusInfo): bool {
        // Checking if the code major is failure.
        if ($statusInfo['imsx_codeMajor'] === 'failure') {
            // If code major is failure, checking if the code minor section is present.
            if (!isset($statusInfo['imsx_CodeMinor'])) {
                throw new moodle_exception(statusInfo::noCodeMinorMessage);
                return false;
            }

            // Convert to array if it's an object (from JSON response)
            $codeMinor = is_object($statusInfo['imsx_CodeMinor']) ? (array) $statusInfo['imsx_CodeMinor'] : $statusInfo['imsx_CodeMinor'];

            // If the code minor section is present, checking if the structure is valid.
            if (!is_array($codeMinor) || !isset($codeMinor['imsx_codeMinorField'])) {
                // If the structure is not valid, throw a moodle exception.
                throw new moodle_exception(statusInfo::invalidCodeMinorStructureMessage);
                return false;
            }

            // Validate the actual code minor values
            $validCodeMinors = statusInfo::validCodeMinors;
            $codeMinorFields = $codeMinor['imsx_codeMinorField'];

            if (is_array($codeMinorFields)) {
                foreach ($codeMinorFields as $field) {
                    // Convert to array if it's an object
                    $fieldArray = is_object($field) ? (array) $field : $field;
                    if (is_array($fieldArray) && isset($fieldArray['imsx_codeMinorFieldValue'])) {
                        if (!in_array($fieldArray['imsx_codeMinorFieldValue'], $validCodeMinors)) {
                            throw new moodle_exception(statusInfo::invalidCodeMinorMessage);
                            return false;
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * Function that creates a success response with status info using the default_response class.
     */
    private function createSuccessResponseWithStatusInfo(stdClass $response, command $command): stdClass {
        $data = null;
        $collectionName = null;

        // Find the collection data
        foreach ($command->get_collection_names() as $collectionName) {
            if (property_exists($response, $collectionName)) {
                $data = $response->{$collectionName};
                break;
            }
        }

        // Convert data to array if it's an object
        if (is_object($data)) {
            $data = (array) $data;
        }

        // Create a success response with status info
        $defaultResponse = default_response::success(
            $data,
            $collectionName,
            'Request completed successfully'
        );

        // Convert the response to an object to maintain consistency
        return (object) $defaultResponse->toArray();
    }

     /**
     * Function that creates an error response with status info using the default_response class.
     */
    private function createErrorResponseWithStatusInfo(\Exception $error, command $command): stdClass {
        $statusCode = $error->getCode();
        $codeMinor = $this->getCodeMinorForStatus($statusCode);

        // Convert array to proper codeMinor object
        $codeMinorField = new \enrol_oneroster\local\v1p2\statusinfo_relations\codeMinorField(
            $codeMinor['imsx_codeMinorField'][0]['imsx_codeMinorFieldName'],
            \enrol_oneroster\local\v1p2\statusinfo_relations\codeMinorValues::from($codeMinor['imsx_codeMinorField'][0]['imsx_codeMinorFieldValue'])
        );

        $codeMinorObj = new \enrol_oneroster\local\v1p2\statusinfo_relations\codeMinor($codeMinorField);

        // Create a failure response with status info
        $defaultResponse = default_response::failure(
            \enrol_oneroster\local\v1p2\statusinfo_relations\severity::error,
            $codeMinorObj,
            $error->getMessage()
        );

        // Convert the response to an object to maintain consistency
        return (object) $defaultResponse->toArray();
    }

     /**
     * Function that creates an error status info response.
     * @return array The error status info response array
     */
    private function createErrorStatusInfo(\Exception $error): array {
        $statusCode = $error->getCode();
        $codeMinor = $this->getCodeMinorForStatus($statusCode);

        return [
            'imsx_statusInfo' => [
                'imsx_codeMajor' => 'failure',
                'imsx_severity' => 'error',
                'imsx_CodeMinor' => $codeMinor,
                'imsx_description' => $error->getMessage()
            ]
        ];
    }

     /**
     * Function that creates a specific code minor based on the http status code.
     * @return array The code minor information array
     */
    private function getCodeMinorForStatus(int $statusCode): array {
        return match ($statusCode) {
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
