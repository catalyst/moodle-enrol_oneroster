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
namespace enrol_oneroster;

use \enrol_oneroster\local\csv_client_helper;
use \enrol_oneroster\local\csv_client_const_helper;
use \enrol_oneroster\csv_test_helper;

/**
 * One Roster tests for the client_helper class.
 *
 * @package    enrol_oneroster
 * @copyright  Gustavo Amorim De Almeida, Ruben Cooper, Josh Bateson, Brayden Porter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers  \enrol_oneroster\local\csv_client_helper
 */
class process_csv_data_type_validation_test extends \advanced_testcase {
    /**
     * The path to the test directory.
     *
     * @var string
     */
    private $testdir;

    /**
     * Set up the test environment.
     */
    protected function setUp(): void {
        parent::setUp();

        // Create a temporary directory for the test.
        $this->testdir = make_temp_directory('csvtest_dir');

        // Use the helper class to set up CSV files.
        csv_test_helper::setupcsvfiles($this->testdir);
    }

    /**
     * Test the validate_csv_data_types method.
     *
     * @covers \enrol_oneroster\local\csv_client_helper::validate_csv_data_types
     */
    public function test_validatecsvdatatypes(): void {
        $result = csv_client_helper::validate_csv_data_types($this->testdir);

        $this->assertArrayHasKey('is_valid', $result);
        $this->assertTrue($result['is_valid']);
        $this->assertEmpty($result['invalid_files']);
    }

    /**
     * Test the get_data_types function.
     *
     * @covers \enrol_oneroster\local\csv_client_helper::get_data_types
     */
    public function test_getdatatypes() {
        // Test academicSessions.csv data types.
        $result = csv_client_helper::get_data_types(csv_client_const_helper::FILE_ACADEMIC_SESSIONS);
        $expecteddatatypes = csv_client_helper::get_file_datatypes();
        $expected = $expecteddatatypes[csv_client_const_helper::FILE_ACADEMIC_SESSIONS];
        $this->assertEquals($expected, $result, 'The expected data types for academicSessions.csv do not match.');

        // Test classes.csv data types.
        $result = csv_client_helper::get_data_types(csv_client_const_helper::FILE_CLASSES);
        $expecteddatatypes = csv_client_helper::get_file_datatypes();
        $expected = $expecteddatatypes[csv_client_const_helper::FILE_CLASSES];
        $this->assertEquals($expected, $result, 'The expected data types for classes.csv do not match.');

        // Test file with no data types defined.
        $result = csv_client_helper::get_data_types('unknown.csv');
        $expected = [];
        $this->assertEquals($expected, $result, 'The expected data types for unknown.csv should be an empty array.');
    }

    /**
     * Test the determine_data_type function.
     *
     * @covers \enrol_oneroster\local\csv_client_helper::determine_data_type
     */
    public function test_determinedatatype() {
        $result = csv_client_helper::determine_data_type('as-trm-222-1234', [csv_client_const_helper::DATATYPE_GUID, csv_client_const_helper::DATATYPE_STRING]);
        $this->assertEquals(csv_client_const_helper::DATATYPE_GUID, $result, 'The expected data type for sourcedId should be guid.');

        $result = csv_client_helper::determine_data_type('active', [csv_client_const_helper::DATATYPE_ENUM_STATUS]);
        $this->assertEquals(csv_client_const_helper::DATATYPE_ENUM_STATUS, $result, 'The expected data type for status should be enum_status.');

        $result = csv_client_helper::determine_data_type('', [csv_client_const_helper::DATATYPE_GUID, csv_client_const_helper::DATATYPE_NULL]);
        $this->assertEquals(csv_client_const_helper::DATATYPE_NULL, $result, 'The expected data type for an empty value should be null.');

        $result = csv_client_helper::determine_data_type('2023-05-01T18:25:43.511Z', [csv_client_const_helper::DATATYPE_DATETIME, csv_client_const_helper::DATATYPE_STRING]);
        $this->assertEquals(csv_client_const_helper::DATATYPE_DATETIME, $result, 'The expected data type for dateLastModified should be datetime.');

        $result = csv_client_helper::determine_data_type('invalid_guid_@!', [csv_client_const_helper::DATATYPE_GUID]);
        $this->assertEquals('unknown', $result, 'The expected data type for an invalid GUID should be unknown.');
    }

