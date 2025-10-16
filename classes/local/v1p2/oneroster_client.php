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
use enrol_oneroster\local\interfaces\course_representation;
use enrol_oneroster\local\interfaces\coursecat_representation;
use enrol_oneroster\local\interfaces\user_representation;
use enrol_oneroster\local\interfaces\enrollment_representation;

use enrol_oneroster\local\collections\orgs as orgs_collection;
use enrol_oneroster\local\collections\schools as schools_collection;
use enrol_oneroster\local\collections\terms as terms_collection;
use enrol_oneroster\local\v1p1\endpoints\rostering as rostering_endpoint;
use enrol_oneroster\local\entities\org as org_entity;
use enrol_oneroster\local\entities\school as school_entity;
use enrol_oneroster\local\v1p2\entities\user as user_entity;
use enrol_oneroster\local\entities\user as root_user_entity;



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
use context_user;
use DateTime;

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
    
    /**
     * Sync the roster.
     *
     * @param   int $onlysincetime
     */
    public function sync_roster(?int $onlysincetime = null): void {
        global $DB;

        // Most systems do not have many organisations in them.
        // Fetch all organisations to add them to the cache.
        $this->fetch_organisation_list();

        $schoolidstosync = explode(',', get_config('enrol_oneroster', 'datasync_schools'));
        $countofschools = count($schoolidstosync);
        $this->get_trace()->output("Processing {$countofschools} schools");

        $onlysince = null;
        if ($onlysincetime) {
            // Only fetch users last modified in the onlysince period.
            $onlysince = new DateTime();
            $onlysince->setTimestamp($onlysincetime);
        }

        // Synchronise all users.
        // One Roster does not provide a way of fetching users relating to a specific school.
        // All users for all supported schools will be created first.
        $this->get_trace()->output("Updating the user roster", 1);

        // Only fetch users last modified in the past day.
        // All timezones in One Roster are Zulu.
        $this->sync_users_in_schools($schoolidstosync, $onlysince);

        // Fetch the details of all enrolment instances before running the sync.
        $this->cache_enrolment_instances();

        $this->fetch_current_enrolment_data();

        // Synchronise all courses, classes, and enrolments.
        foreach ($schoolidstosync as $schoolidtosync) {
            $this->get_trace()->output("Fetching school with sourcedId '{$schoolidtosync}'", 2);
            $school = $this->get_container()->get_entity_factory()->fetch_org_by_id($schoolidtosync);
            if ($school instanceof school_entity) {
                $this->get_trace()->output("Synchronising school '{$schoolidtosync}'", 2);
                $this->sync_school($school, $onlysince);
            } else {
                $this->get_trace()->output("Organisation with sourcedId '{$schoolidtosync}' is not a school. Skipping.", 3);
            }
        }

        $this->get_trace()->output("Processing unenrolments", 3);
        foreach ($this->existingroleassignments as $instanceid => $ra) {
            $instance = $DB->get_record('enrol', ['id' => $instanceid]);
            if ($instance === null) {
                $this->get_trace()->output("No enrolment instance found with id {$instanceid}");
                continue;
            }

            $context = \context_course::instance($instance->courseid);

            // Unassign roles for this user.
            foreach ($ra as $userid => $roleids) {
                foreach (array_keys($roleids) as $roleid) {
                    if ($roleid) {
                        role_unassign($roleid, $userid, $context->id, 'enrol_oneroster', $instance->id);
                    }
                }
            }

            // Unenrol the user if they have no remaining roles in this enrolment instance.
            // Note: A manual enrolment in the same course is a separate instance.
            $this->get_plugin_instance()->unenrol_user(
                $instance,
                $userid
            );
        }

        $this->get_trace()->output("Completed synchronisation of Rostering information");
        $this->get_trace()->output(sprintf("Entity\t\tCreate\tUpdate\tDelete"), 1);
        foreach ($this->get_metrics() as $thing => $actions) {
            $this->get_trace()->output(
                sprintf(
                    "Entity '%s'\t%d\t%d\t%d",
                    $thing,
                    $actions['create'],
                    $actions['update'],
                    $actions['delete']
                ),
                1
            );
        }
    }

    /**
     * Synchronise user agents for a user.
     *
     * @param   user_entity $entity The user to sync agents for
     * @param   stdClass $localuser The local record for the user
     */
    protected function sync_user_agents(root_user_entity $entity, stdClass $localuser): void {

        $roles = $entity->get('roles');

        $student = false;

        foreach(array_values($roles) as $role){
            if ($role->role == 'student') {
                $student = true;
                continue;
            }
        }
        // Only applied for students as per section 4.1.2 of the specification.
        if(!$role || !$student) return;

        $localusercontext = context_user::instance($localuser->id);
            
        // Create a mapping of userid => [roleid] for current user agents.
        $localuseragents = [];
        foreach (get_users_roles($localusercontext, [], false) as $userid => $roleassignments) {
            foreach (array_values($roleassignments) as $ra) {
                if ($ra->component === 'enrol_oneroster') {
                    if (!array_key_exists($userid, $localuseragents)) {
                        $localuseragents[$userid] = [];
                    }
                    $localuseragents[$userid][$ra->roleid] = true;
                }
            }
        }
        
        // Update remote user agents.
        foreach ($entity->get_agent_entities() as $remoteagent) {
            if (!$remoteagent) {
                continue;
            }

            // Ensure that the local user exists.
            $localagent = $this->update_or_create_user($remoteagent);
            if (!$localagent) {
                // Unable to create the local agent.
                $this->get_trace()->output(sprintf(
                    "Unable to assign %s (%s) as a %s of %s (%s). Local user not found.",
                    $remoteagent->get('username'),
                    $remoteagent->get('idnumber'),
                    $role->get('role'),
                    $entity->get('username'),
                    $entity->get('idnumber')
                ), 4);
                continue;
            }

            // Fetch the local role for the remote agent.
            foreach(array_values($roles) as $role){
                $roleid = $this->get_role_mapping($role -> get('role'), CONTEXT_USER);
                if (!$roleid) {
                    // No local mapping for this role.
                    $this->get_trace()->output(sprintf(
                        "Unable to assign %s (%s) as a %s of %s (%s). Role mapping not found.",
                        $remoteagent->get('username'),
                        $remoteagent->get('idnumber'),
                        $role->get('role'),
                        $entity->get('username'),
                        $entity->get('idnumber')
                    ), 4);
                    continue;
                }

                $assignrole = !array_key_exists($localagent->id, $localuseragents);
                $assignrole = $assignrole || !array_key_exists($roleid, $localuseragents[$localagent->id]);

                if ($assignrole) {
                    // Assign the role.
                    role_assign($roleid, $localagent->id, $localusercontext, 'enrol_oneroster');
                    $this->get_trace()->output(sprintf(
                        "Assigned %s (%s) as a %s of %s (%s).",
                        $remoteagent->get('username'),
                        $remoteagent->get('idnumber'),
                        $role->get('role'),
                        $entity->get('username'),
                        $entity->get('idnumber')
                    ), 4);
                    $this->add_metric('user_mapping', 'create');
                } else {
                    // Unset the local agent mapping.
                    unset($localuseragents[$localagent->id][$roleid]);
                }

            }
        }
        
        // Unenrol stale mappings.
        foreach ($localuseragents as $localagentid => $localagentroles) {
            foreach ($localagentroles as $roleid) {
                $this->get_trace()->output(sprintf(
                    "Unasssigned user with id %s from being a %s of %s (%s).",
                    $localagentid,
                    $roleid,
                    $localuser->username,
                    $localuser->idnumber
                ), 4);
                role_unassign($roleid, $localagentid, $localusercontext, 'enrol_oneroster');
                $this->add_metric('user_mapping', 'delete');
            }
        }
    }

    /**
     * Synchronise all users in the Schools.
     *
     * @param   int[] $schoolids
     * @param   DateTime|null $onlysince Only sync users which have been remotely modified since the specified date
     */
    public function sync_users_in_schools(array $schoolids, ?DateTime $onlysince = null): void {
        $filter = null;
        if ($onlysince) {
            // Only fetch users last modified in the onlysince period.
            $filter = new filter('dateLastModified',  $onlysince->format('o-m-d'), '>');
        }
        // Note: Some Endpoints do not sort properly on Array properties.
        $users = $this->get_container()->get_collection_factory()->get_users(
            [],
            $filter,
            function($data) use ($schoolids) {
                $roles = $data->get('roles');
                $roleids = [];

                foreach ($roles as $role) {
                    $orgid = $role->orgSourcedId;
                    if (!empty($orgid)) {
                        $roleids[] = $orgid;
                    }
                }

            $foundids = array_unique($roleids);
            return !!count(array_intersect($schoolids, $foundids));
            }
        );
        $usercount = 0;
        foreach ($users as $user) {
            $this->update_or_create_user($user);
            $usercount++;
        }
        $this->get_trace()->output("Finished processing users. Processed {$usercount} users", 3);
    }

    /**
     * Update or create a Moodle User based on an entity representing a user.
     *
     * @param   user_representation $entity An entity representing a user category
     * @return  stdClass
     */
    protected function update_or_create_user(user_representation $entity): stdClass {
        global $CFG, $DB;

        // Note: This is _usually_ the responsibility of an authentication plugin but One Roster can work with different
        // authentication sources which do not know anything about One Roster.
        require_once("{$CFG->dirroot}/user/lib.php");

        // Fetch the user representation for this entity.
        $remoteuser = $entity->get_user_data();
        $remoteuser->auth = $this->get_config_setting('newuser_auth');
        $remoteuser->confirmed = true;

        if ($this->get_user_mapping($remoteuser->idnumber)) {
            $localuser = $this->update_existing_user($entity, $remoteuser);
        } else {
            // Create a new uesr.
            $localuser = $this->create_new_user($entity, $remoteuser);
        }

        // See whether this user is an agent for any other user.
        // Note: This is only applied for students as per section 4.1.2 of the specification.
        $this->sync_user_agents($entity, $localuser);

        return $localuser;
    }

}

