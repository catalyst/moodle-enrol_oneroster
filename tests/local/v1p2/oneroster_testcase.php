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
 * @copyright  QUT Capstone Team - Abhinav Gandham, Harrison Dyba, Jonathon Foo, Kushi Patel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster\tests\local\v1p2;

defined('MOODLE_INTERNAL') || die;
require_once(__DIR__ . '/../oneroster_testcase.php');

use enrol_oneroster\tests\local\oneroster_testcase as oneroster_testcase_version_one;
use enrol_oneroster\local\v1p1\container;
use enrol_oneroster\local\interfaces\container as container_interface;
use enrol_oneroster\local\interfaces\collection_factory as collection_factory_interface;
use enrol_oneroster\local\interfaces\entity_factory as entity_factory_interface;
use enrol_oneroster\local\interfaces\rostering_endpoint as rostering_endpoint_interface;
use enrol_oneroster\local\v1p2\endpoints\rostering as rostering_endpoint;
use enrol_oneroster\local\v1p2\factories\collection_factory;
use enrol_oneroster\local\v1p2\factories\entity_factory;
/**
 * One Roster Entity tests.
 *
 * @package    enrol_oneroster
 * @copyright  QUT Capstone Team - Abhinav Gandham, Harrison Dyba, Jonathon Foo, Kushi Patel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class oneroster_testcase extends oneroster_testcase_version_one{
    /**
     * Mock a container
     * @return container_interface mocked container, use following methods to build individual parts of container throughout testing
     */
    protected function get_mocked_container(): container_interface {
        $client = $this->mock_client();
        $mock = $this->getMockBuilder(container::class)
            ->setConstructorArgs([$client])
            ->onlyMethods([
            'get_client',
            ])
            ->getMock();

        $mock->method('get_client')->willReturn($client);
        return $mock;
        }
    /**
     * Mock a rostering endpoint, returns it and adds it to mocked container
     *  @param container_interface $container is a mocked container
     *  @param array $mockedfunctions the rostering functions to me mocked
     * @return rostering_endpoint_interface mocked rostering endpoint 
     */
    protected function mock_rostering_endpoint(container_interface $container, array $mockfunctions): rostering_endpoint_interface {
        $mock = $this->getMockBuilder(rostering_endpoint::class)
            ->setConstructorArgs([$container])
            ->onlyMethods(array_values($mockfunctions))
            ->getMock();
    
        $container->method('get_rostering_endpoint')->willReturn($mock);

        return $mock;
    }
    /**
     * Mock a entity factory, returns it and adds it to mocked container
     *  @param container_interface $container is a mocked container
     *  @param array $mockedfunctions the rostering functions to me mocked
     * @return entity_factory_interface mocked entity factory 
     */
    protected function mock_entity_factory(container_interface $container, array $mockfunctions): entity_factory_interface {
        $mock = $this->getMockBuilder(entity_factory::class)
            ->setConstructorArgs([$container])
            ->onlyMethods(array_values($mockfunctions))
            ->getMock();

        $container->method('get_entity_factory')->willReturn($mock);

        return $mock;
    }
    
    /**
     * Mock a collection factory, returns it and adds it to mocked container
     *  @param container_interface $container is a mocked container
     *  @param array $mockedfunctions the rostering functions to me mocked
     * @return entity_colection_interface mocked entity factory 
     */
    protected function mock_collection_factory(container_interface $container, array $mockfunctions): collection_factory_interface {
        $mock = $this->getMockBuilder(collection_factory::class)
            ->setConstructorArgs([$container])
            ->onlyMethods(array_values($mockfunctions))
            ->getMock();

        $container->method('get_collection_factory')->willReturn($mock);

        return $mock;
    }
}
