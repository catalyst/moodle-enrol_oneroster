<?php
namespace enrol_oneroster\local;

use moodle_exception;

enum codeMajor {
    case success;
    case processing;
    case failure;
    case unsupported;
}

enum severity {
    case status;
    case warning;
    case error;
}

enum minorFieldValue {
    case fullsuccess;
    case invalid_filter_field;
    case invalid_selection_field;
    case invaliddata;
    case unauthorisedrequest;
    case forbidden;
    case server_busy;
    case unknownobject;
    case internal_server_error;
}

class codeMinor {
    public string $fieldname;
    public minorFieldValue $codeMinorFieldValue;

    public function __construct(object $http_codeminor) {
        $this->fieldname = $http_codeminor->fieldName ?? '';
        $this->codeMinorFieldValue = match ($http_codeminor->fieldValue ?? '') {
            'fullsuccess' => minorFieldValue::fullsuccess,
            'invalid_filter_field' => minorFieldValue::invalid_filter_field,
            'invalid_selection_field' => minorFieldValue::invalid_selection_field,
            'invaliddata' => minorFieldValue::invaliddata,
            'unauthorisedrequest' => minorFieldValue::unauthorisedrequest,
            'forbidden' => minorFieldValue::forbidden,
            'server_busy' => minorFieldValue::server_busy,
            'unknownobject' => minorFieldValue::unknownobject,
            'internal_server_error' => minorFieldValue::internal_server_error,
            default => throw new moodle_exception('invalid minor field value'),
        };
    }
}

class imsx_status {
    public codeMajor $codeMajor;
    public codeMinor $codeMinor;
    public string $description;
    public severity $severity;

    public function __construct(object $http) {
        if (!isset($http->codeMajor)) {
            throw new moodle_exception('no codeMajor');
        }
        if (!isset($http->codeMinor)) {
            throw new moodle_exception('no codeMinor');
        }
        if (!isset($http->description)) {
            throw new moodle_exception('no description');
        }
        if (!isset($http->severity)) {
            throw new moodle_exception('no severity');
        }

        $this->codeMajor = match ($http->codeMajor) {
            'success' => codeMajor::success,
            'processing' => codeMajor::processing,
            'failure' => codeMajor::failure,
            'unsupported' => codeMajor::unsupported,
            default => throw new moodle_exception('invalid codeMajor'),
        };

        $this->severity = match ($http->severity) {
            'status' => severity::status,
            'warning' => severity::warning,
            'error' => severity::error,
            default => throw new moodle_exception('invalid severity'),
        };

        $this->description = $http->description;
        $this->codeMinor = new codeMinor($http->codeMinor);
    }
}
