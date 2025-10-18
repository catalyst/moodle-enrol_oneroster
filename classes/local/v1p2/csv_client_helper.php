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
namespace enrol_oneroster\local\v1p2;

require_once(__DIR__ . '/../v1p1/csv_client_helper.php');
require_once(__DIR__ . '/csv_client_const_helper.php');
use enrol_oneroster\local\v1p1\csv_client_helper as csv_client_helper_version_one;

/**
 * Class csv_client_helper
 *
 * Helper class for OneRoster v1p2 plugin
 *
 * @package    enrol_oneroster
 * @copyright  QUT Capstone Team - Abhinav Gandham, Harrison Dyba, Jonathon Foo, Kushi Patel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class csv_client_helper extends csv_client_helper_version_one {

    /**
     * Function to get the header for a given file.
     *
     * @param string $filename The name of the file.
     * @return array The header for the given file.
     */
    public static function get_header(string $filename): array {
        switch ($filename) {
            case csv_client_const_helper::FILE_USERPROFILES:
                return csv_client_const_helper::HEADER_USERPROFILES;
            case csv_client_const_helper::FILE_ROLES:
                return csv_client_const_helper::HEADER_ROLES;
            default:
                return parent::get_header($filename);
        }
    }
}
