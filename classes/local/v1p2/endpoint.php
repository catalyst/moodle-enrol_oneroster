<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Endpoint class for OneRoster 1.2.
 *
 * @package    enrol_oneroster
 * @copyright  Khushi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster\local\v1p2;

use enrol_oneroster\local\v1p2\interfaces\container;

class endpoint {

    /** @var container */
    protected $container;

    /**
     * Constructor.
     *
     * @param container $container
     */
    public function __construct(container $container) {
        $this->container = $container;
    }

    /**
     * Returns the container.
     *
     * @return container
     */
    public function get_container(): container {
        return $this->container;
    }
}