    /**
     * Test the is_valid_human_readable_string function.
     *
     * @covers \enrol_oneroster\local\csv_client_helper::is_valid_human_readable_string
     */
    public function test_is_valid_human_readable_string() {
        $result = csv_client_helper::is_valid_human_readable_string('John Doe');
        $this->assertTrue($result, 'The string "John Doe" should be a valid human readable string.');

        $result = csv_client_helper::is_valid_human_readable_string('John Doe 123');
        $this->assertTrue($result, 'The string "John Doe 123" should not be a valid human readable string.');

        $result = csv_client_helper::is_valid_human_readable_string('John Doe$');
        $this->assertFalse($result, 'The string "John Doe$" should not be a valid human readable string.');

        $result = csv_client_helper::is_valid_human_readable_string('John Doe%');
        $this->assertFalse($result, 'The string "John Doe%" should not be a valid human readable string.');

        $result = csv_client_helper::is_valid_human_readable_string('John Doe^');
        $this->assertFalse($result, 'The string "John Doe^" should not be a valid human readable string.');
    }

    /**
     * Test the is_int_type function.
     *
     * @covers \enrol_oneroster\local\csv_client_helper::is_int_type
     */
    public function test_is_int_type() {
        $result = csv_client_helper::is_int_type('123');
        $this->assertTrue($result, 'The string "123" should be an integer.');

        $result = csv_client_helper::is_int_type('123.45');
        $this->assertFalse($result, 'The string "123.45" should not be an integer.');

        $result = csv_client_helper::is_int_type('!');
        $this->assertFalse($result, 'The string "!" should not be an integer.');

        $result = csv_client_helper::is_int_type('A');
        $this->assertFalse($result, 'The string "A" should not be an integer.');
    }

    /**
     * Test the is_list_of_strings function.
     *
     * @covers \enrol_oneroster\local\csv_client_helper::is_list_of_strings
     */
    public function test_is_list_of_strings() {
        $result = csv_client_helper::is_list_of_strings('Math, Science, History');
        $this->assertTrue($result, 'The string "Math, Science, History" should be a list of strings.');

        $result = csv_client_helper::is_list_of_strings('Math');
        $this->assertTrue($result, 'The string "Math" should be a list of strings.');

    }

    /**
     * Test the is_valid_subject_codes function.
     *
     * @covers \enrol_oneroster\local\csv_client_helper::is_valid_subject_codes
     */
    public function test_is_valid_subject_codes() {
        $result = csv_client_helper::is_valid_subject_codes('Math123, Science123, History123');
        $this->assertTrue($result, 'The string "Math, Science, History" should be a valid list of subject codes.');

        $result = csv_client_helper::is_valid_subject_codes('Math123');
        $this->assertTrue($result, 'The string "Math" should be a valid list of subject codes.');
    }

    /**
     * Test the is_valid_periods function.
     *
     * @covers \enrol_oneroster\local\csv_client_helper::is_valid_periods
     */
    public function test_is_valid_periods() {
        $result = csv_client_helper::is_valid_periods('1, 2, 3');
        $this->assertTrue($result, 'The string "1, 2, 3" should be a valid list of periods.');

        $result = csv_client_helper::is_valid_periods('1');
        $this->assertTrue($result, 'The string "1" should be a valid list of periods.');

        $result = csv_client_helper::is_valid_periods('A');
        $this->assertFalse($result, 'The string "A" should not be a valid list of periods.');

        $result = csv_client_helper::is_valid_periods('1, A, 3');
        $this->assertFalse($result, 'The string "1, A, 3" should not be a valid list of periods.');
    }

