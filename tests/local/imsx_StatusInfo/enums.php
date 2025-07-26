<?php

namespace enrol_oneroster\local;

use moodle_exception;

enum codeMajor: string {
    case success = 'success';
    case processing = 'processing';
    case failure = 'failure';
    case unsupported = 'unsupported';
}

enum severity: string {
    case status = 'status';
    case warning = 'warning';
    case error = 'error';
}

enum minorFieldValue: string {
    case fullsuccess = 'fullsuccess';
    case invalid_filter_field = 'invalid_filter_field';
    case invalid_selection_field = 'invalid_selection_field';
    case invaliddata = 'invaliddata';
    case unauthorisedrequest = 'unauthorisedrequest';
    case forbidden = 'forbidden';
    case server_busy = 'server_busy';
    case unknownobject = 'unknownobject';
    case internal_server_error = 'internal_server_error';
}

class codeMinor {
    public string $fieldname;
    public minorFieldValue $codeMinorFieldValue;

    public function __construct(object $http_codeminor) {
        if (!isset($http_codeminor->codeMinorFieldName)) {
            throw new moodle_exception('Missing codeMinorFieldName');
        }
        if (!isset($http_codeminor->codeMinorFieldValue)) {
            throw new moodle_exception('Missing codeMinorFieldValue');
        }

        $this->fieldname = $http_codeminor->codeMinorFieldName;

        try {
            $this->codeMinorFieldValue = minorFieldValue::from($http_codeminor->codeMinorFieldValue);
        } catch (\ValueError $e) {
            throw new moodle_exception('Invalid codeMinorFieldValue: ' . $http_codeminor->codeMinorFieldValue);
        }
    }
}

class imsx_status {
    public codeMajor $codeMajor;
    public codeMinor $codeMinor;
    public string $description;
    public severity $severity;

    public function __construct(object $http) {
        if (!isset($http->codeMajor)) {
            throw new moodle_exception('Missing codeMajor');
        }
        if (!isset($http->codeMinor)) {
            throw new moodle_exception('Missing codeMinor');
        }
        if (!isset($http->description)) {
            throw new moodle_exception('Missing description');
        }
        if (!isset($http->severity)) {
            throw new moodle_exception('Missing severity');
        }

        try {
            $this->codeMajor = codeMajor::from($http->codeMajor);
        } catch (\ValueError $e) {
            throw new moodle_exception('Invalid codeMajor value: ' . $http->codeMajor);
        }

        try {
            $this->severity = severity::from($http->severity);
        } catch (\ValueError $e) {
            throw new moodle_exception('Invalid severity value: ' . $http->severity);
        }

        $this->description = $http->description;
        $this->codeMinor = new codeMinor($http->codeMinor);
    }
}
