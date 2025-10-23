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

namespace enrol_oneroster\local\v1p2;

use enrol_oneroster\local\v1p1\csv_client as csv_client_version_one;
use enrol_oneroster\local\v1p2\oneroster_client as versioned_oneroster_client;
use enrol_oneroster\local\command;
use enrol_oneroster\local\interfaces\filter;
use stdClass;

/**
 * One Roster Client.
 *
 * This plugin synchronizes enrolment and roles with an uploaded OneRoster CSV file.
 *
 * @package    enrol_oneroster
 * @copyright  QUT Capstone Team - Abhinav Gandham, Harrison Dyba, Jonathon Foo, Kushi Patel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class csv_client extends csv_client_version_one {
    use versioned_oneroster_client;

    // Basepath for roles data.
    const BASEPATH_ROLES = 'roles';

    protected $data;

   /**
     * Set the data retrieved from the CSV file.
     *
     * @param array $manifest The manifest data.
     * @param array $users The users data.
     * @param array $classes The classes data.
     * @param array $courses The courses data.
     * @param array $orgs The orgs data.
     * @param array $enrollments The enrollments data.
     * @param array $academicsessions The academic sessions data.
     * @param array $roles The roles data.
     * @param array $demographics The demographics data.
     * @param array|null $userprofiles The user profiles data (optional).
     */
    public function versioned_set_data(
        array $manifest,
        array $users,
        array $classes,
        array $courses,
        array $orgs,
        array $enrollments,
        array $academicsessions,
        array $roles,
        array $demographics,
        ?array $userprofiles = null
    ): void {
        $this->data = [
            'manifest' => $manifest,
            'users' => $users,
            'classes' => $classes,
            'courses' => $courses,
            'orgs' => $orgs,
            'enrollments' => $enrollments,
            'academicSessions' => $academicsessions,
            'roles' => $roles,
            'demographics' => $demographics,
            'userProfiles' => $userprofiles ?? [],
        ];
    }

    /**
        * Execute the supplied command.
        *
        * @param   command $command The command to execute.
        * @param   filter $filter
        * @return  stdClass
    */
    public function execute(command $command, ?filter $filter = null): stdClass {

        // Same logic as the extended class method.
        $url = $command->get_url('');

        // Split the URL into tokens using '/' as the delimiter (e.g., /schools/org-sch-222-456/terms).
        $tokens = explode('/', $url);

        // The second token represents the base path ('users', 'orgs', 'schools').
        $basepath = $tokens[1];

        // Get the organisation ID.
        $orgid = $this->orgid ?? null;

        if ($orgid == null) {
            throw new \Exception('Organization ID is not set.');
        }

        switch ($basepath) {
            case self::BASEPATH_ORGS:
                $new = parent::execute($command, $filter);
                return $new;
                break;

            case self::BASEPATH_SCHOOLS:
                $new = parent::execute($command, $filter);
                return $new;
                break;

            case self::BASEPATH_USERS:
                // The getUsers operation is used to retrieve all users in a school.
                $usersdata = $this->data[self::BASEPATH_USERS];
                $keys = array_map(function($user) {
                    return $user['sourcedId'];
                }, $usersdata);
                $mappedUserData = array_combine($keys, $usersdata);
                $users = [];
                // Collecting role data to be associated with users.
                $roledata = $this->data[self::BASEPATH_ROLES];
                $keys = array_map(function($role) {
                    return $role['sourcedId'];
                }, $roledata);
                $mappedRoleData = array_combine($keys, $roledata);
                foreach ($mappedUserData as $userid => $userdata){
                    $user = (object) $userdata;
                    $userroles = [];
                    foreach($mappedRoleData as $roleid => $roledata){
                        $role = (object) $roledata;
                        // Check role is associated with current organisation being processed.
                        if ($role->orgSourcedId != $orgid) continue;
                        // Check if role is associated with current user being constructed.
                        if ($role->userSourcedId === $userid){
                            // Remove sourcedId, required for construction but not present in completed object.
                            unset($role->userSourcedId);
                            // Add current role to array.
                            $userroles[$roleid] = $role;
                        }
                    }
                    // Set constructed array into user object or move to next user.
                    if(!empty($userroles)){
                        $user->roles = $userroles;
                    }else{
                        // User has no roles associated with current org.
                        // They will not be included in return, no point counstructing them.
                        continue;
                    }
                    if ($user->status === 'inactive') {
                        $user->status = 'tobedeleted';
                    }

                    // Convert agentsourcedID into agent objects in an array.
                    if (!empty($user->agentSourcedIds)) {
                        $agentids = explode(',', $user->agentSourcedIds);
                        $user->agents = array_map(function ($agentid) {
                            return (object) [
                                'sourcedId' => trim($agentid),
                                'type' => 'user'
                            ];
                        }, $agentids);
                    } else {
                        $user->agents = [];
                    }

                    // Convert userIDs into injects in accordance to information model.
                    if (!empty($user->userIds)) {
                        $useridslist = explode(',', str_replace(['{', '}'], '', $user->userIds));
                        $user->userIds = array_map(function ($useriditem) {
                            list($type, $identifier) = explode(':', $useriditem);
                            return (object) [
                                'type' => trim($type),
                                'identifier' => trim($identifier)
                            ];
                        }, $useridslist);
                    } else {
                        $user->userIds = [];
                    }

                    // Renaming primaryOrgSourcedId to simply primaryOrg, original is removed later.
                    // User's primary org does not determine if they are related to current selected org, their roles do.
                    if (!empty($user->primaryOrgSourcedId)) {
                        $user->primaryOrg = $user->primaryOrgSourcedId;
                    }else{
                        $user->primaryOrg = [];
                    }
                    // Remove sourcedIds that have been processed.
                    unset($user->agentSourcedIds, $user->primaryOrgSourcedId);
                    // Add to array of users.
                    $users[$userid] = $user;
                }
                    // Return the users.
                    return (object) [
                    'response' => (object) [
                        'users' => $users
                    ]
                ];

            default:
                return new stdClass();
        }
    }
}