    /**
     * Test the is_datetime_type function.
     *
     * @covers \enrol_oneroster\local\csv_client_helper::is_datetime_type
     */
    public function test_is_datetime_type() {
        $result = csv_client_helper::is_datetime_type('2023-05-01T18:25:43.511Z');
        $this->assertTrue($result, 'The string "2023-05-01T18:25:43.511Z" should be a valid datetime.');

        $result = csv_client_helper::is_datetime_type('2023-05-01');
        $this->assertTrue($result, 'The string "2023-05-01" should not be a valid datetime.');

        $result = csv_client_helper::is_datetime_type('2023-05-01T18:25:43');
        $this->assertFalse($result, 'The string "2023-05-01T18:25:43" should not be a valid datetime.');

        $result = csv_client_helper::is_datetime_type('2023-05-01T18:25');
        $this->assertFalse($result, 'The string "2023-05-01T18:25" should not be a valid datetime.');

        $result = csv_client_helper::is_datetime_type('2023-05-01T18');
        $this->assertFalse($result, 'The string "2023-05-01T18" should not be a valid datetime.');

        $result = csv_client_helper::is_datetime_type('2023-05-01T');
        $this->assertFalse($result, 'The string "2023-05-01T" should not be a valid datetime.');

        $result = csv_client_helper::is_datetime_type('2023-05-01');
        $this->assertTrue($result, 'The string "2023-05-01" should not be a valid datetime.');

        $result = csv_client_helper::is_datetime_type('2023-05-01T18:25:43.511Z');
        $this->assertTrue($result, 'The string "2023-05-01T18:25:43.511Z" should be a valid datetime.');
    }

    /**
     * Test the is_date_type function.
     *
     * @covers \enrol_oneroster\local\csv_client_helper::is_date_type
     */
    public function test_is_date_type() {
        $result = csv_client_helper::is_date_type('2023-05-01');
        $this->assertTrue($result, 'The string "2023-05-01" should be a valid date.');

        $result = csv_client_helper::is_date_type('2023-05-01T18:25:43.511Z');
        $this->assertFalse($result, 'The string "2023-05-01T18:25:43.511Z" should not be a valid date.');

        $result = csv_client_helper::is_date_type('2023-05-01T18:25:43.511');
        $this->assertFalse($result, 'The string "2023-05-01T18:25:43.511" should not be a valid date.');

        $result = csv_client_helper::is_date_type('2023-05-01T18:25:43');
        $this->assertFalse($result, 'The string "2023-05-01T18:25:43" should not be a valid date.');

        $result = csv_client_helper::is_date_type('2023-05-01T18:25');
        $this->assertFalse($result, 'The string "2023-05-01T18:25" should not be a valid date.');

        $result = csv_client_helper::is_date_type('2023-05-01T18');
        $this->assertFalse($result, 'The string "2023-05-01T18" should not be a valid date.');

        $result = csv_client_helper::is_date_type('2023-05-01T');
        $this->assertFalse($result, 'The string "2023-05-01T" should not be a valid date.');

        $result = csv_client_helper::is_date_type('2023-05-01');
        $this->assertTrue($result, 'The string "2023-05-01" should be a valid date.');
    }

    /**
     * Test the is_guid_type function.
     *
     * @covers \enrol_oneroster\local\csv_client_helper::is_guid_type
     */
    public function test_is_guid_type() {
        $result = csv_client_helper::is_guid_type('usr-222-123456');
        $this->assertTrue($result, 'The string "usr-222-123456" should be a valid GUID.');

        $result = csv_client_helper::is_guid_type('usr-222-123456-!@#!');
        $this->assertFalse($result, 'The string "usr-222-123456-123456" should not be a valid GUID.');

        $result = csv_client_helper::is_guid_type('usr-222-123456-123456');
        $this->assertTrue($result, 'The string "usr-222-123456-123456" should not be a valid GUID.');
    }

