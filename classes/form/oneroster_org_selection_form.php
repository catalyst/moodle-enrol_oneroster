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
namespace enrol_oneroster\form;

/**
 * One Roster Enrollment Client.
 *
 * @package    enrol_oneroster
 * @copyright  Gustavo Amorim De Almeida, Ruben Cooper, Josh Bateson, Brayden Porter
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class oneroster_org_selection_form extends \moodleform {
    /**
     * Creates a form with fields to select an organisation, a hidden field for the temp directory and a step field
     *
     * Also adds a submit button with validation rules
     *
     * @return void
     */
    public function definition() {
        $mform = $this->_form;

        $orgoptions = $this->_customdata['orgoptions'];
        $tempdir = $this->_customdata['tempdir'];

        $mform->addElement('select', 'organization', get_string('selectorganization', 'enrol_oneroster'), $orgoptions);
        $mform->setType('organization', PARAM_TEXT);
        $mform->addRule('organization', null, 'required', null, 'client');

        $mform->addElement('hidden', 'tempdir', $tempdir);
        $mform->setType('tempdir', PARAM_PATH);

        $mform->addElement('hidden', 'step', 2);
        $mform->setType('step', PARAM_INT);

        $this->add_action_buttons(true, get_string('submit'));
    }
}
