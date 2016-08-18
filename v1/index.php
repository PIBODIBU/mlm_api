<?php

require_once '../include/security/DB_Security.php';
require_once '../libs/flight/Flight.php';

require_once '../include/security/UUID.php';
require_once '../include/security/API.php';

Flight::map('jsonError', function ($error, $message) {
    Flight::json(
        $response = array(
            'error' => $error,
            'error_message' => $message
        ), 400);
});

Flight::route('POST /login', function () {
    verifyRequiredParams(array('username', 'password'));

    $username = $_POST['username'];
    $password = $_POST['password'];

    Flight::json(
        $response = array(
            'uuid' => UUID::generate_v4(),
            'api_key' => API::generate_key(),
            'client_secret' => API::generate_secret(),
        )
    );
});

Flight::route('GET /users', function () {
    verifyRequiredParams(array('api_key'));

    $apiKey = $_GET['api_key'];

    $dbSecurity = new DB_Security();

    if (!$dbSecurity->verifyUserApiKey($apiKey)) {

    }
});

Flight::route('/test', function () {

});

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

Flight::start();