    /**
     * Test the is_status_enum_type function.
     *
     * @covers \enrol_oneroster\local\csv_client_helper::is_status_enum_type
     */
    public function test_is_status_enum_type() {
        $result = csv_client_helper::is_status_enum_type('active');
        $this->assertTrue($result, 'The string "active" should be a valid status enum.');

        $result = csv_client_helper::is_status_enum_type('inactive');
        $this->assertTrue($result, 'The string "inactive" should be a valid status enum.');

        $result = csv_client_helper::is_status_enum_type('tobedeleted');
        $this->assertTrue($result, 'The string "tobedeleted" should not be a valid status enum.');

        $result = csv_client_helper::is_status_enum_type('deleted');
        $this->assertFalse($result, 'The string "deleted" should not be a valid status enum.');
    }

    /**
     * Test the is_type_enum function.
     *
     * @covers \enrol_oneroster\local\csv_client_helper::is_type_enum
     */
    public function test_is_type_enum() {
        $result = csv_client_helper::is_type_enum('gradingPeriod');
        $this->assertTrue($result, 'The string "gradingPeriod" should be a valid enum.');

        $result = csv_client_helper::is_type_enum('term');
        $this->assertTrue($result, 'The string "term" should be a valid enum.');

        $result = csv_client_helper::is_type_enum('semester');
        $this->assertTrue($result, 'The string "semester" should be a valid enum.');

        $result = csv_client_helper::is_type_enum('schoolYear');
        $this->assertTrue($result, 'The string "schoolYear" should be a valid enum.');

        $result = csv_client_helper::is_type_enum('invalid_enum');
        $this->assertFalse($result, 'The string "invalid_enum" should not be a valid enum.');
    }

    /**
     * Test the is_year_type function.
     *
     * @covers \enrol_oneroster\local\csv_client_helper::is_year_type
     */
    public function test_is_year_type() {
        $result = csv_client_helper::is_year_type('2023');
        $this->assertTrue($result, 'The string "2023" should be a valid year.');

        $result = csv_client_helper::is_year_type('2012');
        $this->assertTrue($result, 'The string "2012" should be a valid year.');

        $result = csv_client_helper::is_year_type('3053');
        $this->assertTrue($result, 'The string "3053" should not be a valid year.');

        $result = csv_client_helper::is_year_type('999');
        $this->assertFalse($result, 'The string "999" should not be a valid year.');
    }

    /**
     * Test the is_valid_grades function.
     *
     * @covers \enrol_oneroster\local\csv_client_helper::is_valid_grades
     */
    public function test_is_valid_grades() {
        $results = csv_client_helper::is_valid_grades('09');
        $this->assertTrue($results, 'The string "09" should be a valid list of grades.');

        $results = csv_client_helper::is_valid_grades('IT, PR, PK, TK, KG, 01, 02, 03, 04, 05, 06, 07, 08, 09, 10, 11, 12, 13, PS, UG, Other');
        $this->assertTrue($results, 'The string "IT, PR, PK, TK, KG, 01, 02, 03, 04, 05, 06, 07, 08, 09, 10, 11, 12, 13, PS, UG, Other" should be a valid list of grades.');
    }

    /**
     * Test the is_valid_grade function.
     *
     * @covers \enrol_oneroster\local\csv_client_helper::is_valid_grade
     */
    public function test_is_valid_grade() {
        $results = csv_client_helper::is_valid_grade('09');
        $this->assertTrue($results, 'The string "09" should be a valid list of grades.');

        $results = csv_client_helper::is_valid_grade('IT, PR, PK, TK, KG, 01, 02, 03, 04, 05, 06, 07, 08, 09, 10, 11, 12, 13, PS, UG, Other');
        $this->assertFalse($results, 'The string "IT, PR, PK, TK, KG, 01, 02, 03, 04, 05, 06, 07, 08, 09, 10, 11, 12, 13, PS, UG, Other" should be a valid list of grades.');
    }

