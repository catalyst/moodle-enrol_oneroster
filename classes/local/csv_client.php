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
 * @copyright  Gustavo Amorim De Almeida, Ruben Cooper, Josh Bateson, Brayden Porter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace enrol_oneroster\local;

use enrol_oneroster\local\interfaces\client as client_interface;
use enrol_oneroster\local\oneroster_client as root_oneroster_client;
use enrol_oneroster\local\command;
use enrol_oneroster\local\interfaces\filter;
use stdClass;
use DateTime;
use enrol_oneroster\local\v1p1\oneroster_client as versioned_client;

class csv_client implements client_interface  {
    use root_oneroster_client;
    use versioned_client;

    // Define constants for the base paths and types
    const BASEPATH_ORGS = 'orgs';
    const BASEPATH_SCHOOLS = 'schools';
    const TYPE_TERMS = 'terms';
    const TYPE_CLASSES = 'classes';
    const TYPE_ENROLLMENTS = 'enrollments';
    const BASEPATH_USERS = 'users';
    private $orgId; 

    /**
     * Authenticate the client. This is a no-op for the CSV client.
     * 
     * @return void
     */
    public function authenticate(): void {
        return;
    }

    /**
     * Set the data retrieved from the CSV file.
     *
     * @param array $manifest The manifest data
     * @param array $users The users data
     * @param array $classes The classes data
     * @param array $orgs The orgs data
     * @param array $enrollments The enrollments data
     * @param array $academicSessions The academic sessions data
     * @return void
     */
    public function set_data(
        array $manifest, 
        array $users, 
        array $classes, 
        array $orgs, 
        array $enrollments, 
        array $academicSessions
    ): void {
        $this->data = [
            'manifest' => $manifest,
            'users' => $users,
            'classes' => $classes,
            'orgs' => $orgs,
            'enrollments' => $enrollments,
            'academicSessions' => $academicSessions,
        ];
    }

