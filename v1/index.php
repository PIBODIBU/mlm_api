<?php

require_once '../libs/flight/Flight.php';

require_once '../include/security/DB_Security.php';
require_once '../include/utils/Request_Utils.php';

require_once '../include/db/handlers/UsersHandler.php';
require_once '../include/db/handlers/TimerHandler.php';
require_once '../include/db/handlers/BankInfoHandler.php';
require_once '../include/db/handlers/ShippingInfoHandler.php';

require_once '../include/security/UUID.php';
require_once '../include/security/APISecSec.php';

require_once '../include/model/ShippingInfo.php';
require_once '../include/model/BankInfo.php';

require_once '../include/utils/ErrorCodes.php';
require_once '../include/config/loc_config.php';

define('UPLOAD_DIRECTORY', dirname(__DIR__) . "/uploads/");

/**
 * METHODS MAPPING
 */

// Send error status & message via json
Flight::map('jsonError', function ($error, $message, $error_code = NO_ERROR) {
    Flight::json(
        $response = array(
            'error' => $error,
            'error_message' => $message,
            'error_code' => $error_code
        ));
});

Flight::route('/test/upload', function () {
    $response = array();
    $fileName = APISec::generate_file_name();
    $uploadFile = UPLOAD_DIRECTORY . $fileName;
    $fileExtension = pathinfo($_FILES['userfile']['name'], PATHINFO_EXTENSION);
    $uploadFile .= '.' . $fileExtension;

    if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadFile)) {
        $response['error'] = FALSE;
        $response['error_message'] = "File successfully uploaded.";
        $response['photo_url'] = UPLOADS_DIR_URL . $fileName .= '.' . $fileExtension;
    } else {
        $response['error'] = TRUE;
        $response['error_message'] = "Upload failed";
    }

    Flight::json($response);
});

/**
 * @api {post} /register Register
 * @apiDescription Register in the app.
 * @apiName PostRegister
 * @apiGroup Basic
 *
 * @apiParam (Main info) {String} name Name.
 * @apiParam (Main info) {String} surname Surname.
 * @apiParam (Main info) {String} email Email address.
 * @apiParam (Main info) {String} phone Phone number in international format.
 * @apiParam (Main info) {String} username Username.
 * @apiParam (Main info) {String} password Password.
 * @apiParam (Main info) {String} refer Username of the referrer.
 * @apiParam (Shipping info) {String} shipping_name Name.
 * @apiParam (Shipping info) {String} shipping_surname Surname.
 * @apiParam (Shipping info) {String} shipping_address Full address.
 * @apiParam (Shipping info) {String} shipping_city City.
 * @apiParam (Shipping info) {String} shipping_postal_code Postal code.
 * @apiParam (Shipping info) {String} shipping_country Country.
 * @apiParam (Shipping info) {String} shipping_phone Phone number in international format.
 * @apiParam (Bank info) {String} bank_name Name.
 * @apiParam (Bank info) {String} bank_surname Surname.
 * @apiParam (Bank info) {String} bank_iban IBAN.
 * @apiParam (Bank info) {String} bank_swift_code Swift code.
 * @apiParam (Bank info) {String} bank_paypal Paypal email.
 * @apiParam (Bank info) {String} bank_debit_card Number of debit card.
 * @apiParam (Bank info) {String} bank_personal_code Personal code.
 *
 * @apiSuccess {Object} main_info Main user's info.
 * @apiSuccess {Object} bank_info Bank info.
 * @apiSuccess {Object} shipping_info Shipping info.
 *
 * @apiError {Boolean} error Error status
 * @apiError {String} error_message Description of the error
 * @apiError {Number} error_code Identifier of the error
 */
