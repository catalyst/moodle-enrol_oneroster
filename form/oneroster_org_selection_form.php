<?php
namespace enrol_oneroster\form;

require_once("$CFG->libdir/formslib.php");

class oneroster_org_selection_form extends \moodleform {
    public function definition() {
        $mform = $this->_form;

        $orgoptions = $this->_customdata['orgoptions'];
        $tempdir = $this->_customdata['tempdir'];

        $mform->addElement('select', 'organization', get_string('selectorganization', 'enrol_oneroster'), $orgoptions);
        $mform->setType('organization', PARAM_RAW);
        $mform->addRule('organization', null, 'required', null, 'client');

        $mform->addElement('hidden', 'tempdir', $tempdir);
        $mform->setType('tempdir', PARAM_RAW);

        $mform->addElement('hidden', 'step', 2);
        $mform->setType('step', PARAM_INT);

        $this->add_action_buttons(true, get_string('submit'));
    }
}
