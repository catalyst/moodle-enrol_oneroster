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

namespace enrol_oneroster\classes\local\v1p2\entities;

use coding_exception;
use enrol_oneroster\local\endpoints\rostering as rostering_endpoint;
use enrol_oneroster\local\v1p2\entity;
use enrol_oneroster\local\interfaces\container as container_interface;
use stdClass;

/**
 * User profile entity. Currently not an official moodle object as there is no moodle representation.
 *
 * @package    enrol_oneroster
 * @copyright  QUT Capstone Team - Abhinav Gandham, Harrison Dyba, Jonathon Foo, Kushi Patel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class userprofile extends entity {
    /**
     * Get the operation ID for the endpoint.
     *
     * @param   container_interface $container
     * @return  string
     */
    protected static function get_operation_id(container_interface $container): string {
        return static::get_generic_operation_id($container);
    }

     /**
     * Get the operation ID for the endpoint which returns the generic representation of this type.
     *
     * For example a school is a subtype of the organisation object. You can fetch a school from the organisation
     * endpoint, but you cannot fetch an organisation from the school endpoint.
     *
     * @param   container_interface $container
     * @return  string
     */
    protected static function get_generic_operation_id(container_interface $container): string {
        // Use a string value since getUserProfile endpoint constant doesn't exist yet
        return 'getUserProfile';
    }

     /**
     * Parse the data returned from the One Roster Endpoint.
     *
     * @param   container_interface $container The container for this client
     * @param   stdClass $data The raw data returned from the endpoint
     * @return  stdClass The parsed data
     */
    protected static function parse_returned_row(container_interface $container, stdClass $data): stdClass {
        if (!property_exists($data, 'userProfile')) {
            throw new coding_exception("The returned data is missing the 'userProfile' property");
        }
        return $data->userProfile;
    }

    /**
     * Get the user that this profile belongs to.
     *
     * @return  user The owner user
     */
    public function get_user(): user {
        $userSourcedId = $this->get('userSourcedId');
        return $this->container->get_entity_factory()->fetch_user_by_id($userSourcedId);
    }

    public function get_userProfile_data(): stdClass {
        return (object) [
            'profileId' => $this->get('sourcedId'),
            'status' => $this->get('status'),
            'dateLastModified' => $this->get('dateLastModified'),
            'userSourcedId' => $this->get('userSourcedId'),
            'profileType' => $this->get('profileType'),
            'vendorId' => $this->get('vendorId'),
            'applicationId' => $this->get('applicationId'),
            'description' => $this->get('description'),
            'credentialType' => $this->get('credentialType'),
            'username' => $this->get('username'),
            'password' => $this->get('password')
        ];
    }

}
