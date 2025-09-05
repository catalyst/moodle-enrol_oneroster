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
 * One Roster Client.
 *
 * This plugin synchronises enrolment and roles with a One Roster endpoint.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster\local;

use BadMethodCallException;
use enrol_oneroster\client_helper;
use enrol_oneroster\local\command;
use enrol_oneroster\local\interfaces\container as container_interface;
use enrol_oneroster\local\interfaces\filter;
use enrol_oneroster\local\interfaces\rostering_client;
use enrol_oneroster\local\interfaces\rostering_endpoint;
use enrol_oneroster\plugin as enrol_oneroster_plugin;
use moodle_exception;
use moodle_url;
use null_progress_trace;
use progress_trace;
use stdClass;

/**
 * One Roster Client.
 *
 * Note: This is implemented as a Trait to allow support of both OAuth1, and OAuth2, which are both implemented in
 * upstream Moodle by use of abstract classes.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait oneroster_client {
    /** @var progress_trace The logging system */
    protected $trace;

    /** @var container_interface The container used for fetching factories */
    protected $container;

    /** @var plugin An instance of the enrol_oneroster_plugin enrolment plugin */
    protected $instance;

    /** @var bool Whether to automatically wrap responses with status info */
    protected $autoStatusInfo = false;

    /**
     * Get the enrol_oneroster_plugin instance.
     *
     * @return  enrol_oneroster_plugin
     */
    protected function get_plugin_instance(): enrol_oneroster_plugin {
        if ($this->instance === null) {
            $this->instance = new enrol_oneroster_plugin();
        }

        return $this->instance;
    }

    /**
     * Set the log tracer.
     *
     * @param progress_trace $trace
     */
    public function set_trace(progress_trace $trace): void {
        $this->trace = $trace;
    }

    /**
     * Get the log tracer.
     *
     * @return progress_trace
     */
    public function get_trace(): progress_trace {
        if ($this->trace === null) {
            return new null_progress_trace();
        }

        return $this->trace;
    }

    /**
     * Enable or disable automatic status info wrapping for all execute calls.
     *
     * @param bool $enabled
     */
    public function setAutoStatusInfo(bool $enabled): void {
        $this->autoStatusInfo = $enabled;
    }

    /**
     * Check if auto status info is enabled.
     *
     * @return bool
     */
    public function isAutoStatusInfoEnabled(): bool {
        return $this->autoStatusInfo;
    }

    /**
     * Execute the supplied command.
     *
     * @param   command $command The command to execute
     * @param   filter $filter
     * @return  stdClass
     */
    public function execute(command $command, filter $filter = null): stdClass {
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

        return (object) [
            'info' => $info,
            'response' => $response,
        ];
    }

    /**
     * Execute command with the version 12 status info object.
     *
     * @param   command $command The command to execute
     * @param   filter $filter
     * @return  array OneRoster v1p2 response with status info
     */
    public function executeWithStatusInfo(command $command, filter $filter = null): array {
        try {
            $result = $this->execute($command, $filter);
            $response = $result->response;

            // Check if response already has status info
            if (isset($response->imsx_statusInfo)) {
                return (array) $response;
            }

            // Wrap in v1p2 status info response
            return $this->wrapResponseWithStatusInfo($response, $command);

        } catch (\Exception $error) {
            return $this->createErrorStatusInfo($error);
        }
    }

    /**
     * Execute command with optional status info wrapping.
     *
     * @param   command $command The command to execute
     * @param   filter $filter
     * @param   bool $withStatusInfo Whether to wrap response with OneRoster v1p2 status info
     * @return  stdClass|array
     */
    public function executeWithOptionalStatusInfo(command $command, filter $filter = null, bool $withStatusInfo = false) {
        if ($withStatusInfo || $this->autoStatusInfo) {
            return $this->executeWithStatusInfo($command, $filter);
        }

        return $this->execute($command, $filter);
    }

    /**
     * Function that wraps the response with status info.
     */
    private function wrapResponseWithStatusInfo(stdClass $response, command $command): array {
        $data = null;
        $collectionName = null;

        // Find the collection data
        foreach ($command->get_collection_names() as $collectionName) {
            if (property_exists($response, $collectionName)) {
                $data = $response->{$collectionName};
                break;
            }
        }

        return [
            'imsx_statusInfo' => [
                'imsx_codeMajor' => 'success',
                'imsx_severity' => 'status',
                'imsx_description' => 'Request completed successfully'
            ],
            $collectionName => $data
        ];
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
        switch ($statusCode) {
            case 400:
                return ['imsx_codeMinorField' => [
                    ['imsx_codeMinorFieldName' => 'TargetEndSystem', 'imsx_codeMinorFieldValue' => 'invaliddata']
                ]];
            case 401:
                return ['imsx_codeMinorField' => [
                    ['imsx_codeMinorFieldName' => 'TargetEndSystem', 'imsx_codeMinorFieldValue' => 'unauthorisedrequest']
                ]];
            case 403:
                return ['imsx_codeMinorField' => [
                    ['imsx_codeMinorFieldName' => 'TargetEndSystem', 'imsx_codeMinorFieldValue' => 'forbidden']
                ]];
            case 404:
                return ['imsx_codeMinorField' => [
                    ['imsx_codeMinorFieldName' => 'TargetEndSystem', 'imsx_codeMinorFieldValue' => 'unknownobject']
                ]];
            case 429:
                return ['imsx_codeMinorField' => [
                    ['imsx_codeMinorFieldName' => 'TargetEndSystem', 'imsx_codeMinorFieldValue' => 'server_busy']
                ]];
            default:
                return ['imsx_codeMinorField' => [
                    ['imsx_codeMinorFieldName' => 'TargetEndSystem', 'imsx_codeMinorFieldValue' => 'internal_server_error']
                ]];
        }
    }

    /**
     * Get the entity factory for this One Roster implementation.
     *
     * @return  container_interface
     */
    abstract public function get_container(): container_interface;

    /**
     * Perform all availilable synchronisations.
     *
     * @param   int $onlysincetime
     */
    public function synchronise(?int $onlysincetime = null): void {
        if (class_implements($this, rostering_client::class)) {
            $this->sync_roster($onlysincetime);
        }
    }

    /**
     * Authenticate against the One Roster endpoint as required.
     */
    abstract public function authenticate(): void;
}
