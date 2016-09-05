<?php

/**
 * Verifying required params posted or not
 */
function verifyRequiredParams($requiredFields)
{
    $error = false;
    $errorFields = "";
    $requestParams = $_REQUEST;

    // Handling PUT request params
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        parse_str(file_get_contents('php://input'), $requestParams);
    }

    foreach ($requiredFields as $field) {
        // Array parser
        if (is_array($field)) {
            $passed = false;
            foreach ($field as $arrayField) {
                if (isset($requestParams[$arrayField]) && strlen(trim($requestParams[$arrayField])) > 0) {
                    $passed = true;
                    break;
                }
            }

            if (!$passed) {
                $error = true;
                foreach ($field as $arrayField) {
                    $errorFields .= $arrayField . '/';
                }
            }

            continue;
        }

        // Normal param parser
        if (!isset($requestParams[$field]) || strlen(trim($requestParams[$field])) <= 0) {
            $error = true;
            $errorFields .= $field . ', ';
        }
    }

    if ($error) {
        Flight::jsonError(true, 'Required field(s) ' . substr($errorFields, 0, substr($errorFields, -2) == ', ' ? -2 : -1) . ' is missing or empty');
    }
}

function addErrorStatusToArray($array = array(), $error = false, $error_msg = "", $error_code = NO_ERROR)
{
    if (is_array($array)) {
        $array['error']['error'] = $error;
        $array['error']['error_message'] = $error_msg;
        $array['error']['error_code'] = $error_code;
    }

    return $array;
}