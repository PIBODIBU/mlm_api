<?php

require_once '../libs/flight/Flight.php';

require_once '../include/security/DB_Security.php';
require_once '../include/utils/Request_Utils.php';

require_once '../include/db/handlers/UsersHandler.php';
require_once '../include/security/UUID.php';
require_once '../include/security/API.php';

/**
 * METHOD MAPPING
 */

// Send error status & message via json
Flight::map('jsonError', function ($error, $message) {
    Flight::json(
        $response = array(
            'error' => $error,
            'error_message' => $message
        ), 400);
});

Flight::route('POST /register', function () {
    verifyRequiredParams(array('name', 'surname', 'username', 'password'));
    $userHandler = new UsersHandler(DbConnect::connect());

    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    $uuid = UUID::generate_v4();
    $apiKey = API::generate_key();
    $clientSecret = API::generate_secret();

    if (!$userHandler->addItem(new User(
        $uuid,
        $apiKey,
        $clientSecret,
        $name,
        $surname,
        $username,
        md5($password)
    ))
    ) {
        Flight::jsonError(true, "Error occurred during registration");
    }

    Flight::json(addErrorStatusToArray(
        $userHandler->getUserByUUID($uuid, false, array('username', 'password')), false, ""));
});

Flight::route('POST /login', function () {
    verifyRequiredParams(array('username', 'password'));
    $dbSecurity = new DB_Security();
    $userHandler = new UsersHandler(DbConnect::connect());

    $username = $_POST['username'];
    $password = $_POST['password'];
    $user = $userHandler->getUserByUsername($username);

    if ($user == NULL) {
        Flight::jsonError(true, "Bad username or password");
    }

    if (!$dbSecurity->validatePassword($password, $user['password'])) {
        Flight::jsonError(true, "Bad username or password");
    }

    Flight::json(addErrorStatusToArray(
        $userHandler->getUserByUUID($user['uuid'], false, array('username', 'password')), false, ""));
});

/**
 * Get all users
 *
 * Required params:
 *                  limit(int),
 *                  offset(int)
 */
Flight::route('GET /users', function () {
    verifyRequiredParams(array('api_key', 'limit', 'offset', 'signature'));
    $userHandler = new UsersHandler(DbConnect::connect());
    $dbSecurity = new DB_Security($userHandler->getConnection());

    $limit = $_GET['limit'];
    $offset = $_GET['offset'];
    $apiKey = $_GET['api_key'];
    $signature = $_GET['signature'];

    if (!$dbSecurity->verifyUserApiKey($apiKey)) {
        Flight::jsonError(true, 'Bad api key');
    }

    if (!$dbSecurity->validateSignature(array($limit, $offset), $signature, $apiKey)) {
        Flight::jsonError(true, 'Bad signature');
    }

    Flight::json($userHandler->getAll(array('username', 'password', 'api_key', 'client_secret', 'uuid')));
});

Flight::route('/test', function () {

});

Flight::start();