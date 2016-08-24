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
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = true;
            $error_fields .= $field . ', ';
        }
    }

    if ($error) {
        Flight::jsonError(true, 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty');
    }
}

function addErrorStatusToArray($array = array(), $error = false, $error_msg = "", $error_code = NO_ERROR)
{
    $array['error'] = $error;
    $array['error_message'] = $error_msg;
    $array['error_code'] = $error_code;

    return $array;
}