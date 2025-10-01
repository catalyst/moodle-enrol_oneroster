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
// MERCHANTABILITY or FITNESS FOR ANY PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * One Roster Enrolment Client Unit tests.
 *
 * @package    enrol_oneroster
 * @copyright  QUT Capstone Team - Abhinav Gandham, Harrison Dyba, Jonathon Foo, Khushi Patel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster\tests\local\v1p2\entities;


use stdClass;
use coding_exception;

require_once('/var/www/moodle/enrol/oneroster/classes/local/entities/role.php');
require_once(__DIR__ . '/../oneroster_testcase.php');
use enrol_oneroster\tests\local\v1p2\oneroster_testcase;
use enrol_oneroster\classes\local\entities\role;

class role_test extends oneroster_testcase {
    /**
     * Test the properties of the entity.
     */
    public function test_entity(): void {
        $container = $this->get_mocked_container();

        $entity = new role($container, '12345');

        $this->assertInstanceOf(role::class, $entity);
    }

     /**
     * Ensure that preloading of entity data means that endpoint is not called.
     */
    public function test_preload(): void {
        $container = $this->get_mocked_container();

        $rostering = $this->mock_rostering_endpoint($container, ['execute']);
        $rostering
            ->expects($this->never())
            ->method('execute');
        $container->method('get_rostering_endpoint')->willReturn($rostering);

        $entity = new role($container, 'role-123', (object) [
            'sourcedId' => 'preloadedRole'
        ]);

        // The get_data() function should contain the data.
        $data = $entity->get_data();
        $this->assertIsObject($data);
        $this->assertEquals('preloadedRole', $data->sourcedId);

        // And it can be retrieved via `get()`.
        $this->assertEquals('preloadedRole', $entity->get('sourcedId'));

        // Non-existent objects return null.
        $this->assertNull($entity->get('notAField'));
    }

     /**
     * Ensure that the get function calls the web service correctly.
     */
    public function test_get(): void {
        $container = $this->get_mocked_container();

        $rostering = $this->mock_rostering_endpoint($container, ['execute']);
        $rostering
            ->expects($this->once())
            ->method('execute')
            ->willReturn((object) [
                'role' => (object) [
                    'sourcedId' => 'role-123',
                    'userSourcedId' => 'user-456',
                    'orgSourcedId' => 'org-789',
                    'roleType' => 'primary',
                    'role' => 'guardian',
                    'status' => 'active',
                    'dateLastModified' => '2024-01-01T00:00:00Z',
                ],
            ]);
        $container->method('get_rostering_endpoint')->willReturn($rostering);

        $entity = new role($container, 'role-123');

        // The get_data() function should contain the data.
        $data = $entity->get_data();
        $this->assertIsObject($data);
        $this->assertEquals('role-123', $data->sourcedId);
        $this->assertEquals('user-456', $data->userSourcedId);
        $this->assertEquals('org-789', $data->orgSourcedId);
        $this->assertEquals('primary', $data->roleType);
        $this->assertEquals('guardian', $data->role);
        $this->assertEquals('active', $data->status);
        $this->assertEquals('2024-01-01T00:00:00Z', $data->dateLastModified);


        // And it can be retrieved via `get()` without incurring another fetch.
        $this->assertEquals('role-123', $entity->get('sourcedId'));
        $this->assertEquals('user-456', $entity->get('userSourcedId'));

        // Non-existent objects return null.
        $this->assertNull($entity->get('fake'));
    }

     /**
     * An coding_exception exception should be thrown when the data does not contain a 'userProfile' attribute.
     */
    public function test_get_missing_structure(): void {
        $container = $this->get_mocked_container();

        $rostering = $this->mock_rostering_endpoint($container, ['execute']);
        $rostering->method('execute')->willReturn((object) [
            'sourcedId' => 'foo',
        ]);
        $container->method('get_rostering_endpoint')->willReturn($rostering);

        $this->expectException(coding_exception::class);

        $entity = new role($container, 'role-123');
        $entity->get_data();
    }

     /**
     * Ensure that the user profile representations are correct.
     *
     * @dataProvider role_data_provider
     * @param   stdClass $data The data in the user profile
     * @param   stdClass $expected The data returned as a user profile
     */
    public function test_get_userprofile_data($data, $expected): void {
        $container = $this->get_mocked_container();

        $entity = new role($container, $data->sourcedId, $data);

        $this->assertEquals($expected, $entity->get_role_data());
    }

    /**
     * Data provider for user profile tests.
     *
     * @return array
     */
    public static function userprofile_data_provider(): array {
        return [
            'minimal_required' => [
                (object)[
                    'sourcedId'        => 'r-1',
                    'status'           => 'active',
                    'dateLastModified' => '2024-01-02T03:04:05Z',
                    'userSourcedId'    => 'u-1',
                    'roleType'         => 'primary',
                    'role'             => 'teacher',
                    'orgSourcedId'     => 'o-1',
                ],
                (object)[
                    'roleId'           => 'r-1',
                    'status'           => 'active',
                    'dateLastModified' => '2024-01-02T03:04:05Z',
                    'userSourcedId'    => 'u-1',
                    'roleType'         => 'primary',
                    'role'             => 'teacher',
                    'orgSourcedId'     => 'o-1',
                    'beginDate'        => null,
                    'endDate'          => null,
                    'userProfileSourcedId' => null,
                ],
            ],
            'with_optional_dates_and_profile' => [
                (object)[
                    'sourcedId'            => 'r-2',
                    'status'               => 'tobedeleted',
                    'dateLastModified'     => '2024-05-10T10:20:30Z',
                    'userSourcedId'        => 'u-2',
                    'roleType'             => 'secondary',
                    'role'                 => 'guardian',
                    'orgSourcedId'         => 'o-2',
                    'beginDate'            => '2024-06-01',
                    'endDate'              => '2024-12-31',
                    'userProfileSourcedId' => 'up-9',
                ],
                (object)[
                    'roleId'               => 'r-2',
                    'status'               => 'tobedeleted',
                    'dateLastModified'     => '2024-05-10T10:20:30Z',
                    'userSourcedId'        => 'u-2',
                    'roleType'             => 'secondary',
                    'role'                 => 'guardian',
                    'orgSourcedId'         => 'o-2',
                    'beginDate'            => '2024-06-01',
                    'endDate'              => '2024-12-31',
                    'userProfileSourcedId' => 'up-9',
                ],
            ],
        ];
    }
}