    /**
     * Set the organisation ID.
     *
     * @param string $orgId The organisation ID
     */
    public function set_orgid($orgId) {
        $this->orgId = $orgId;
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
        // Split the URL into tokens using '/' as the delimiter (eg. /schools/org-sch-222-456/terms)
        $tokens = explode('/', $url); 
        // The second token represents the base path ('users', 'orgs', 'schools')
        $basepath = $tokens[1];
        // The third token represents the Organisation ID
        $param = $tokens[2] ?? '';
        // The fourth token represents the type of data to fetch ('terms', 'classes', 'enrollments')
        $type = $tokens[3] ?? '';
        // Get the organisation ID
        $orgId = "org-sch-222-456";

        switch ($basepath):
            case self::BASEPATH_ORGS:
                // The endpoint getOrg is called to fetch the org data 
                if ($param == $orgId || $param == '') {
                    $orgdata = $this->data['orgs'];
                    $keys = array_map(function($orgs) { return $orgs['sourcedId']; }, $orgdata);
                    $mapped_data = array_combine($keys, $orgdata);
                    if (isset($mapped_data[$orgId])) {
                        $org = (object) $mapped_data[$orgId];
                        $org->status = 'active'; 
                        $org->dateLastModified = date('Y-m-d\TH:i:s\Z');
                    }
                    return (object) [
                        'response' => (object) [
                            'org' => $org
                        ]
                    ];
                }

            case self::BASEPATH_SCHOOLS:
                // The endpoint getTermsForSchool is called to fetch a list of classes in a term 
                if ($type == self::TYPE_TERMS) {
                    $academicsessiondata = $this->data['academicSessions'];
                    $keys = array_map(function($schools) { return $schools['sourcedId']; }, $academicsessiondata);
                    $mapped_data = array_combine($keys, $academicsessiondata);
                    $academicSession = [];
                    foreach ($mapped_data as $academicId => $academicdata) {
                        if (isset($academicdata['startDate'])) {
                            $academicdata['startDate'] = str_replace('-', '/', $academicdata['startDate']);
                            $date = DateTime::createFromFormat('d/m/Y', $academicdata['startDate']);
                            if ($date) {
                                $academicdata['startDate'] = $date->format('Y-m-d');
                            }
                        }
                        
                        if (isset($academicdata['endDate'])) {
                            $academicdata['endDate'] = str_replace('-', '/', $academicdata['endDate']);
                            $date = DateTime::createFromFormat('d/m/Y', $academicdata['endDate']);
                            if ($date) {
                                $academicdata['endDate'] = $date->format('Y-m-d');
                            }
                        }
                        $academic = (object) $academicdata;
                        $getparentSourcedId = (object)['sourcedId' => $academicdata['parentSourcedId']];
                        $getchildrenSourcedId = (object)['sourcedId' => $academicdata['sourcedId']];

                        $parentSourcedId = isset($academicdata['parentSourcedId']) ? $getparentSourcedId : [null];
                        $childrenSourcedId = isset($academicdata['sourcedId']) ? $getchildrenSourcedId : [null];

                        $academic->parent = $parentSourcedId;
                        $academic->children = $childrenSourcedId;

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

                if ($type == self::TYPE_CLASSES) {
                    // The endpoint getClassesForSchool is called to fetch all students for a class 
                    $classdata = $this->data['classes'];
                    $keys = array_map(function($schools) { return $schools['sourcedId']; }, $classdata);
                    $mapped_data = array_combine($keys, $classdata);
                    $classes = [];
                    foreach ($mapped_data as $classId => $classData) {
                        $class = (object) $classData;
                        if (isset($class->schoolSourcedId) && $class->schoolSourcedId == $orgId) {
                            $getcourseSource = (object) ['sourcedId' => $class->courseSourcedId];;
                            $getschoolSource = (object) ['sourcedId' => $class->schoolSourcedId];
                            $getermSourcedIds = array_map(function ($term) { return (object) ['sourcedId' => $term]; }, (array) $class->termSourcedIds);
                            $getsubjects = array_map(function ($subject) { return (object) ['subject' => $subject]; }, (array) $class->subjects);
                            $getperiods = array_map(function ($period) { return (object) ['period' => $period]; }, (array) $class->periods);

                            $courseObject = isset($class->courseSourcedId) ? $getcourseSource: null;
                            $termsArray = isset($class->termSourcedIds) ? $getermSourcedIds : [null];
                            $subjectsArray = isset($class->subjects) ? $getsubjects : [null];
                            $periodArray = isset($class->periods) ? $getperiods : [null];

                            $class->school = $getschoolSource;
                            $class->period = $periodArray;
                            $class->course = $courseObject;
                            $class->terms = $termsArray;
                            $class->subject = $subjectsArray;

                            unset($class->schoolSourcedId, $class->courseSourcedId, $class->termSourcedIds, $class->subjects, $class->periods);
                        }
                        $classes[$classId] = $class;
                    }
                    return (object) [
                        'response' => (object) [
                            'classes' => $classes
                        ]
                    ];
                }

                if ($type == self::TYPE_ENROLLMENTS) {
                    // The endpoint getEnrollmentsForSchool is called to fetch all enrollments in a school 
                    $enrollmentdata = $this->data['enrollments'];
                    $keys = array_map(function($schools) { return $schools['sourcedId']; }, $enrollmentdata);
                    $mapped_data = array_combine($keys, $enrollmentdata);
                    $enrollments = [];
                    foreach ($mapped_data as $enrollmentId => $enrollmentData) {
                        $enrollment = (object) $enrollmentData;
                        if (isset($enrollment->schoolSourcedId) && $enrollment->schoolSourcedId == $orgId) {
                            $getuserSourcedId = (object) ['sourcedId' => $enrollmentData['userSourcedId']];
                            $isClassSourcedIdArray = is_array($enrollmentData['classSourcedId']);
                            $classSourcedIdList  = array_map(function ($classSourcedId) { (object) ['sourcedId' => $classSourcedId]; }, (array) $enrollmentData['classSourcedId']);
                            $classSourcedIdObject  = (object) ['sourcedId' => $enrollmentData['classSourcedId']];

                            $userObject = isset($enrollmentData['userSourcedId']) ? $getuserSourcedId : null;
                            $enrollment->school = (object) ['sourcedId' => $enrollmentData['schoolSourcedId']];
                            $classObject = isset($enrollmentData['classSourcedId']) ? ($isClassSourcedIdArray ? $classSourcedIdList : $classSourcedIdObject ) : [null];

                            $enrollment->user = $userObject;
                            $enrollment->class = $classObject;

                            unset($enrollment->schoolSourcedId, $enrollment->classSourcedId);
                        }
                        $enrollments[$enrollmentId] = $enrollment;
                    }
                    return (object) [
                        'response' => (object) [
                            'enrollments' => $enrollments
                        ]
                    ];
                }

            case self::BASEPATH_USERS:
                // The endpoint GetAllUsers is called to fetch all users in a school
                $usersData = $this->data['users'];
                $keys = array_map(function($user) {return $user['sourcedId']; }, $usersData);
                $mapped_data = array_combine($keys, $usersData);
                $users = [];
                foreach ($mapped_data as $userId => $userData) {
                    $user = (object) $userData;
                    if (isset($user->orgSourcedIds) && in_array($orgId, (array) $user->orgSourcedIds)) {
                        if (isset($userData['agentSourcedIds']) && !empty($userData['agentSourcedIds'])) {
                            $getagents = array_map(function ($agent) {
                                return (object) ['sourcedId' => $agent];
                            }, (array) $userData['agentSourcedIds']);
                        } else {
                            $getagents = [(object) ['sourcedId' => ""]];
                        }
                        if (isset($userData['orgSourcedIds']) && !empty($userData['orgSourcedIds'])) {
                            $orgs = array_map(function ($org) {
                                return (object) ['sourcedId' => $org];
                            }, (array) $userData['orgSourcedIds']);
                        } else {
                            $orgs = [(object) ['sourcedId' => ""]];
                        }

                        $user->agents = $getagents;
                        $user->orgs = $orgs;

                        unset($user->orgSourcedIds, $user->agentSourcedIds);
                        $users[$userId] = $user;
                    }    
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