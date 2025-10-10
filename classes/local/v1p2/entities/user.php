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

namespace enrol_oneroster\local\v1p2\entities;

use coding_exception;
use enrol_oneroster\local\endpoints\rostering as rostering_endpoint;
use enrol_oneroster\local\v1p2\entity;
use enrol_oneroster\local\interfaces\container as container_interface;
use enrol_oneroster\local\interfaces\user_representation;
use enrol_oneroster\local\entities\org;
use PhpParser\Node\Stmt\Else_;
use stdClass;

/**
 * User profile entity. Currently not an official moodle object as there is no moodle representation.
 *
 * @package    enrol_oneroster
 * @copyright  QUT Capstone Team - Abhin
 */
class user extends entity implements user_representation {
    /**
     * Get the operation ID for the endpoint, otherwise known as the name of the endpoint.
     *
     * @copyright  Andrew Nicols <andrew@nicols.co.uk>
     * @param   container_interface $container
     * @return  string
     */
    protected static function get_operation_id(container_interface $container): string {
        return static::get_generic_operation_id($container);
    }

    /**
     * Get the operation ID for the endpoint which returns the generic representation of this type.
     *
     * For example a school is a subtype of the organisation object. You can fetch a school from the organisatino
     * endpoint, but you cannot fetch an organisation from the school endpoint.
     *
     * @copyright  Andrew Nicols <andrew@nicols.co.uk>
     * @param   container_interface $container
     * @return  string
     */
    protected static function get_generic_operation_id(container_interface $container): string {
        return rostering_endpoint::getUser;
    }

    /**
     * Parse the data returned from the One Roster Endpoint.
     * 
     * @copyright  Andrew Nicols <andrew@nicols.co.uk>
     * @param   container_interface $container The container for this client
     * @param   stdClass $data The raw data returned from the endpoint
     * @return  stdClass The parsed data
     */
    protected static function parse_returned_row(container_interface $container, stdClass $data): stdClass {
        if (!property_exists($data, 'user')) {
            throw new coding_exception("The returned data is missing the 'user' property");
        }
        return $data->user;
    }

    /**
     * Get the data which represents this One Roster Object as a Moodle User.
     * 
     * @copyright  Andrew Nicols <andrew@nicols.co.uk>
     * @return  stdClass
     */
    public function get_user_data(): stdClass {
        return (object) [
            'idnumber' => $this->get('sourcedId'),
            'username' => strtolower($this->get('identifier')),
            'email' => $this->get('email'),
            'password' => $this->get('password') ?? '',
            'firstname' => $this->get('givenName'),
            'lastname' => $this->get('familyName'),
        ];
    }

    /**
     * Get the user that this class belongs to.
     *
     * @return  org The owner organisation
     */
    public function get_orgs(): org {
        // Fetch the user details.
        $roles = $this->data->get('roles');
        $roleids = [];

        foreach ($roles as $role) {
            $orgid = $role->orgSourcedId;
            if (!empty($orgid)) {
                $orgids[] = $orgid;
            }
        }
        $foundids = array_unique($orgids);
        return $this->container->get_collection_factory()->get_orgs($foundids);
    }
}