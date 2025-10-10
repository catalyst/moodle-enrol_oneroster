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
    // Add new methods or override methods from v1p1 trait here.

    /**
     * Synchronise user agents for a user.
     *
     * @param   user_entity $entity The user to sync agents for
     * @param   stdClass $localuser The local record for the user
     */
    protected function sync_user_agents(user_entity $entity, stdClass $localuser): void {

        $roles = $entity->get('roles');

        $student = false;

        foreach(array_values($roles) as $role){
            if ($role->get('role') == 'student') {
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
}