    /**
     * Test the is_class_type_enum function.
     *
     * @covers \enrol_oneroster\local\csv_client_helper::is_class_type_enum
     */
    public function test_is_class_type_enum() {
        $result = csv_client_helper::is_class_type_enum('Scheduled');
        $this->assertTrue($result, 'The string "Scheduled" should be a valid class type enum.');

        $result = csv_client_helper::is_class_type_enum('homeRoom');
        $this->assertTrue($result, 'The string "homeRoom" should be a valid class type enum.');

        $result = csv_client_helper::is_class_type_enum('notValid');
        $this->assertFalse($result, 'The string "notValid" should not be a valid class type enum.');
    }

    /**
     * Test the is_valid_guid_list function.
     *
     * @covers \enrol_oneroster\local\csv_client_helper::is_valid_guid_list
     */
    public function test_is_valid_guid_list() {
        $result = csv_client_helper::is_valid_guid_list('usr-222-123456, usr-222-123456');
        $this->assertTrue($result, 'The string "usr-222-123456, usr-222-123456" should be a valid list of GUIDs.');

        $result = csv_client_helper::is_valid_guid_list('usr-222-123456');
        $this->assertTrue($result, 'The string "usr-222-123456" should be a valid list of GUIDs.');

        $result = csv_client_helper::is_valid_guid_list('usr-222-123456, usr-222-123456, usr-222-123!@456');
        $this->assertFalse($result, 'The string "usr-222-123456, usr-222-123456, usr-222-123!@456" should not be a valid list of GUIDs.');
    }

    /**
     * Test the is_role_enum function.
     *
     * @covers \enrol_oneroster\local\csv_client_helper::is_role_enum
     */
    public function test_is_role_enum() {
        $result = csv_client_helper::is_role_enum('teacher');
        $this->assertTrue($result, 'The string "teacher" should be a valid role enum.');

        $result = csv_client_helper::is_role_enum('student');
        $this->assertTrue($result, 'The string "student" should be a valid role enum.');

        $result = csv_client_helper::is_role_enum('administrator');
        $this->assertTrue($result, 'The string "administrator" should be a valid role enum.');

        $result = csv_client_helper::is_role_enum('proctor');
        $this->assertTrue($result, 'The string "proctor" should be a valid role enum.');

        $result = csv_client_helper::is_role_enum('parent');
        $this->assertFalse($result, 'The string "parent" should not be a valid role enum.');
    }

    /**
     * Test the is_primary_enum function.
     *
     * @covers \enrol_oneroster\local\csv_client_helper::is_primary_enum
     */
    public function test_is_primary_enum() {
        $result = csv_client_helper::is_primary_enum('TRUE');
        $this->assertTrue($result, 'The string "TRUE" should be a valid primary enum.');

        $result = csv_client_helper::is_primary_enum('FALSE');
        $this->assertTrue($result, 'The string "FALSE" should be a valid primary enum.');

        $result = csv_client_helper::is_primary_enum('lose');
        $this->assertFalse($result, 'The string "lose" should not be a valid primary enum.');

        $result = csv_client_helper::is_primary_enum('false');
        $this->assertTrue($result, 'The string "false" should not be a valid primary enum.');
    }

