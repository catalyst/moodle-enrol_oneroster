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
     * Class constructor to initialize the count variable.
     *
     * @return void
     */
    public function __construct() {
        $this->count = 0;
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

        switch ($basepath):
            case 'orgs':
                /** The endpoint getOrg is called to fetch the org data */
                if ($this->count == 1 || $this->count == 4) {
                    $orgdata = $this->data['orgs'];
                    $org = null;  

                    foreach ($orgdata as $data) {
                        // ISSUE HERE: sourcedId is not called dynamically yet
                        if (isset($data['sourcedId']) && $data['sourcedId'] === 'org-sch-222-3456') {
                            $org = new stdClass();
                            $org->name = $data['name'] ?? null;
                            $org->identifier = $data['identifier'] ?? null;
                            $org->type = $data['type'] ?? null;
                            $org->sourcedId = $data['sourcedId'] ?? null;

                            // ISSUE HERE: the status and dateLastModified are not called dynamically yet and need to be fixed
                            $org->status = $data['status'] ?? 'active';
                            $org->dateLastModified = $data['dateLastModified'] ?? '2023-01-10T09:15:00.0000000+00:00';
                        }
                    }
                    $this->count++;
                    return (object) [
                        'response' => (object) [
                            'org' => $org  
                        ]
                    ];
                }
                

            case 'schools':
                /** The endpoint getTermsForSchool is called to fetch a list of classes held in a term */
                if ($this->count == 2) {
                    $academicsessiondata = $this->data['academicSessions'];
                    $academicSession = [];

                    foreach ($academicsessiondata as $dataacademicSession) {
                        $academicSessionObject = new stdClass();
                        $academicSessionObject->title = $dataacademicSession['title'] ?? null;
                        $academicSessionObject->startDate = $dataacademicSession['startDate'] ?? null;
                        $academicSessionObject->endDate = $dataacademicSession['endDate'] ?? null;
                        $academicSessionObject->type = $dataacademicSession['type'] ?? null;
                        $academicSessionObject->parent = isset($dataacademicSession['parentSourcedId']) ? (object) [
                            'sourcedId' => $dataacademicSession['parentSourcedId']
                        ] : null;
                        $academicSessionObject->children = isset($dataacademicSession['sourcedId']) ? (object) [
                            'sourcedId' => $dataacademicSession['sourcedId']
                        ] : null;
                        $academicSessionObject->schoolYear = $dataacademicSession['schoolYear'] ?? null;
                        $academicSessionObject->sourcedId = $dataacademicSession['sourcedId'] ?? null;
                        $academicSessionObject->status = $dataacademicSession['status'] ?? null;
                        $academicSessionObject->dateLastModified = $dataacademicSession['dateLastModified'] ?? null;

                        $academicSession[] = $academicSessionObject;
                    }
                    $this->count++;
                    return (object) [
                        'response' => (object) [
                            'academicSessions' => $academicSession,
                            'terms' => $academicSession
                        ]
                    ];
                }

                /** The endpoint getClassesForSchool is called to fetch all students for a class */
                if ($this->count == 3) {
                    $classdata = $this->data['classes'];
                    $classes = [];
                    foreach ($classdata as $dataclass) {
                        // ISSUE HERE: schoolSourcedId is not called dynamically yet
                        if (isset($dataclass['schoolSourcedId']) && $dataclass['schoolSourcedId'] === 'org-sch-222-456') {
                            $classObject = new stdClass();
                            $classObject->title = $dataclass['title'] ?? null;
                            $classObject->classCode = $dataclass['classCode'] ?? null;
                            $classObject->classType = $dataclass['classType'] ?? null;
                            $classObject->location = $dataclass['location'] ?? null;
                            $classObject->grades = $dataclass['grades'] ?? null;
                            $classObject->subjects = $dataclass['subjects'] ?? null;
                            $classObject->course = isset($dataclass['courseSourcedId']) ? (object) [
                                'sourcedId' => $dataclass['courseSourcedId']
                            ] : null;
                            $classObject->school = (object) [
                                'sourcedId' => $dataclass['schoolSourcedId']
                            ];
                            $classObject->terms = isset($dataclass['termSourcedIds']) ? array_map(function($term) {
                                return (object) ['sourcedId' => $term];
                            }, $dataclass['termSourcedIds']) : [];
                            $classObject->periods = $dataclass['periods'] ?? null;
                            $classObject->sourcedId = $dataclass['sourcedId'] ?? null;
                            $classObject->status = $dataclass['status'] ?? null;
                            $classObject->dateLastModified = $dataclass['dateLastModified'] ?? null;
                        }
                        $classes[] = $classObject;
                        $this->count++;
                        return (object) [
                            'response' => (object) [
                                'classes' => $classes
                            ]
                        ];
                    }
                }

                /** The endpoint getEnrollmentsForSchool is called to fetch all enrolments in a school */
                if ($this->count == 5){
                    $enrollmentdata = $this->data['enrollments'];
                    $enrollments = [];
                    foreach ($enrollmentdata as $dataenrollment) {
                        // ISSUE HERE: schoolSourcedId is not called dynamically yet and also onyl gets one enrollment object
                        if (isset($dataenrollment['schoolSourcedId']) && $dataenrollment['schoolSourcedId'] === 'org-sch-222-456') {
                            $enrollmentObject = new stdClass();
                            $enrollmentObject->user = isset($dataenrollment['userSourcedId']) ? (object) [
                                'sourcedId' => $dataenrollment['userSourcedId']
                            ] : null;
                            $enrollmentObject->school = (object) [
                                'sourcedId' => $dataenrollment['schoolSourcedId']
                            ];
                            $enrollmentObject->class = isset($dataenrollment['classSourcedId']) ? (is_array($dataenrollment['classSourcedId']) ? array_map(function($classSourcedId) {
                                        return (object) ['sourcedId' => $classSourcedId];
                                    }, $dataenrollment['classSourcedId']) : [(object) ['sourcedId' => $dataenrollment['classSourcedId']]]) : [];
                            $enrollmentObject->role = $dataenrollment['role'] ?? null;
                            $enrollmentObject->primary = $dataenrollment['primary'] ?? null;
                            $enrollmentObject->beginDate = $dataenrollment['beginDate'] ?? null;
                            $enrollmentObject->endDate = $dataenrollment['endDate'] ?? null;
                            $enrollmentObject->sourcedId = $dataenrollment['sourcedId'] ?? null;
                            $enrollmentObject->status = $dataenrollment['status'] ?? null;
                            $enrollmentObject->dateLastModified = $dataenrollment['dateLastModified'] ?? null;
                            $enrollments[] = $enrollmentObject;
                        }
                    }
                    $this->count++;
                    return (object) [
                        'response' => (object) [
                            'enrollments' => $enrollments
                        ]
                    ];
                }

                    
            case 'users':
                /** The endpoint GetAllUsers is called to fetch all users */
                if ($this->count == 0) {
                    $usersData = $this->data['users'];
                    $users = [];
                    foreach ($usersData as $datauser) {
                        $userObject = new stdClass();
                        $userObject->username = $datauser['username'] ?? null;
                        $userObject->userIds = isset($datauser['userIds']) ? array_map(function($userId) {
                            return (object) ['type' => $userId['type'], 'identifier' => $userId['identifier']];
                        }, $datauser['userIds']) : [];
                        $userObject->enabledUser = $datauser['enabledUser'] ?? null;
                        $userObject->givenName = $datauser['givenName'] ?? null;
                        $userObject->familyName = $datauser['familyName'] ?? null;
                        $userObject->middleName = $datauser['middleName'] ?? null;
                        $userObject->role = $datauser['role'] ?? null;
                        $userObject->identifier = $datauser['identifier'] ?? null;
                        $userObject->email = $datauser['email'] ?? null;
                        $userObject->sms = $datauser['sms'] ?? null;
                        $userObject->phone = $datauser['phone'] ?? null;
                        $userObject->agents = isset($datauser['agentSourcedIds']) ? array_map(function($agents) {
                            return (object) ['sourcedId' => $agents];
                        }, $datauser['agentSourcedIds']) : [];
                        $userObject->orgs = array_map(function($orgs) {
                            return (object) ['sourcedId' => $orgs];
                        }, is_array($datauser['orgSourcedIds']) ? $datauser['orgSourcedIds'] : [$datauser['orgSourcedIds']]);
                        $userObject->sourcedId = $datauser['sourcedId'] ?? null;  
                        $userObject->status = $datauser['status'] ?? null;
                        $userObject->dateLastModified = $datauser['dateLastModified'] ?? null;
                        $userObject->grades = $datauser['grades'] ?? null;
                        $userObject->password = $datauser['password'] ?? null;
                        $users[] = $userObject;
                    }
                    $this->count++;
                    return (object) [
                        'response' => (object) [
                            'users' => $users 
                        ]
                    ];
                }

                if ($this->count == 6) {
                    /** The endpoint GetAllUsers is called to fetch an user */
                    $usersData = $this->data['users'];
                    $userObject = null;

                    foreach ($usersData as $datauser) {
                    // ISSUE HERE: sourcedId is not called dynamically yet
                    if (isset($datauser['sourcedId']) && $datauser['sourcedId'] === 'usr-222-123456') {
                        $userObject = new stdClass();
                        $userObject->username = $datauser['username'] ?? null;
                        $userObject->userIds = isset($datauser['userIds']) ? array_map(function($userId) {
                            return (object) ['type' => $userId['type'], 'identifier' => $userId['identifier']];
                        }, $datauser['userIds']) : [];
                        $userObject->enabledUser = $datauser['enabledUser'] ?? null;
                        $userObject->givenName = $datauser['givenName'] ?? null;
                        $userObject->familyName = $datauser['familyName'] ?? null;
                        $userObject->middleName = $datauser['middleName'] ?? null;
                        $userObject->role = $datauser['role'] ?? null;
                        $userObject->identifier = $datauser['identifier'] ?? null;
                        $userObject->email = $datauser['email'] ?? null;
                        $userObject->sms = $datauser['sms'] ?? null;
                        $userObject->phone = $datauser['phone'] ?? null;
                        $userObject->agents = isset($datauser['agentSourcedIds']) ? array_map(function($agents) {
                            return (object) ['sourcedId' => $agents];
                        }, $datauser['agentSourcedIds']) : [];
                        $userObject->orgs = array_map(function($orgs) {
                            return (object) ['sourcedId' => $orgs];
                        }, is_array($datauser['orgSourcedIds']) ? $datauser['orgSourcedIds'] : [$datauser['orgSourcedIds']]);
                        $userObject->sourcedId = $datauser['sourcedId'] ?? null;  
                        $userObject->status = $datauser['status'] ?? null;
                        $userObject->dateLastModified = $datauser['dateLastModified'] ?? null;
                        $userObject->grades = $datauser['grades'] ?? null;
                        $userObject->password = $datauser['password'] ?? null;
                        }
                    }      
                    return (object) [
                        'response' => (object) [
                            'user' => $userObject
                        ]
                    ];
                }
            default:
                return new stdClass();
        endswitch;
    }
}