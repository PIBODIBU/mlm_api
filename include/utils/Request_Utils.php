<?php

/**
 * Verifying required params posted or not
 */
function verifyRequiredParams($required_fields)
{
    $error = false;
    $error_fields = "";
    $request_params = $_REQUEST;

    // Handling PUT request params
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        parse_str(file_get_contents('php://input'), $request_params);
    }

    foreach ($required_fields as $field) {
        // Array parser
        if (is_array($field)) {
            $passed = false;
            foreach ($field as $arrayField) {
                if (isset($request_params[$arrayField]) && strlen(trim($request_params[$arrayField])) > 0) {
                    $passed = true;
                    break;
                }
            }

            if (!$passed) {
                $error = true;
                foreach ($field as $arrayField) {
                    $error_fields .= $arrayField . '/';
                }
            }

            continue;
        }

        // Normal param parser
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }

    if ($error) {
        Flight::jsonError(true, 'Required field(s) ' . substr($error_fields, 0, substr($error_fields, -2) == ', ' ? -2 : -1) . ' is missing or empty');
    }
}

function addErrorStatusToArray($array = array(), $error = false, $error_msg = "", $error_code = NO_ERROR)
{
    if (is_array($array)) {
        $array['error'] = $error;
        $array['error_message'] = $error_msg;
        $array['error_code'] = $error_code;
    }

    return $array;
}