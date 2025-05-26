<?php
namespace enrol_oneroster;

defined('MOODLE_INTERNAL') || die();

class statusinfo {
    public $codeMajor;
    public $severity;
    public $codeMinor;
    public $description;

    public function __construct($data) {
        $this->codeMajor = $data['codeMajor'] ?? 'failure';
        $this->severity = $data['severity'] ?? 'error';
        $this->codeMinor = $data['codeMinor'] ?? '';
        $this->description = $data['description'] ?? '';
    }

    public function to_array() {
        return [
            'imsx_codeMajor' => $this->codeMajor,
            'imsx_severity' => $this->severity,
            'imsx_codeMinor' => ['code' => $this->codeMinor],
            'imsx_description' => ['text' => $this->description]
        ];
    }
}
