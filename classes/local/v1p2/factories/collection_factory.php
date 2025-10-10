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
 * One Roster Enrolment Client.
 *
 * @package    enrol_oneroster
 * @copyright  QUT Capstone Team - Abhinav Gandham, Harrison Dyba, Jonathon Foo, Kushi Patel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster\local\v1p2\factories;

use enrol_oneroster\local\v1p1\factories\collection_factory as collection_factory_version_one;
use enrol_oneroster\local\collections\users as users_collection;
use enrol_oneroster\local\v1p2\collections\users as versioned_users_collection;
use enrol_oneroster\local\filter;

/**
 * One Roster v1p2 Collection Factory.
 *
 * @package    enrol_oneroster
 * @copyright  QUT Capstone Team - Abhinav Gandham, Harrison Dyba, Jonathon Foo, Kushi Patel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class collection_factory extends collection_factory_version_one{
    /**
     * Fetch a collection of users.
     *
     * @param   array $params The parameters to use when fetching the collection
     * @param   filter $filter The filter to use when fetching the collection
     * @param   callable $recordfilter Any subsequent filter to apply to the results
     * @return  users_collection
     */
    public function get_users(array $params = [], ?filter $filter = null, ?callable $recordfilter = null): users_collection {
        return new versioned_users_collection($this->container, $params, $filter, $recordfilter);
    }
}
