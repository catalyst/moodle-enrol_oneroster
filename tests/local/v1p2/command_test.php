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
 * @copyright  QUT Capstone Team - Abhinav Gandham, Harrison Dyba, Jonathon Foo, Khushi Patel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster\tests\local\v1p2;


require_once('/var/www/moodle/enrol/oneroster/tests/local/command_test.php');
use enrol_oneroster\tests\local\command_test as command_test_version_one;

/**
 * One Roster tests for the `command` class.
 *
 * @package    enrol_oneroster
 * @copyright  QUT Capstone Team - Abhinav Gandham, Harrison Dyba, Jonathon Foo, Khushi Patel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers  \enrol_oneroster\local\command
 */
class command_test extends command_test_version_one{
     /**
     * Test the URL construction via the constructor.
     *
     * @dataProvider param_and_url_provider
     * @param   string $url
     * @param   array|null $params
     * @param   string $expectedurl
     * @param   array $finalparams
     */
    public function test_construct_url($url, $params, $expectedurl, array $finalparams): void {
        $endpoint = $this->getMockBuilder(endpoint::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get_url_for_command'])
            ->getMock();

        $endpoint
            ->method('get_url_for_command')
            ->will($this->willReturnArgument(1));

        $command = new command(
            $endpoint,
            $url,
            'someMethod',
            'Description of some example test method',
            null,
            null,
            null,
            $params
        );

        $this->assertSame($expectedurl, $command->get_url(''));

        $this->assertIsArray($command->get_params());
        $this->assertSame($finalparams, $command->get_params());
    }

     /**
     * Test the URL construction via the constructor when the params and URL or incorrect.
     *
     * @dataProvider invalid_param_and_url_provider
     * @param   string $url
     * @param   array|null $params
     */
    public function test_construct_url_invalid_params($url, $params): void {
        $endpoint = $this->getMockBuilder(endpoint::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['get_url_for_command'])
            ->getMock();

        $endpoint
            ->method('get_url_for_command')
            ->will($this->willReturnArgument(1));

        $this->expectException(\OutOfRangeException::class);
        $command = new command(
            $endpoint,
            $url,
            'someMethod',
            'Description of some example test method',
            null,
            null,
            null,
            $params
        );
    }

    /**
     * Ensure that the get_collection_names function return the list of possible collections.
     *
     * @dataProvider get_collection_names_provider
     * @param   array|null $collectionnames
     */
    public function test_get_collections(?array $collectionnames): void {
        $endpoint = $this->getMockBuilder(endpoint::class)
            ->disableOriginalConstructor()
            ->getMock();

        $command = new command(
            $endpoint,
            '/someMethod',
            'someMethod',
            'Description of some example test method',
            $collectionnames,
            null,
            null,
            []
        );

        $this->assertEquals($collectionnames, $command->get_collection_names());
    }

     /**
     * Ensure that the is_collection function returns correctly for a range of collection values.
     *
     * @dataProvider is_collection_provider
     * @param   array|null $collectionnames
     * @param   bool $iscollection
     */
    public function test_is_collection(?array $collectionnames, bool $iscollection): void {
        $endpoint = $this->getMockBuilder(endpoint::class)
            ->disableOriginalConstructor()
            ->getMock();

        $command = new command(
            $endpoint,
            '/someMethod',
            'someMethod',
            'Description of some example test method',
            $collectionnames,
            null,
            null,
            []
        );

        $this->assertEquals($iscollection, $command->is_collection());
    }

     /**
     * Tests for `get_method` function.
     */
    public function test_get_method(): void {
        $endpoint = $this->getMockBuilder(endpoint::class)
            ->disableOriginalConstructor()
            ->getMock();

        $command = new command(
            $endpoint,
            '/testMethod',
            'someMethod',
            'Description of some example test method',
            null,
            null,
            null,
            []
        );

        $this->assertEquals('someMethod', $command->get_method());
    }

    /**
     * Tests for `get_description` function.
     */
    public function test_get_description(): void {
        $endpoint = $this->getMockBuilder(endpoint::class)
            ->disableOriginalConstructor()
            ->getMock();

        $command = new command(
            $endpoint,
            '/testDescription',
            'someDescription',
            'Description of some example test description',
            null,
            null,
            null,
            []
        );

        $this->assertEquals('Description of some example test description', $command->get_description());
    }
}
