<?php
use enrol_oneroster\local\imsx\imsx_status;

defined('MOODLE_INTERNAL') || die();

class imsx_status_testcase extends advanced_testcase {

    public function test_imsx_status_constructs_successfully() {
        $input = (object)[
            'codeMajor' => 'success',
            'severity' => 'status',
            'description' => 'Test description',
            'codeMinor' => (object)[
                'fieldName' => 'field',
                'fieldValue' => 'invalid_selection_field',
            ],
        ];

        $status = new imsx_status($input);

        $this->assertEquals('success', $status->codeMajor->name);
        $this->assertEquals('status', $status->severity->name);
        $this->assertEquals('Test description', $status->description);
        $this->assertEquals('field', $status->codeMinor->fieldname);
        $this->assertEquals('invalid_selection_field', $status->codeMinor->codeMinorFieldValue->name);
    }
}
