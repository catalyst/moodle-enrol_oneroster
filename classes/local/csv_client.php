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
 * One Roster Client.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class csv_client implements client_interface  {
    use root_oneroster_client;
    use versioned_client;

    private $data;

    public function set_data($users, $classes, $courses, $orgs, $enrollments, $academicsessions) {
        $this->data = [
            'users' => $users,
            'classes' => $classes,
            'courses' => $courses,
            'orgs' => $orgs,
            'enrollments' => $enrollments,
            'academicsessions' => $academicsessions,
        ];
    }

    public function authenticate(): void {
        return;
    }




    /**
     * Execute the supplied command.
     *
     * @param   command $command The command to execute
     * @param   filter $filter
     * @return  stdClass
     */
    public function execute(command $command, ?filter $filter = null): stdClass {
        $result = new stdClass();

        switch ($command->get_method()):
            case 'getAllAcademicSessions':
                $result->AllAcademicSessions = array_column($this->data['academicsessions'], 'sourcedId');
                break;

            case 'getAcademicSession':
                $sourcedId = $_GET['sourcedId'] ?? null; 
                $academicsession = $this->getAcademicSession($sourcedId);
                if ($academicsession) {
                    $result->AcademicSession = $academicsession;
                }
                break;

            case 'getAllClasses':
                $result->AllClasses = array_column($this->data['classes'], 'sourcedId');
                break;

            case 'getClass':
                $sourcedId = $_GET['sourcedId'] ?? null; 
                $class = $this->getClass($sourcedId);
                if ($class) {
                    $result->Class = $class;
                }
                break;

            case 'getAllCourses':
                $result->AllCourses = array_column($this->data['courses'], 'sourcedId');
                break;

            case 'getCourse':
                $sourcedId = $_GET['sourcedId'] ?? null; 
                $course = $this->getCourse($sourcedId);
                if ($course) {
                    $result->Course = $course;
                }
                break;

            // case 'getAllGradingPeriods':
            //     return getAllGradingPeriods();
            //     break;

            // case 'getGradingPeriod':
            //     return getGradingPeriod();
            //     break;

            // case 'getAllDemographics':
            //     return getAllDemographics();
            //     break;

            // case 'getDemographics':
            //     return getDemographics();
            //     break;

            case 'getAllEnrollments':
                $result->AllEnrollments = array_column($this->data['enrollments'], 'sourcedId');
                break;

            case 'getEnrollment':
                $sourcedId = $_GET['sourcedId'] ?? null; 
                $enrollment = $this->getEnrollment($sourcedId);
                if ($enrollment) {
                    $result->Enrollment = $enrollment;
                }
                break;

            case 'getAllOrgs':
                $result->AllOrgs = array_column($this->data['orgs'], 'sourcedId');
                break;

            case 'getOrg':
                $sourcedId = $_GET['sourcedId'] ?? null; 
                $org = $this->getOrg($sourcedId);
                if ($org) {
                    $result->Org = $org;
                }
                break;

            case 'getAllSchools':
                $result->AllSchools = array_column($this->data['orgs'], 'name');
                break;

            case 'getSchool':
                $name = $_GET['name'] ?? null; 
                $school = $this->getSchool($name);
                if ($school) {
                    $result->School = $school;
                }
                break;
                

            case 'getAllStudents':
                $result->AllStudents = array_values(array_map(function($user) {
                    return $user['sourcedId'];
                }, array_filter($this->data['users'], function($user) {
                    return $user['role'] === 'student';
                })));
                break;

            case 'getStudent':
                $sourcedId = $_GET['sourcedId'] ?? null;
                $student = $this->getStudent($sourcedId);
                if ($student) {
                    $result->Student = $student;
                }
                break;

            case 'getAllTeachers':
                $result->AllTeachers = array_values(array_map(function($user) {
                    return $user['sourcedId'];
                }, array_filter($this->data['users'], function($user) {
                    return $user['role'] === 'teacher';
                })));

            case 'getTeacher':
                $sourcedId = $_GET['sourcedId'] ?? null;
                $teacher = $this->getTeacher($sourcedId);
                if ($teacher) {
                    $result->Teacher = $teacher;
                }
                break;

            case 'getAllTerms':
                $result->AllTerms =  array_unique(array_column($this->data['classes'], 'termSourcedIds'));
                break;

            // case 'getTerm':
            //     $termSourcedIds = $_GET['sourcedId'] ?? null;
            //     $term = $this->getTerm($termSourcedIds);
            //     if ($term) {
            //         $result->Term = $term;
            //     }
            //     break;


            case 'getAllUsers':
                $result->AllUsers= array_column($this->data['users'], 'sourcedId');
                break;

            case 'getUser':
                $sourcedId = $_GET['sourcedId'] ?? null;
                $user = $this->getUser($sourcedId);
                if ($user) {
                    $result->User = $user;
                }
                break;

            case 'getCoursesForSchool':
                $orgSourcedId = $_GET['orgSourcedId'] ?? null;
                $courses = $this->getCoursesForSchool($orgSourcedId);
                if ($courses) {
                    $result->Courses = $courses;
                }
                break;

            // case 'getEnrollmentsForClassInSchool':
            //     return getEnrollmentsForClassInSchool();
            //     break;

            // case 'getStudentsForClassInSchool':
            //     return getStudentsForClassInSchool();
            //     break;

            // case 'getTeachersForClassInSchool':
            //     return getTeachersForClassInSchool();
            //     break;

            case 'getEnrollmentsForSchool':
                $schoolSourcedId = $_GET['schoolSourcedId'] ?? null;
                $enrollments = $this->getEnrollmentsForSchool($schoolSourcedId);
                if ($enrollments) {
                    $result->Enrollments = $enrollments;
                }
                break;

            case 'getStudentsForSchool':
                $schoolSourcedId = $_GET['schoolSourcedId'] ?? null;
                $students = $this->getStudentsForSchool($schoolSourcedId);
                if ($students) {
                    $result->Students = $students;
                }
                break;

            case 'getTeachersForSchool':
                $schoolSourcedId = $_GET['schoolSourcedId'] ?? null;
                $teachers = $this->getTeachersForSchool($schoolSourcedId);
                if ($teachers) {
                    $result->Teachers = $teachers;
                }
                break;
            

            case 'getTermsForSchool':
                $schoolSourcedId = $_GET['schoolSourcedId'] ?? null;
                $terms = $this->getTermsForSchool($schoolSourcedId);
                if ($terms) {
                    $result->Terms = $terms;
                }
                break;

            case 'getClassesForTerm':
                $termSourcedId = $_GET['termSourcedId'] ?? null;
                $classes = $this->getClassesForTerm($termSourcedId);
                if ($classes) {
                    $result->Classes = $classes;
                }
                break;

            // case 'getGradingPeriodsForTerm':
            //     return getGradingPeriodsForTerm();
            //     break;

            case 'getClassesForCourse':
                $courseSourcedId = $_GET['courseSourcedId'] ?? null;
                $classes = $this->getClassesForCourse($courseSourcedId);
                if ($classes) {
                    $result->Classes = $classes;
                }
                break;

            default:
                return new stdClass();
        endswitch;
    
        return $result;
    }
    
    







































    public function getAcademicSession($sourcedId) {
        if (!$sourcedId) {
            return null; // or handle the error as needed
        }
    
        foreach ($this->data['academicsessions'] as $session) {
            if ($session['sourcedId'] === $sourcedId) {
                return $session;
            }
        }
    
        return null; // or handle the case where the session is not found
    }


    public function getClass($sourcedId) {
        if (!$sourcedId) {
            return null; // or handle the error as needed
        }
    
        foreach ($this->data['classes'] as $session) {
            if ($session['sourcedId'] === $sourcedId) {
                return $session;
            }
        }
    
        return null; // or handle the case where the session is not found
    }


    public function getCourse($sourcedId) {
        if (!$sourcedId) {
            return null; // or handle the error as needed
        }
    
        foreach ($this->data['courses'] as $session) {
            if ($session['sourcedId'] === $sourcedId) {
                return $session;
            }
        }
    
        return null; // or handle the case where the session is not found
    }


    public function getEnrollment($sourcedId) {
        if (!$sourcedId) {
            return null; // or handle the error as needed
        }
    
        foreach ($this->data['enrollments'] as $session) {
            if ($session['sourcedId'] === $sourcedId) {
                return $session;
            }
        }
    
        return null; // or handle the case where the session is not found
    }


    public function getOrg($sourcedId) {
        if (!$sourcedId) {
            return null; // or handle the error as needed
        }
    
        foreach ($this->data['orgs'] as $session) {
            if ($session['sourcedId'] === $sourcedId) {
                return $session;
            }
        }
    
        return null; // or handle the case where the session is not found
    }
    

    public function getSchool($name) {
        if (!$name) {
            return null; // or handle the error as needed
        }
    
        foreach ($this->data['orgs'] as $session) {
            if ($session['name'] === $name) {
                return $session;
            }
        }
    
        return null; // or handle the case where the session is not found
    }


    public function getStudent($sourcedId) {
        if (!$sourcedId) {
            return null; // or handle the error as needed
        }
    
        foreach ($this->data['users'] as $user) {
            if ($user['role'] === 'student' && $user['sourcedId'] === $sourcedId) {
                return $user;
            }
        }
    
        return null; // or handle the case where the student is not found
    }


    public function getTeacher($sourcedId) {
        if (!$sourcedId) {
            return null; // or handle the error as needed
        }
    
        foreach ($this->data['users'] as $user) {
            if ($user['role'] === 'teacher' && $user['sourcedId'] === $sourcedId) {
                return $user;
            }
        }
    
        return null; // or handle the case where the teacher is not found
    }

    public function getUser($sourcedId) {
        if (!$sourcedId) {
            return null; // or handle the error as needed
        }
    
        foreach ($this->data['users'] as $session) {
            if ($session['sourcedId'] === $sourcedId) {
                return $session;
            }
        }
    
        return null; // or handle the case where the session is not found
    }

    public function getCoursesForSchool($schoolSourcedId) {
        if (!$schoolSourcedId) {
            return null; // or handle the error as needed
        }

        $filteredCourses = array_filter($this->data['courses'], function($course) use ($schoolSourcedId) {
            return $course['orgSourcedId'] === $schoolSourcedId;
        });

        $sourcedIds = array_map(function($course) {
            return $course['sourcedId'];
        }, $filteredCourses);

        return $sourcedIds;
    }

    public function getEnrollmentsForSchool($schoolSourcedId) {
        if (!$schoolSourcedId) {
            return null; // or handle the error as needed
        }

        $filteredEnrollments = array_filter($this->data['enrollments'], function($enrollment) use ($schoolSourcedId) {
            return $enrollment['schoolSourcedId'] === $schoolSourcedId;
        });

        return array_values($filteredEnrollments); 
    }

    public function getStudentsForSchool($schoolSourcedId) {
        if (!$schoolSourcedId) {
            return null; // or handle the error as needed
        }

        $filteredStudents = array_filter($this->data['users'], function($user) use ($schoolSourcedId) {
            return $user['role'] === 'student' && $user['orgSourcedIds'] === $schoolSourcedId;
        });

        return array_values($filteredStudents);
    }

    public function getTeachersForSchool($schoolSourcedId) {
        if (!$schoolSourcedId) {
            return null; // or handle the error as needed
        }

        $filteredTeachers = array_filter($this->data['users'], function($user) use ($schoolSourcedId) {
            return $user['role'] === 'teacher' && $user['orgSourcedIds'] === $schoolSourcedId;
        });

        return array_values($filteredTeachers);
    }

    public function getTermsForSchool($schoolSourcedId) {
        if (!$schoolSourcedId) {
            return null; // or handle the error as needed
        }

        $filteredTerms = array_filter($this->data['classes'], function($class) use ($schoolSourcedId) {
            return $class['schoolSourcedId'] === $schoolSourcedId;
        });

        $termSourcedIds = array_unique(array_map(function($class) {
            return $class['termSourcedIds'];
        }, $filteredTerms));

        return $termSourcedIds;
    }

    public function getClassesForTerm($termSourcedId) {
        if (!$termSourcedId) {
            return null; // or handle the error as needed
        }

        $filteredClasses = array_filter($this->data['classes'], function($class) use ($termSourcedId) {
            return $class['termSourcedIds'] === $termSourcedId;
        });

        return array_values($filteredClasses); 
    }

    public function getClassesForCourse($courseSourcedId) {
        if (!$courseSourcedId) {
            return null; // or handle the error as needed
        }

        $filteredClasses = array_filter($this->data['classes'], function($class) use ($courseSourcedId) {
            return $class['courseSourcedId'] === $courseSourcedId;
        });

        return array_values($filteredClasses);
    }
}