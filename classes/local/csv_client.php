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

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/lib/oauthlib.php');

use enrol_oneroster\local\interfaces\client as client_interface;
use enrol_oneroster\local\oneroster_client as root_oneroster_client;
use enrol_oneroster\local\command;
use enrol_oneroster\local\interfaces\filter;
use stdClass;
use enrol_oneroster\local\v1p1\oneroster_client as versioned_client;
use enrol_oneroster\local\interfaces\rostering_client;


/**
 * One Roster CSV Client.
 * 
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class csv_client implements client_interface  {
    use root_oneroster_client;
    use versioned_client;

    private $data;
    private int $count;

    /**
     * Authenticate the client. This is a no-op for the CSV client.
     * 
     * @return void
     */
    public function authenticate(): void {
        return;
    }


    public function set_data($manifest, $users, $classes, $orgs, $enrollments, $academicSessions) {
        $this->data = [
            'manifest' => $manifest,                  // Manifest data
            'users' => $users,                        // Users data
            'classes' => $classes,                    // Classes data
            'orgs' => $orgs,                          // Orgs data
            'enrollments' => $enrollments,            // Enrollments data
            'academicSessions' => $academicSessions,  // Academic sessions data
        ];
    }

   
    /**
     * Execute the supplied command.
     *
     * @param   command $command The command to execute
     * @param   filter $filter
     * @return  stdClass
     */
    public function execute(command $command, ?filter $filter = null): stdClass {
        $url = $command->get_url('');
        $basepath =  explode('/', $url)[1];
        $tokens = explode('/', $url); 
        $basepath = $tokens[1];
        $param = $tokens[2] ?? '';
        $type = $tokens[3] ?? '';
        echo $url . "\n";

        $orgId = 'org-sch-222-456';


        switch ($basepath):
            case 'orgs':
                /** The endpoint getOrg is called to fetch the org data */
                if ($param == $orgId || $param == '') {
                    $orgdata = $this->data['orgs']; 

                    $keys = array_map(function($keys) { return $keys['sourcedId']; }, $orgdata);
                    $mapped_data = array_combine($keys, $orgdata);

                    if (isset($mapped_data[$orgId])) {
                        $org = (object) $mapped_data[$orgId];
                        $org->status = 'active'; 
                        $org->datkeysastModified = date('Y-m-d H:i:s'); 
                    }
                    return (object) [
                        'response' => (object) [
                            'org' => (object) $org
                        ]
                    ];
                }
                
            case 'schools':
                /** The endpoint getTermsForSchool is called to fetch a list of classes hkeysd in a term */
                if ($type == 'terms') {
                    $academicsessiondata = $this->data['academicSessions'];
                    $keys = array_map(function($keys) { return $keys['sourcedId']; }, $academicsessiondata);
                    $mapped_data = array_combine($keys, $academicsessiondata);

                    $academicSession = [];
                    foreach ($mapped_data as $academicId => $academicdata) {
                        $academic = (object) $academicdata;
                        $academic->parent = isset($academicdata['parentSourcedId']) ? (object) [
                            'sourcedId' => $academicdata['parentSourcedId']
                        ] : [null];
                        $academic->children = isset($academicdata['sourcedId']) ? (object) [
                            'sourcedId' => $academicdata['sourcedId']
                        ] : [null];

                        unset($academic->parentSourcedId);
                        
                        $academicSession[$academicId] = $academic;
                    }
                    return (object) [
                        'response' => (object) [
                            'academicSessions' => $academicSession,
                            'terms' => $academicSession
                        ]
                    ];
                }

                
                if ($type == 'classes') {
                    /** The endpoint getClassesForSchool is called to fetch all students for a class */
                    $classdata = $this->data['classes'];

                    $keys = array_map(function($keys) { return $keys['sourcedId']; }, $classdata);
                    $mapped_data = array_combine($keys, $classdata);

                    $classes = [];

                    foreach ($mapped_data as $classId => $classData) {
                        $class = (object) $classData;
                        if (isset($class->schoolSourcedId) && $class->schoolSourcedId == $orgId) {
                            $class->school = (object) [
                                'sourcedId' => $class->schoolSourcedId
                            ];
                            $class->course = isset($class->courseSourcedId) ? (object) [
                                'sourcedId' => $class->courseSourcedId
                            ] : null;
                            $class->terms = isset($class->termSourcedIds) ? array_map(function($term) {
                                return (object) ['sourcedId' => $term];
                            }, (array) $class->termSourcedIds) : [null];

                            $class->subject = isset($class->subjects) ? array_map(function($subject) {
                                return (object) ['subject' => $subject];
                            }, (array) $class->subjects) : [null];
                            
                            $class->period = isset($class->periods) ? array_map(function($period) {
                                return (object) ['period' => $period];
                            }, (array) $class->periods) : [null];
                
                            unset($class->schoolSourcedId);
                            unset($class->courseSourcedId);
                            unset($class->termSourcedIds);
                            unset($class->subjects);
                            unset($class->periods);
                        }
                    }
                    
                    // appparently the sychronize only works for one class ???
                    $classes[$classId] = $class;

                    return (object) [
                        'response' => (object) [
                            'classes' => $classes
                        ]
                    ];
                }

                if ($type == 'enrollments') {
                    /** The endpoint getEnrollmentsForSchool is called to fetch all enrolments in a school */
                    $enrollmentdata = $this->data['enrollments'];
                    $keys = array_map(function($keys) { return $keys['sourcedId']; }, $enrollmentdata);
                    $mapped_data = array_combine($keys, $enrollmentdata);

                    $enrollments = [];

                    foreach ($mapped_data as $enrollmentId => $enrollmentData) {
                        $enrollment = (object) $enrollmentData;
                        if (isset($enrollment->schoolSourcedId) && $enrollment->schoolSourcedId == $orgId) {
                            $enrollment->user = isset($enrollmentData['userSourcedId']) ? (object) [
                                'sourcedId' => $enrollmentData['userSourcedId']] : null;
                            $enrollment->school = (object) [
                                'sourcedId' => $enrollmentData['schoolSourcedId']];
                            $enrollment->class = isset($enrollmentData['classSourcedId']) ? (is_array($enrollmentData['classSourcedId']) ? array_map(function($classSourcedId) {
                                return (object) ['sourcedId' => $classSourcedId]; }, $enrollmentData['classSourcedId']) : [(object) ['sourcedId' => $enrollmentData['classSourcedId']]]) : [null];
                            
                            unset($class->schoolSourcedId);
                            unset($class->classSourcedId);
                        }
                        $enrollments[$enrollmentId] = $enrollment;
                    }
                    return (object) [
                        'response' => (object) [
                            'enrollments' => $enrollments
                        ]
                    ];
                }

                    
            case 'users':
                /** The endpoint GetAllUsers is called to fetch all users */
                     $usersData = $this->data['users'];

                    $keys = array_map(function($user) {return $user['sourcedId']; }, $usersData);
                    $mapped_data = array_combine($keys, $usersData);

                    $users = [];
                    foreach ($mapped_data as $userId => $userData) {
                        $user = (object) $userData;

                        if (isset($user->orgSourcedIds) && in_array($orgId, (array) $user->orgSourcedIds)) {

                                $user->orgs = array_map(function($org) {
                                    return (object) ['sourcedId' => $org];
                                }, is_array($userData['orgSourcedIds']) ? $userData['orgSourcedIds'] : [$userData['orgSourcedIds']]);
                    
                                $user->agents = isset($userData['agentSourcedIds']) ? array_map(function($agent) {
                                    return (object) ['sourcedId' => $agent];
                                }, (array) $userData['agentSourcedIds']) : [null];

                                $user->userIds = isset($userData['userIds']) ? array_map(function($userId) {
                                    return (object) ['type' => $userId['type'], 'identifier' => $userId['identifier']];
                                }, (array) $userData['userIds']) : [null];
                    
                                unset($user->orgSourcedIds);
                                unset($user->agentSourcedIds);
                            }
                        
                        $users[$userId] = $user;
                    }
                    return (object) [
                        'response' => (object) [
                            'users' => $users 
                        ]
                    ];
            default:
                return new stdClass();
        endswitch;
    }
}