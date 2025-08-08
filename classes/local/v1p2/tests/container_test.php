<?php
namespace enrol_oneroster\local\v1p2;
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

//namespace enrol_oneroster\local;

defined('MOODLE_INTERNAL') || die;
require_once(__DIR__ . '/oneroster_testcase.php');
use enrol_oneroster\local\oneroster_testcase;

use enrol_oneroster\local\interfaces\client as client_interface;

/**
 * One Roster tests for container.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @covers  \enrol_oneroster\local\container
 */
use enrol_oneroster\local\v1p2\container;
use enrol_oneroster\local\v1p2\collections\classes_for_user;
use enrol_oneroster\local\v1p2\interfaces\collection;
use enrol_oneroster\local\v1p2\interfaces\factory;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/enrol/oneroster/tests/local/oneroster_testcase.php');

class container_test extends oneroster_testcase {

    public function test_get_classes_for_user_returns_collection_instance() {
        $this->resetAfterTest(true);

        // Create mock factory.
        $mockfactory = $this->getMockBuilder(factory::class)
                            ->disableOriginalConstructor()
                            ->getMock();

        // Create mock collection.
        $mockcollection = $this->createMock(classes_for_user::class);

        // Setup mock to return mockcollection when get_classes_for_user is called.
        $mockfactory->expects($this->once())
                    ->method('get_classes_for_user')
                    ->willReturn($mockcollection);

        // Inject mock factory into container.
        $container = new container($mockfactory);

        // Act
        $result = $container->get_classes_for_user();

        // Assert
        $this->assertInstanceOf(classes_for_user::class, $result);
    }
}