Flight::route('POST /register', function () {
    verifyRequiredParams(array(
        // Main info
        'name',
        'surname',
        'email',
        'phone',
        'username',
        'password',
        'refer',

        // Shipping info
        'shipping_name',
        'shipping_surname',
        'shipping_address',
        'shipping_city',
        'shipping_postal_code',
        'shipping_country',
        'shipping_phone',

        // Bank info
        'bank_name',
        'bank_surname',
        'bank_iban',
        'bank_swift_code',
        'bank_paypal',
        'bank_debit_card',
        'bank_personal_code',
    ));

    $dbConnection = DbConnect::connect();

    $userHandler = new UsersHandler($dbConnection);
    $timerHandler = new TimerHandler($dbConnection);
    $shippingHandler = new ShippingInfoHandler($dbConnection);
    $bankInfoHandler = new BankInfoHandler($dbConnection);

    // Main info
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $refer = $_POST['refer'];

    // Shipping info
    $shippingName = $_POST['shipping_name'];
    $shippingSurname = $_POST['shipping_surname'];
    $shippingAddress = $_POST['shipping_address'];
    $shippingCity = $_POST['shipping_city'];
    $shippingPostalCode = $_POST['shipping_postal_code'];
    $shippingCountry = $_POST['shipping_country'];
    $shippingPhone = $_POST['shipping_phone'];

    // Bank info
    $bankName = $_POST['bank_name'];
    $bankSurname = $_POST['bank_surname'];
    $bankIban = $_POST['bank_iban'];
    $bankSwiftCode = $_POST['bank_swift_code'];
    $bankPaypal = $_POST['bank_paypal'];
    $bankDebitCard = $_POST['bank_debit_card'];
    $bankPersonalCode = $_POST['bank_personal_code'];

    // Additional info
    $uuid = UUID::generate_v4();
    $apiKey = APISec::generate_key();
    $clientSecret = APISec::generate_secret();
    $createdAt = date("Y-m-d H:i:s");
    $lastLogin = date("Y-m-d H:i:s");
    $isOnline = 1;

    // Basic checks
    if ($userHandler->isEmailOccupied($email)) {
        Flight::jsonError(TRUE, "Email address is already taken", ERROR_EMAIL_ALREADY_TAKEN);
    }
    if ($userHandler->isUsernameOccupied($username)) {
        Flight::jsonError(TRUE, "Username is already taken", ERROR_USERNAME_ALREADY_TAKEN);
    }
    if ($bankInfoHandler->isIBANOccupied($bankIban)) {
        Flight::jsonError(TRUE, "IBAN is already taken", ERROR_IBAN_ALREADY_TAKEN);
    }
    if ($bankInfoHandler->isSwiftCodeOccupied($bankSwiftCode)) {
        Flight::jsonError(TRUE, "Swift code is already taken", ERROR_SWIFT_CODE_ALREADY_TAKEN);
    }
    if ($bankInfoHandler->isDebitCardOccupied($bankDebitCard)) {
        Flight::jsonError(TRUE, "Debit card is already taken", ERROR_DEBIT_CARD_CODE_ALREADY_TAKEN);
    }
    if ($bankInfoHandler->isPersonalCodeOccupied($bankPersonalCode)) {
        Flight::jsonError(TRUE, "Personal code is already taken", ERROR_PERSONAL_CODE_CODE_ALREADY_TAKEN);
    }

    $user = new User(
        $uuid,
        $apiKey,
        $clientSecret,
        $name,
        $surname,
        $email,
        $phone,
        $username,
        password_hash($password, PASSWORD_BCRYPT, ['cost' => PASSWORD_ENCRYPTION_COST]),
        $refer,
        $createdAt,
        $lastLogin,
        $isOnline
    );

    $shippingInfo = new ShippingInfo(
        $uuid,
        $shippingName,
        $shippingSurname,
        $shippingAddress,
        $shippingCity,
        $shippingPostalCode,
        $shippingCountry,
        $shippingPhone
    );

    $bankInfo = new BankInfo(
        $uuid,
        $bankName,
        $bankSurname,
        $bankIban,
        $bankSwiftCode,
        $bankPaypal,
        $bankDebitCard,
        $bankPersonalCode
    );

    $timer = new Timer(
        $createdAt,
        $uuid
    );

    if (!$userHandler->addItem($user) || !$timerHandler->addItem($timer) ||
        !$shippingHandler->addItem($shippingInfo) || !$bankInfoHandler->addItem($bankInfo)
    ) {
        Flight::jsonError(true, "Error occurred during registration");
    }

    Flight::json(addErrorStatusToArray(
        $userHandler->getUserByUUID($uuid, false, array('username', 'password')), false, ""));
});

/**
 * @api {post} /login Login
 * @apiDescription Get API key, client secret and UUID by username and password
 * @apiName PostLogin
 * @apiGroup Basic
 *
 * @apiParam {String} username User's username.
 * @apiParam {String} password User's password.
 *
 *
 * @apiSuccess {Object} main_info Main user's info.
 * @apiSuccess {Object} bank_info Bank info.
 * @apiSuccess {Object} shipping_info Shipping info.
 *
 * @apiError {Boolean} error Error status
 * @apiError {String} error_message Description of the error
 * @apiError {Number} error_code Identifier of the error
 */
Flight::route('POST /login', function () {
    verifyRequiredParams(array('username', 'password'));

    $dbConnection = DbConnect::connect();
    $dbSecurity = new DB_Security($dbConnection);
    $userHandler = new UsersHandler($dbConnection);
    $bankInfoHandler = new BankInfoHandler($dbConnection);
    $shippingInfoHandler = new ShippingInfoHandler($dbConnection);

    $username = $_POST['username'];
    $password = $_POST['password'];
    $user = $userHandler->getUserByUsername($username);

    if ($user == NULL) {
        Flight::jsonError(true, "Bad username or password", ERROR_BAD_USERNAME_OR_PASSWORD);
    }

    if (!$dbSecurity->validatePassword($password, $user['password'])) {
        Flight::jsonError(true, "Bad username or password", ERROR_BAD_USERNAME_OR_PASSWORD);
    }

    if (!$userHandler->update(
        array('is_online', 'last_login'),
        array(1, date("Y-m-d H:i:s")),
        array('uuid', $user['uuid'])
    )
    ) {
        Flight::json(addErrorStatusToArray(array(), true, "Error occurred. Please, try again later.", ERROR_INTERNAL_SERVER));
    }

    Flight::json(
        array(
            'main_info' => $userHandler->getUserByUUID($user['uuid'], false, array('username', 'password')),
            'bank_info' => $bankInfoHandler->get(false, array('uuid'), new Filter('uuid', $user['uuid'])),
            'shipping_info' => $shippingInfoHandler->get(false, array('uuid'), new Filter('uuid', $user['uuid'])),
        )
    );
});