    /**
     * Test the is_org_type_enum function.
     *
     * @covers \enrol_oneroster\local\csv_client_helper::is_org_type_enum
     */
    public function test_is_org_type_enum() {
        $result = csv_client_helper::is_org_type_enum('school');
        $this->assertTrue($result, 'The string "school" should be a valid org type enum.');

        $result = csv_client_helper::is_org_type_enum('district');
        $this->assertTrue($result, 'The string "district" should be a valid org type enum.');

        $result = csv_client_helper::is_org_type_enum('state');
        $this->assertTrue($result, 'The string "state" should be a valid org type enum.');

        $result = csv_client_helper::is_org_type_enum('department');
        $this->assertTrue($result, 'The string "department" should be a valid org type enum.');

        $result = csv_client_helper::is_org_type_enum('local');
        $this->assertTrue($result, 'The string "local" should be a valid org type enum.');

        $result = csv_client_helper::is_org_type_enum('national');
        $this->assertTrue($result, 'The string "national" should be a valid org type enum.');

        $result = csv_client_helper::is_org_type_enum('university');
        $this->assertFalse($result, 'The string "university" should not be a valid org type enum.');
    }

    /**
     * Test the is_email_type function.
     *
     * @covers \enrol_oneroster\local\csv_client_helper::is_email_type
     */
    public function test_is_email_type() {
        $result = csv_client_helper::is_email_type('josh@example.com');
        $this->assertTrue($result, 'The string "josh@example.com" should be a valid email.');

        $result = csv_client_helper::is_email_type('josh@example');
        $this->assertFalse($result, 'The string "josh@example" should not be a valid email.');

        $result = csv_client_helper::is_email_type('josh@');
        $this->assertFalse($result, 'The string "josh@" should not be a valid email.');
    }

    /**
     * Test the is_user_id function.
     *
     * @covers \enrol_oneroster\local\csv_client_helper::is_valid_user_id
     */
    public function test_is_user_id() {
        $result = csv_client_helper::is_valid_user_id('{LDAP:12},{LTI:15},{Fed:23}');
        $this->assertTrue($result, 'The string "{LDAP:12},{LTI:15},{Fed:23}" should be a valid user ID.');

        $result = csv_client_helper::is_valid_user_id('{LDAP:12}');
        $this->assertTrue($result, 'The string "{LDAP:12}" should be a valid user ID.');

        $result = csv_client_helper::is_valid_user_id('{InvalidFormat}');
        $this->assertFalse($result, 'The string "{InvalidFormat}" should not be a valid user ID.');

        $result = csv_client_helper::is_valid_user_id('{LDAP:12},{LTI:15},{Fed:}');
        $this->assertFalse($result, 'The string "{LDAP:12},{LTI:15},{Fed:}" should not be a valid user ID because the last entry is missing a value.');
    }

    /**
     * Test the is_role_user_enum function.
     *
     * @covers \enrol_oneroster\local\csv_client_helper::is_role_user_enum
     */
    public function test_is_role_user_enum() {
        $result = csv_client_helper::is_role_user_enum('teacher');
        $this->assertTrue($result, 'The string "teacher" should be a valid role user enum.');

        $result = csv_client_helper::is_role_user_enum('aide');
        $this->assertTrue($result, 'The string "aide" should be a valid role user enum.');

        $result = csv_client_helper::is_role_user_enum('guardian');
        $this->assertTrue($result, 'The string "guardian" should be a valid role user enum.');

        $result = csv_client_helper::is_role_user_enum('relative');
        $this->assertTrue($result, 'The string "relative" should be a valid role user enum.');

        $result = csv_client_helper::is_role_user_enum('student');
        $this->assertTrue($result, 'The string "student" should be a valid role user enum.');

        $result = csv_client_helper::is_role_user_enum('parent');
        $this->assertTrue($result, 'The string "parent" should be a valid role user enum.');

        $result = csv_client_helper::is_role_user_enum('administrator');
        $this->assertTrue($result, 'The string "administrator" should be a valid role user enum.');

        $result = csv_client_helper::is_role_user_enum('proctor');
        $this->assertTrue($result, 'The string "proctor" should be a valid role user enum.');

        $result = csv_client_helper::is_role_user_enum('invalid');
        $this->assertFalse($result, 'The string "invalid" should not be a valid role user enum.');
    }
}
