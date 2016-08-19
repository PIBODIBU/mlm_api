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
    verifyRequiredParams(array(
        'name',
        'surname',
        'email',
        'phone',
        'username',
        'password',
        'refer'
    ));
    $userHandler = new UsersHandler(DbConnect::connect());

    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $refer = $_POST['refer'];
    $createdAt = date("Y-m-d H:i:s");
    $lastLogin = date("Y-m-d H:i:s");
    $isOnline = 1;

    $uuid = UUID::generate_v4();
    $apiKey = API::generate_key();
    $clientSecret = API::generate_secret();

    if (!$userHandler->addItem(new User(
        $uuid,
        $apiKey,
        $clientSecret,
        $name,
        $surname,
        $email,
        $phone,
        $username,
        md5($password),
        $refer,
        $createdAt,
        $lastLogin,
        $isOnline
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

    $userHandler->update(
        array('uuid', 'is_online', 'last_login'),
        array($user['uuid'], 1, date("Y-m-d H:i:s"))
    );

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

    Flight::json($userHandler->getAll($userHandler->getPrivateSchema()));
});

Flight::route('/test', function () {

});

Flight::start();