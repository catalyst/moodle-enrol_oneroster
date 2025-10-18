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

namespace enrol_oneroster\local\v1p1;

use enrol_oneroster\local\interfaces\client as client_interface;
use enrol_oneroster\local\oneroster_client as root_oneroster_client;
use enrol_oneroster\local\command;
use enrol_oneroster\local\interfaces\filter;
use stdClass;
use DateTime;
use enrol_oneroster\local\v1p1\oneroster_client as versioned_client;

/**
 * One Roster Client.
 *
 * This plugin synchronizes enrolment and roles with an uploaded OneRoster CSV file.
 *
 * @package    enrol_oneroster
 * @copyright  Gustavo Amorim De Almeida, Ruben Cooper, Josh Bateson, Brayden Porter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class csv_client implements client_interface {
    use root_oneroster_client;
    use versioned_client;

    /**
     * CSV data array.
     *
     * @var array
     */
    private array $data;

    /**
     * Base path for organisations.
     */
    const BASEPATH_ORGS = 'orgs';

    /**
     * Base path for schools.
     */
    const BASEPATH_SCHOOLS = 'schools';

    /**
     * Type constant for terms.
     */
    const TYPE_TERMS = 'terms';

    /**
     * Type constant for classes.
     */
    const TYPE_CLASSES = 'classes';

    /**
     * Type constant for enrollments.
     */
    const TYPE_ENROLLMENTS = 'enrollments';

    /**
     * Base path for users.
     */
    const BASEPATH_USERS = 'users';

    /**
     * Stores the organisation ID.
     *
     * @var string The organisation ID.
     */
    protected $orgid;

    /**
     * Key for academic sessions.
     */
    const ACADEMIC_SESSIONS_KEY = 'academicSessions';

    /**
     * Key for periods.
     */
    const PERIODS_KEY = 'periods';

    /**
     * Key for subjects.
     */
    const SUBJECTS_KEYS = 'subjects';

    /**
     * Key for subject codes.
     */
    const SUBJECT_CODES_KEY = 'subjectCodes';

    /**
     * Key for grades.
     */
    const GRADES_KEY = 'grades';

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
     * @param array $manifest The manifest data.
     * @param array $users The users data.
     * @param array $classes The classes data.
     * @param array $orgs The orgs data.
     * @param array $enrollments The enrollments data.
     * @param array $academicsessions The academic sessions data.
     */
    public function set_data(
        array $manifest,
        array $users,
        array $classes,
        array $orgs,
        array $enrollments,
        array $academicsessions
    ): void {
        $this->data = [
            'manifest' => $manifest,
            'users' => $users,
            'classes' => $classes,
            'orgs' => $orgs,
            'enrollments' => $enrollments,
            'academicSessions' => $academicsessions,
        ];
    }

    /**
     * Set the CSV data for synchronization with v1p2 additional fields.
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
     * @param array $userprofiles The user profiles data.
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
        array $userprofiles
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
            'userProfiles' => $userprofiles,
        ];
    }

    /**
     * Set the organisation ID.
     *
     * @param string $orgid The organisation ID.
     */
    public function set_org_id($orgid) {
        $this->orgid = $orgid;
    }

    /**
     * Execute the supplied command.
     *
     * @param   command $command The command to execute.
     * @param   filter $filter
     * @return  stdClass
     */
    public function execute(command $command, ?filter $filter = null): stdClass {
        $url = $command->get_url('');
        // Split the URL into tokens using '/' as the delimiter (e.g., /schools/org-sch-222-456/terms).
        $tokens = explode('/', $url);
        // The second token represents the base path ('users', 'orgs', 'schools').
        $basepath = $tokens[1];
        // The third token represents the Organisation ID.
        $param = $tokens[2] ?? '';
        // The fourth token represents the type of data to fetch ('terms', 'classes', 'enrollments').
        $type = $tokens[3] ?? '';
        // Get the organisation ID.
        $orgid = $this->orgid ?? null;

        if ($orgid == null) {
            throw new \Exception('Organization ID is not set.');
        }

        switch ($basepath) {
            case self::BASEPATH_ORGS:
                // The endpoint getAllOrgs is called to fetch all organisations.
                if ($param === $orgid || $param === '') {
                    $orgdata = $this->data[self::BASEPATH_ORGS];
                    $keys = array_map(function($orgs) {
                        return $orgs['sourcedId'];
                    }, $orgdata);
                    // Combine keys and organization data into an associative array.
                    $mappeddata = array_combine($keys, $orgdata);
                    if (isset($mappeddata[$orgid])) {
                        $org = (object) $mappeddata[$orgid];
                        // If status and dateLastModified are not set, set them to active and the current date.
                        if ($org->status == null && $org->dateLastModified == null) {
                            $org->status = 'active';
                            $org->dateLastModified = date('Y-m-d');
                        }
                        // To ensure compatibility with v1.0, set the status to 'tobedeleted' if it is 'inactive'.
                        if ($org->status === 'inactive') {
                            $org->status = 'tobedeleted';
                        }
                        $org->children = [(object) ['sourcedId' => $org->parentSourcedId]];

                        unset($org->parentSourcedId);
                    }
                    return (object) [
                        'response' => (object) [
                            'org' => $org
                        ]
                    ];
                }
                break;

            case self::BASEPATH_SCHOOLS:
                // The endpoint getTermsForSchool is called to fetch a list of classes in a term.
                if ($type === self::TYPE_TERMS) {
                    $academicsessiondata = $this->data[self::ACADEMIC_SESSIONS_KEY];
                    $keys = array_map(function ($schools) {
                        return $schools['sourcedId'];
                    }, $academicsessiondata);
                    $mappeddata = array_combine($keys, $academicsessiondata);
                    $academicsession = [];
                    foreach ($mappeddata as $academicid => $academicdata) {
                        $academic = (object) $academicdata;
                        if ($academic->status === 'inactive') {
                            $academic->status = 'tobedeleted';
                        }

                        $academic->parent = (object)['sourcedId' => $academicdata['parentSourcedId']];
                        unset($academic->parentSourcedId);
                        $academicsession[$academicid] = $academic;
                    }
                    return (object) [
                        'response' => (object) [
                            'academicSessions' => $academicsession,
                            'terms' => $academicsession
                        ]
                    ];
                }

                if ($type === self::TYPE_CLASSES) {
                    // The endpoint getClassesForSchool is called to fetch all students for a class.
                    $classdata = $this->data[self::TYPE_CLASSES];
                    $keys = array_map(function($schools) {
                        return $schools['sourcedId'];
                    }, $classdata);
                    $mappeddata = array_combine($keys, $classdata);
                    $classes = [];
                    foreach ($mappeddata as $classid => $classdata) {
                        $class = (object) $classdata;
                        if (isset($class->schoolSourcedId) && $class->schoolSourcedId == $orgid) {
                            if ($class->status === 'inactive') {
                                $class->status = 'tobedeleted';
                            }

                            if (!empty($class->termSourcedIds)) {
                                $termids = explode(',', $class->termSourcedIds);
                                $class->terms = array_map(function ($termid) {
                                    return (object) [
                                        'sourcedId' => trim($termid),
                                        'type' => 'academicSession'
                                    ];
                                }, $termids);
                            } else {
                                $class->terms = [];
                            }

                            if (!empty($class->periods)) {
                                if (is_string($class->periods)) {
                                    $class->periods = array_map('trim', explode(',', $class->periods));
                                } else if (!is_array($class->periods)) {
                                    $class->periods = [$class->periods];
                                }
                            } else {
                                $class->periods = [];
                            }

                            $objs = [
                                self::PERIODS_KEY,
                                self::SUBJECTS_KEYS,
                                self::SUBJECT_CODES_KEY,
                                self::GRADES_KEY
                            ];

                            foreach ($objs as $obj) {
                                if (!empty($class->$obj)) {
                                    if (is_string($class->$obj)) {
                                        $class->$obj = array_map('trim', explode(',', $class->$obj));
                                    } else if (!is_array($class->$obj)) {
                                        $class->$obj = [$class->$obj];
                                    }
                                } else {
                                    $class->$obj = [];
                                }
                            }

                            $class->school = (object) [
                                'sourcedId' => $class->schoolSourcedId,
                                'type' => 'school'
                            ];
                            $class->course = (object) [
                                'sourcedId' => $class->courseSourcedId,
                                'type' => 'course'
                            ];
                            unset($class->schoolSourcedId, $class->courseSourcedId, $class->termSourcedIds);
                        }
                        $classes[$classid] = $class;
                    }
                    return (object) [
                        'response' => (object) [
                            'classes' => $classes
                        ]
                    ];
                }

                if ($type === self::TYPE_ENROLLMENTS) {
                    // The endpoint getEnrollmentsForSchool is called to fetch all enrollments in a school.
                    $enrollmentdata = $this->data[self::TYPE_ENROLLMENTS];
                    $keys = array_map(function($schools) {
                        return $schools['sourcedId'];
                    }, $enrollmentdata);
                    $mappeddata = array_combine($keys, $enrollmentdata);
                    $enrollments = [];
                    foreach ($mappeddata as $enrollmentid => $enrollmentdata) {
                        $enrollment = (object) $enrollmentdata;
                        if (isset($enrollment->schoolSourcedId) && $enrollment->schoolSourcedId == $orgid) {
                            if ($enrollment->status === 'inactive') {
                                $enrollment->status = 'tobedeleted';
                            }

                            $enrollment->user = (object) [
                                'sourcedId' => $enrollmentdata['userSourcedId'],
                                'type' => 'user'
                            ];
                            $enrollment->school = (object) [
                                'sourcedId' => $enrollmentdata['schoolSourcedId'],
                                'type' => 'school'
                            ];
                            $enrollment->class = (object) [
                                'sourcedId' => $enrollmentdata['classSourcedId'],
                                'type' => 'class'
                            ];
                            unset(
                                $enrollment->schoolSourcedId,
                                $enrollment->classSourcedId,
                                $enrollment->userSourcedId
                            );
                        }
                        $enrollments[$enrollmentid] = $enrollment;
                    }
                    return (object) [
                        'response' => (object) [
                            'enrollments' => $enrollments
                        ]
                    ];
                }
                break;

            case self::BASEPATH_USERS:
                // The endpoint getAllUsers is called to fetch all users in a school.
                $usersdata = $this->data[self::BASEPATH_USERS];
                $keys = array_map(function($user) {
                    return $user['sourcedId'];
                }, $usersdata);
                $mappeddata = array_combine($keys, $usersdata);
                $users = [];
                foreach ($mappeddata as $userid => $userdata) {
                    $user = (object) $userdata;
                    if ($user->status === 'inactive') {
                        $user->status = 'tobedeleted';
                    }

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

                    if (!empty($user->orgSourcedIds)) {
                        $orgids = explode(',', $user->orgSourcedIds);
                        $user->orgs = array_map(function ($orgiditem) {
                            return (object) [
                                'sourcedId' => trim($orgiditem),
                                'type' => 'org'
                            ];
                        }, $orgids);
                    } else {
                        $user->orgs = [];
                    }

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

                    unset($user->orgSourcedIds, $user->agentSourcedIds);
                    foreach ($user->orgs as $org) {
                        if ($org->sourcedId == $orgid) {
                            $users[$userid] = $user;
                        }
                    }
                }
                return (object) [
                    'response' => (object) [
                        'users' => $users
                    ]
                ];
            default:
                return new stdClass();
        }
    }

    /**
     * Synchronise CSV data with Moodle.
     * This is a simplified version for CSV data that doesn't use the full entity system.
     *
     * @param int|null $onlysincetime Only sync data modified after this time
     */
    public function synchronise(?int $onlysincetime = null): void {
        global $DB;

        // Process courses first
        if (isset($this->data['classes'])) {
            foreach ($this->data['classes'] as $class) {
                $this->create_or_update_course($class);
            }
        }

        // Process users
        if (isset($this->data['users'])) {
            foreach ($this->data['users'] as $user) {
                $this->create_or_update_user($user);
            }
        }

        // Process enrollments
        if (isset($this->data['enrollments'])) {
            foreach ($this->data['enrollments'] as $enrollment) {
                $this->create_or_update_enrollment($enrollment);
            }
        }
    }

    /**
     * Create or update a course from CSV data.
     */
    private function create_or_update_course($class): void {
        global $DB;

        $course = new stdClass();
        $course->fullname = $class['title'] ?? 'Untitled Course';
        $course->shortname = !empty($class['classCode']) ? $class['classCode'] : $class['sourcedId'];
        $course->category = 1; // Default category
        $course->visible = 1;
        $course->startdate = time();

        // Check if course already exists
        $existing = $DB->get_record('course', ['shortname' => $course->shortname]);
        if ($existing) {
            $course->id = $existing->id;
            $DB->update_record('course', $course);
        } else {
            $course->id = $DB->insert_record('course', $course);
        }

        // Ensure enrollment instance exists
        $this->ensure_enrollment_instance($course->id);
    }

    /**
     * Create or update a user from CSV data.
     */
    private function create_or_update_user($user): void {
        global $DB;

        $userobj = new stdClass();
        $userobj->username = $user['username'] ?? 'user' . time();
        $userobj->firstname = $user['givenName'] ?? '';
        $userobj->lastname = $user['familyName'] ?? '';
        $userobj->email = $user['email'] ?? $userobj->username . '@example.com';
        $userobj->confirmed = 1;
        $userobj->mnethostid = 1;

        // Check if user already exists
        $existing = $DB->get_record('user', ['username' => $userobj->username]);
        if ($existing) {
            $userobj->id = $existing->id;
            $DB->update_record('user', $userobj);
        } else {
            $userobj->id = $DB->insert_record('user', $userobj);
        }
    }

    /**
     * Create or update an enrollment from CSV data.
     */
    private function create_or_update_enrollment($enrollment): void {
        global $DB;

        // Get the course by sourcedId
        $course = $DB->get_record('course', ['shortname' => $enrollment['classSourcedId'] ?? '']);
        if (!$course) {
            return; // Skip if course doesn't exist
        }

        // Get the user by sourcedId
        $user = $DB->get_record('user', ['username' => $enrollment['userSourcedId'] ?? '']);
        if (!$user) {
            return; // Skip if user doesn't exist
        }

        // Get the enrollment instance
        $enrolinstance = $DB->get_record('enrol', [
            'courseid' => $course->id,
            'enrol' => 'oneroster'
        ]);

        if (!$enrolinstance) {
            return; // Skip if enrollment instance doesn't exist
        }

        // Check if enrollment already exists
        $existing = $DB->get_record('user_enrolments', [
            'userid' => $user->id,
            'enrolid' => $enrolinstance->id
        ]);

        if (!$existing) {
            $userenrolment = new stdClass();
            $userenrolment->userid = $user->id;
            $userenrolment->enrolid = $enrolinstance->id;
            $userenrolment->status = 0; // Active
            $userenrolment->timestart = time();
            $userenrolment->timeend = 0;
            $userenrolment->modifierid = 2; // Admin user
            $userenrolment->timecreated = time();
            $userenrolment->timemodified = time();

            $DB->insert_record('user_enrolments', $userenrolment);
        }
    }

    /**
     * Ensure enrollment instance exists for a course.
     */
    private function ensure_enrollment_instance($courseid): void {
        global $DB;

        $enrolinstance = $DB->get_record('enrol', [
            'courseid' => $courseid,
            'enrol' => 'oneroster'
        ]);

        if (!$enrolinstance) {
            $enrol = new stdClass();
            $enrol->enrol = 'oneroster';
            $enrol->courseid = $courseid;
            $enrol->status = 0; // Enabled
            $enrol->sortorder = 0;
            $enrol->timecreated = time();
            $enrol->timemodified = time();

            $DB->insert_record('enrol', $enrol);
        }
    }
}