/**
 * @api {post} /restore/code Request code
 * @apiDescription Request code generation.
 * @apiName PostRequestCodeGen
 * @apiGroup Password restore
 *
 * @apiParam {String} email User's email for password restoring.
 *
 * @apiSuccess {Boolean} error Error status
 * @apiSuccess {String} error_message Description of the error
 * @apiSuccess {Number} error_code Identifier of the error
 *
 * @apiError {Boolean} error Error status
 * @apiError {String} error_message Description of the error
 * @apiError {Number} error_code Identifier of the error
 */
Flight::route('POST /restore/code', function () {
    verifyRequiredParams(array('email'));

    $connection = DbConnect::connect();
    $dbSecurity = new DB_Security($connection);
    $restoreCodeHandler = new RestoreCodeHandler($connection);

    $email = $_POST['email'];

    if (!$dbSecurity->validateEmail($email)) {
        Flight::jsonError(TRUE, 'Invalid email', ERROR_INVALID_EMAIL);
    }

    if ($restoreCodeHandler->isCodeAlreadyCreated($email)) {
        Flight::jsonError(TRUE, 'Code is already generated. Please, check your email.', ERROR_CODE_ALREADY_GENERATED);
    }

    if ($restoreCodeHandler->createRestoreCode($email, true)) {
        Flight::jsonError(false, "Code successfully generated. Please, check your email.");
    } else {
        Flight::jsonError(true, "Server error. Please, try again later.", ERROR_INTERNAL_SERVER);
    }
});

// TODO write docs
Flight::route('POST /restore/password', function () {
    verifyRequiredParams(array('email', 'restore_code', 'new_password'));

    $connection = DbConnect::connect();
    $dbSecurity = new DB_Security($connection);
    $usersHandler = new UsersHandler($connection);
    $restoreCodeHandler = new RestoreCodeHandler($connection);

    $email = $_POST['email'];
    $code = $_POST['restore_code'];
    $newPassword = $_POST['new_password'];

    if (!$dbSecurity->validateEmail($email)) {
        Flight::jsonError(TRUE, 'Invalid email', ERROR_INVALID_EMAIL);
    }

    if (!$restoreCodeHandler->isCodeValid($email, $code)) {
        Flight::jsonError(TRUE, 'Invalid restore code.', ERROR_BAD_RESTORE_CODE);
    }

    if (!$usersHandler->changePassword($email, $newPassword)) {
        Flight::jsonError(TRUE, 'Server error occurred. Please, try again later.', ERROR_INTERNAL_SERVER);
    }

    Flight::jsonError(FALSE, 'Password successfully changed.');
});

/**
 * @api {get} /users Get all users
 * @apiName GetUsers
 * @apiGroup User
 *
 * @apiParam {String} api_key User's API key.
 * @apiParam {Number{0-30}} limit Result limit.
 * @apiParam {Number} offset Result offset.
 * @apiParam {String} signature Signature of the request.
 *
 * @apiSuccess {Object[]} users List of user profiles.
 */
Flight::route('GET /users', function () {
    verifyRequiredParams(array('api_key', 'limit', 'offset', 'signature'));
    $userHandler = new UsersHandler(DbConnect::connect());
    $dbSecurity = new DB_Security($userHandler->getConnection());

    $limit = $_GET['limit'];
    $offset = $_GET['offset'];
    $apiKey = $_GET['api_key'];
    $signature = $_GET['signature'];

    if ($limit > 30) {
        Flight::jsonError(true, 'Too large limit', ERROR_TOO_LARGE_LIMIT);
    }

    if (!$dbSecurity->verifyUserApiKey($apiKey)) {
        Flight::jsonError(true, 'Bad api key', ERROR_BAD_API_KEY);
    }

    if (!$dbSecurity->validateSignature(array($limit, $offset), $signature, $apiKey)) {
        Flight::jsonError(true, 'Bad signature', ERROR_BAD_SIGNATURE);
    }

    Flight::json($userHandler->getAll($userHandler->getPrivateSchema(), $limit, $offset));
});

/**
 * Get user's timer
 */
Flight::route('GET /timer', function () {
    verifyRequiredParams(array('uuid'));

    $timerHandler = new TimerHandler(DbConnect::connect());
    $dbSecurity = new DB_Security($timerHandler->getConnection());

    $user_uuid = $_GET['uuid'];

    Flight::json($timerHandler->getTimerByUserId($user_uuid));
});

Flight::start();