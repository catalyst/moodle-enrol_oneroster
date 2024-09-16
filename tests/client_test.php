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
 * One Roster Enrolment Client Unit tests.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster;

use advanced_testcase;
use enrol_oneroster\local\command;
use enrol_oneroster\client_helper;
use enrol_oneroster\local\endpoint;

/**
 * One Roster tests for the client_helper class.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers  enrol_oneroster\client_helper
 */


 class client_csv_testcase extends advanced_testcase {

    public function test_execute(): void {
        global $DB;
        $this->resetAfterTest(true);
    
        // Include the helper file
        require_once(__DIR__ . '/../csv_data_helper.php');

        // Setup fixtures here.

        // Get data from the helper
        $manifest = csv_data_helper::get_manifest_data();
        $users = csv_data_helper::get_users_data();
        $classes = csv_data_helper::get_classes_data();
        $courses = csv_data_helper::get_courses_data();
        $orgs = csv_data_helper::get_orgs_data();
        $enrollments = csv_data_helper::get_enrollments_data();
        $academicSessions = csv_data_helper::get_academicsessions_data();
        $demographics = csv_data_helper::get_demographics_data();
        $lineItems = csv_data_helper::get_line_items_data();
        $results = csv_data_helper::get_results_data();
        $categories = csv_data_helper::get_categories_data();

        // Assuming client_helper is already defined and provides a get_csv_client() method
        $csvclient = client_helper::get_csv_client();

        // Set the data
        $csvclient->set_data(
            $manifest,
            $users,
            $classes,
            $courses,
            $orgs,
            $enrollments,
            $academicSessions,
            $demographics,
            $lineItems,
            $results,
            $categories
        );
        $csvclient->synchronise();

    }


    // public function test_getAlls(): void {
    //     global $DB;
    //     $this->resetAfterTest(true);
    
    //     // Include the helper file
    //     require_once(__DIR__ . '/../csv_data_helper.php');
    
    //     // Setup fixtures here.
    
    //     // Get data from the helper
    //     $manifest = csv_data_helper::get_manifest_data();
    //     $users = csv_data_helper::get_users_data();
    //     $classes = csv_data_helper::get_classes_data();
    //     $courses = csv_data_helper::get_courses_data();
    //     $orgs = csv_data_helper::get_orgs_data();
    //     $enrollments = csv_data_helper::get_enrollments_data();
    //     $academicsessions = csv_data_helper::get_academicSessions_data();
    //     $demographics = [];
    //     $csvclient = client_helper::get_csv_client();
    
    //     // Set the data
    //     $csvclient->set_data($users, $classes, $courses, $orgs, $enrollments, $academicsessions, $demographics);
    
    //     // Create a command for getAllAcademicSessions
    //     $command = $this->getMockBuilder(command::class)
    //         ->disableOriginalConstructor()
    //         ->getMock();
    //     $command->method('get_method')
    //         ->willReturn('getAllAcademicSessions');
    //     $result = $csvclient->execute($command);
    //     $this->assertObjectHasAttribute('AllAcademicSessions', $result);
    //     $this->assertCount(2, $result->AllAcademicSessions);
    //     $this->assertEquals('ACADSESS_01', $result->AllAcademicSessions[0]);
    //     $this->assertEquals('ACADSESS_02', $result->AllAcademicSessions[1]);


    //     // Create a command for getAllClasses
    //     $command = $this->getMockBuilder(command::class)
    //     ->disableOriginalConstructor()
    //     ->getMock();
    //     $command->method('get_method')
    //         ->willReturn('getAllClasses');
    //     $result = $csvclient->execute($command);
    //     $this->assertObjectHasAttribute('AllClasses', $result);
    //     $this->assertCount(4, $result->AllClasses);
    //     $this->assertEquals('CLASS_01', $result->AllClasses[0]);
    //     $this->assertEquals('CLASS_02', $result->AllClasses[1]);


    //     // Create a command for getAllCourses
    //     $command = $this->getMockBuilder(command::class)
    //     ->disableOriginalConstructor()
    //     ->getMock();
    //     $command->method('get_method')
    //         ->willReturn('getAllCourses');
    //     $result = $csvclient->execute($command);
    //     $this->assertObjectHasAttribute('AllCourses', $result);
    //     $this->assertCount(3, $result->AllCourses);
    //     $this->assertEquals('COURSE_01', $result->AllCourses[0]);
    //     $this->assertEquals('COURSE_02', $result->AllCourses[1]);  
        
        
    //     // Create a command for getAllEnrollments
    //     $command = $this->getMockBuilder(command::class)
    //     ->disableOriginalConstructor()
    //     ->getMock();
    //     $command->method('get_method')
    //         ->willReturn('getAllEnrollments');
    //     $result = $csvclient->execute($command);
    //     $this->assertObjectHasAttribute('AllEnrollments', $result);
    //     $this->assertCount(24, $result->AllEnrollments);
    //     $this->assertEquals('en0001', $result->AllEnrollments[0]);
    //     $this->assertEquals('en0002', $result->AllEnrollments[1]);


    //     // Create a command for getAllOrgs
    //     $command = $this->getMockBuilder(command::class)
    //     ->disableOriginalConstructor()
    //     ->getMock();
    //     $command->method('get_method')
    //         ->willReturn('getAllOrgs');
    //     $result = $csvclient->execute($command);
    //     $this->assertObjectHasAttribute('AllOrgs', $result);
    //     $this->assertCount(4, $result->AllOrgs);
    //     $this->assertEquals('ORG_01', $result->AllOrgs[0]);
    //     $this->assertEquals('ORG_02', $result->AllOrgs[1]);


    //     // Create a command for getAllSchools
    //     $command = $this->getMockBuilder(command::class)
    //     ->disableOriginalConstructor()
    //     ->getMock();
    //     $command->method('get_method')
    //         ->willReturn('getAllSchools');
    //     $result = $csvclient->execute($command);
    //     $this->assertObjectHasAttribute('AllSchools', $result);
    //     $this->assertCount(4, $result->AllSchools);
    //     $this->assertEquals('METROPOLIS ELEMENTARY', $result->AllSchools[0]);
    //     $this->assertEquals('METROPOLIS MIDDLE', $result->AllSchools[1]);
    // }



    // public function test_getAll_filtered(): void {
    //     global $DB;
    //     $this->resetAfterTest(true);
    
    //     // Include the helper file
    //     require_once(__DIR__ . '/../csv_data_helper.php');
    
    //     // Setup fixtures here.
    
    //     // Get data from the helper
    //     $manifest = csv_data_helper::get_manifest_data();
    //     $users = csv_data_helper::get_users_data();
    //     $classes = csv_data_helper::get_classes_data();
    //     $courses = csv_data_helper::get_courses_data();
    //     $orgs = csv_data_helper::get_orgs_data();
    //     $enrollments = csv_data_helper::get_enrollments_data();
    //     $academicsessions = csv_data_helper::get_academicSessions_data();
    //     $demographics = [];
    //     $csvclient = client_helper::get_csv_client();
    
    //     // Set the data
    //     $csvclient->set_data($users, $classes, $courses, $orgs, $enrollments, $academicsessions, $demographics);

    //     // Create a command for getAllStudents
    //     $command = $this->getMockBuilder(command::class)
    //     ->disableOriginalConstructor()
    //     ->getMock();
    //     $command->method('get_method')
    //         ->willReturn('getAllStudents');
    //     $result = $csvclient->execute($command);
    //     $this->assertObjectHasAttribute('AllStudents', $result);
    //     $this->assertCount(16, $result->AllStudents);
    //     $this->assertEquals('student_01', $result->AllStudents[0]);
    //     $this->assertEquals('student_02', $result->AllStudents[1]);


    //     $command = $this->getMockBuilder(command::class)
    //     ->disableOriginalConstructor()
    //     ->getMock();
    //     $command->method('get_method')
    //         ->willReturn('getAllTeachers');
    //     $result = $csvclient->execute($command);
    //     $this->assertObjectHasAttribute('AllTeachers', $result);
    //     $this->assertCount(3, $result->AllTeachers);
    //     $this->assertEquals('teacher_01', $result->AllTeachers[0]);
    //     $this->assertEquals('teacher_02', $result->AllTeachers[1]);
    //     $this->assertEquals('teacher_03', $result->AllTeachers[2]);


    //     // Create a command for getAllTerms
    //     $command = $this->getMockBuilder(command::class)
    //         ->disableOriginalConstructor()
    //         ->getMock();
    //     $command->method('get_method')
    //         ->willReturn('getAllTerms');
    //     $result = $csvclient->execute($command);
    //     $this->assertObjectHasAttribute('AllTerms', $result);
    //     $this->assertCount(1, $result->AllTerms);
    //     $this->assertEquals('ACADSESS_01', $result->AllTerms[0]);


    //     // Create a command for getAllUsers
    //     $command = $this->getMockBuilder(command::class)
    //         ->disableOriginalConstructor()
    //         ->getMock();
    //     $command->method('get_method')
    //         ->willReturn('getAllUsers');
    //     $result = $csvclient->execute($command);
    //     $this->assertObjectHasAttribute('AllUsers', $result);
    //     $this->assertCount(23, $result->AllUsers);
    //     $this->assertEquals('student_01', $result->AllUsers[0]);
    //     $this->assertEquals('student_02', $result->AllUsers[1]);
    // }



    // public function test_gets(): void {
    //     global $DB;
    //     $this->resetAfterTest(true);

    //     // Include the helper file
    //     require_once(__DIR__ . '/../csv_data_helper.php');

    //     // Setup fixtures here.

    //     // Get data from the helper
    //     $manifest = csv_data_helper::get_manifest_data();
    //     $users = csv_data_helper::get_users_data();
    //     $classes = csv_data_helper::get_classes_data();
    //     $courses = csv_data_helper::get_courses_data();
    //     $orgs = csv_data_helper::get_orgs_data();
    //     $enrollments = csv_data_helper::get_enrollments_data();
    //     $academicsessions = csv_data_helper::get_academicSessions_data();
    //     $demographics = [];
    //     $csvclient = client_helper::get_csv_client();
    
    //     // Set the data
    //     $csvclient->set_data($users, $classes, $courses, $orgs, $enrollments, $academicsessions, $demographics);

 
    //     // Call the getAcademicSession method directly
    //     $result = $csvclient->getAcademicSession('ACADSESS_01');
    //     $this->assertNotNull($result);
    //     $this->assertEquals('ACADSESS_01', $result['sourcedId']);
    //     $this->assertArrayHasKey('title', $result);
    //     $this->assertEquals('Fall 2019', $result['title']);


    //     // Call the getClass method directly
    //     $result = $csvclient->getClass('CLASS_03');
    //     $this->assertNotNull($result);
    //     $this->assertEquals('CLASS_03', $result['sourcedId']);
    //     $this->assertArrayHasKey('title', $result);
    //     $this->assertEquals('Digital Photography - Dr. Spock', $result['title']);


    //     // Call the getCourse method directly
    //     $result = $csvclient->getCourse('COURSE_01');
    //     $this->assertNotNull($result);
    //     $this->assertEquals('COURSE_01', $result['sourcedId']);
    //     $this->assertArrayHasKey('title', $result);
    //     $this->assertEquals('1st Grade Math Sem A', $result['title']);


    //     // Call the getEnrollment method directly
    //     $result = $csvclient->getEnrollment('en0001');
    //     $this->assertNotNull($result);
    //     $this->assertEquals('en0001', $result['sourcedId']);
    //     $this->assertArrayHasKey('userSourcedId', $result);
    //     $this->assertEquals('student_01', $result['userSourcedId']);


    //     // Call the getEnrollment method directly
    //     $result = $csvclient->getOrg('ORG_01');
    //     $this->assertNotNull($result);
    //     $this->assertEquals('ORG_01', $result['sourcedId']);
    //     $this->assertArrayHasKey('name', $result);
    //     $this->assertEquals('METROPOLIS ELEMENTARY', $result['name']);
        

    //     // Call the getSchool method directly
    //     $result = $csvclient->getSchool('METROPOLIS ELEMENTARY');
    //     $this->assertNotNull($result);
    //     $this->assertEquals('ORG_01', $result['sourcedId']);
    //     $this->assertArrayHasKey('name', $result);
    //     $this->assertEquals('METROPOLIS ELEMENTARY', $result['name']);


    //     // Call the getSchool method directly
    //     $result = $csvclient->getStudent('student_01');
    //     $this->assertNotNull($result);
    //     $this->assertEquals('student_01', $result['sourcedId']);
    //     $this->assertArrayHasKey('givenName', $result);
    //     $this->assertEquals('Clark', $result['givenName']);


    //     // Call the getSchool method directly
    //     $result = $csvclient->getTeacher('teacher_01');
    //     $this->assertNotNull($result);
    //     $this->assertEquals('teacher_01', $result['sourcedId']);
    //     $this->assertArrayHasKey('givenName', $result);
    //     $this->assertEquals('Robert', $result['givenName']);


    //     // Call the getSchool method directly
    //     $result = $csvclient->getUser('teacher_01');
    //     $this->assertNotNull($result);
    //     $this->assertEquals('teacher_01', $result['sourcedId']);
    //     $this->assertArrayHasKey('givenName', $result);
    //     $this->assertEquals('Robert', $result['givenName']);

    // }


    // public function test_get_subsection(): void {
    //     global $DB;
    //     $this->resetAfterTest(true);

    //     // Include the helper file
    //     require_once(__DIR__ . '/../csv_data_helper.php');

    //     // Setup fixtures here.

    //     // Get data from the helper
    //     $manifest = csv_data_helper::get_manifest_data();
    //     $users = csv_data_helper::get_users_data();
    //     $classes = csv_data_helper::get_classes_data();
    //     $courses = csv_data_helper::get_courses_data();
    //     $orgs = csv_data_helper::get_orgs_data();
    //     $enrollments = csv_data_helper::get_enrollments_data();
    //     $academicsessions = csv_data_helper::get_academicSessions_data();
    //     $demographics = [];
    //     $csvclient = client_helper::get_csv_client();
    
    //     // Set the data
    //     $csvclient->set_data($users, $classes, $courses, $orgs, $enrollments, $academicsessions, $demographics);

    //     // Call the getCoursesForSchool method 
    //     $result = $csvclient->getCoursesForSchool('ORG_01');
    //     $this->assertNotNull($result);
    //     $this->assertIsArray($result);
    //     $this->assertCount(2, $result);
    //     $this->assertContains('COURSE_01', $result);
    //     $this->assertContains('COURSE_02', $result);


    //     // Call the getEnrollmentsForSchool method 
    //     $result = $csvclient->getEnrollmentsForSchool('ORG_01');
    //     $this->assertNotNull($result);
    //     $this->assertIsArray($result);
    //     $this->assertCount(17, $result); 
    //     $this->assertEquals('en0001', $result[0]['sourcedId']);
    //     $this->assertEquals('en0002', $result[1]['sourcedId']);


    //     // Call the getStudentsForSchool method
    //     $result = $csvclient->getStudentsForSchool('ORG_01');
    //     $this->assertNotNull($result);
    //     $this->assertIsArray($result);
    //     $this->assertCount(10, $result); 
    //     $this->assertEquals('student_01', $result[0]['sourcedId']);
    //     $this->assertEquals('student_02', $result[1]['sourcedId']);


    //     // Call the getTeachersForSchool method
    //     $result = $csvclient->getTeachersForSchool('ORG_01');
    //     $this->assertNotNull($result);
    //     $this->assertIsArray($result);
    //     $this->assertCount(2, $result); 
    //     $this->assertEquals('teacher_01', $result[0]['sourcedId']);
    //     $this->assertEquals('teacher_02', $result[1]['sourcedId']);

    //     // Call the getTermsForSchool method
    //     $result = $csvclient->getTermsForSchool('ORG_01');
    //     $this->assertNotNull($result);
    //     $this->assertIsArray($result);
    //     $this->assertCount(1, $result); 
    //     $this->assertEquals('ACADSESS_01', $result[0]);

    //     // Call the getClassesForTerm method
    //     $result = $csvclient->getClassesForTerm('ACADSESS_01');
    //     $this->assertNotNull($result);
    //     $this->assertIsArray($result);
    //     $this->assertCount(4, $result); 
    //     $this->assertEquals('CLASS_01', $result[0]['sourcedId']);
    //     $this->assertEquals('CLASS_02', $result[1]['sourcedId']);

    //     // Call the getClassesForCourse method 
    //     $result = $csvclient->getClassesForCourse('COURSE_01');
    //     $this->assertNotNull($result);
    //     $this->assertIsArray($result);
    //     $this->assertCount(2, $result); 
    //     $this->assertEquals('CLASS_01', $result[0]['sourcedId']);
    //     $this->assertEquals('CLASS_02', $result[1]['sourcedId']);

    // }
}

 