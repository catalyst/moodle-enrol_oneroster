<?php
//require_once(__DIR__ . '/../../../config.php'); 
//require_once(__DIR__ . '/../../../../../config.php');
//require_once(__DIR__ . '/../../../../../config.php');
require_once(__DIR__ . '/../../../../../config.php');

//require_once('/home/khush/capstone-docker/sites/moodle/config.php');


require_once($CFG->dirroot . '/enrol/oneroster/vendor/autoload.php');

use enrol_oneroster\local\imsx_status;

$dummyjson = <<<JSON
{
  "imsx_statusInfo": {
    "codeMajor": "failure",
    "severity": "error",
    "description": "Invalid selection field",
    "codeMinor": {
      "fieldName": "field",
      "fieldValue": "invalid_selection_field"
    }
  }
}
JSON;

try {
    $response = json_decode($dummyjson);
    $status = new imsx_status($response->imsx_statusInfo);

    echo "<pre>";
echo "Status Info Test:\n";
echo "Code Major: {$status->codeMajor->name}\n";
echo "Severity: {$status->severity->name}\n";
echo "Description: {$status->description}\n";
echo "Code Minor Field: {$status->codeMinor->fieldname}\n";
echo "Code Minor Value: {$status->codeMinor->codeMinorFieldValue->name}\n";
echo "</pre>";


} catch (Exception $e) {
    echo "Exception: " . $e->getMessage();
}
