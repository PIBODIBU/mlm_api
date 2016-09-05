<?php

require_once '../libs/flight/Flight.php';

require_once '../include/security/DB_Security.php';
require_once '../include/utils/Request_Utils.php';

require_once '../include/db/handlers/UsersHandler.php';
require_once '../include/db/handlers/TimerHandler.php';
require_once '../include/db/handlers/BankInfoHandler.php';
require_once '../include/db/handlers/ShippingInfoHandler.php';
require_once '../include/db/handlers/MessagesHandler.php';
require_once '../include/db/handlers/DialogsHandler.php';

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
 * @apiParam (Main info) {Object} Multipart file - user's photo.
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

    // User photo
    $userHandler->uploadAvatar($_FILES['photo'], $user);

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

/**
 * @api {post} /password/change Change password
 * @apiDescription Change password with restore code.
 * @apiName PostRestorePassword
 * @apiGroup Password restore
 *
 * @apiParam {String} email User's email for password restoring.
 * @apiParam {String} restore_code Restore code for password changing.
 *
 * @apiSuccess {Boolean} error Error status
 * @apiSuccess {String} error_message Description of the error
 * @apiSuccess {Number} error_code Identifier of the error
 *
 * @apiError {Boolean} error Error status
 * @apiError {String} error_message Description of the error
 * @apiError {Number} error_code Identifier of the error
 */
Flight::route('POST /password/change', function () {
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
 * @api {get} /dialogs          Get dialogs
 * @apiDescription Get user's list of dialogs.
 * @apiName GetDialog
 * @apiGroup Dialogs
 *
 * @apiParam {String} api_key       User's API key.
 * @apiParam {Number} limit         Result limit.
 * @apiParam {Number} offset        Result offset.
 * @apiParam {String} signature     MD5 signature - limit, offset, secret.
 *
 * @apiSuccess {Object[]} dialogs       Dialogs.
 *
 * @apiError {Boolean} error            Error status
 * @apiError {String} error_message     Description of the error
 * @apiError {Number} error_code        Identifier of the error
 */
Flight::route('GET /dialogs', function () {
    verifyRequiredParams(array('api_key', 'limit', 'offset', 'signature'));

    $connection = DbConnect::connect();
    $userHandler = new UsersHandler($connection);
    $dialogsHandler = new DialogsHandler($connection);
    $dbSecurity = new DB_Security($connection);

    $limit = $_GET['limit'];
    $offset = $_GET['offset'];
    $apiKey = $_GET['api_key'];
    $signature = $_GET['signature'];

    if (!$dbSecurity->verifyUserApiKey($apiKey)) {
        Flight::jsonError(true, 'Bad api key', ERROR_BAD_API_KEY);
    }

    if (!$dbSecurity->validateSignature(array($limit, $offset), $signature, $apiKey)) {
        Flight::jsonError(true, 'Bad signature', ERROR_BAD_SIGNATURE);
    }

    // Get user
    $user = $userHandler->get(true, array(), new Filter('api_key', $apiKey));

    if ($user === NULL) {
        Flight::jsonError(TRUE, 'User not found', ERROR_USER_NOT_FOUND);
    }

    // Get dialogs list
    $dialogs = $dialogsHandler->getDialogs($user->getUUID());

    Flight::json($dialogs);
});

/**
 * @api {get} /dialogs/:id      Get dialog by id
 * @apiDescription Get one dialog by its id.
 * @apiName GetDialogById
 * @apiGroup Dialogs
 *
 * @apiParam {String} api_key       User's API key.
 * @apiParam {String} signature     MD5 signature - id (path), secret.
 *
 * @apiSuccess {Object} dialog      Dialog.
 *
 * @apiError {Boolean} error            Error status
 * @apiError {String} error_message     Description of the error
 * @apiError {Number} error_code        Identifier of the error
 */
Flight::route('GET /dialogs/@id:[0-9]+', function ($dialogId) {
    verifyRequiredParams(array('api_key', 'signature'));

    $connection = DbConnect::connect();
    $dialogsHandler = new DialogsHandler($connection);
    $dbSecurity = new DB_Security($connection);

    $apiKey = $_GET['api_key'];

    if (!$dbSecurity->verifyUserApiKey($apiKey)) {
        Flight::jsonError(true, 'Bad api key', ERROR_BAD_API_KEY);
    }

    if (!$dialogsHandler->isDialogExists($dialogId)) {
        Flight::jsonError(true, 'Dialog does not exists', ERROR_DIALOG_NOT_EXISTS);
    }

    if (!$dbSecurity->isMyDialog($apiKey, $dialogId)) {
        Flight::jsonError(true, 'It is not your dialog', ERROR_NOT_YOUR_DIALOG);
    }

    Flight::json($dialogsHandler->getDialog($dialogId));
});

/**
 * @api {get} /dialogs/create      Create dialog
 * @apiDescription Create new dialog with specified user.
 * @apiName GetCreateDialog
 * @apiGroup Dialogs
 *
 * @apiParam {String} api_key       User's API key.
 * @apiParam {String} peer_uuid     Peer UUID.
 * @apiParam {String} signature     MD5 signature - peer_uuid, secret.
 *
 * @apiSuccess {Object} dialog      Dialog.
 *
 * @apiError {Boolean} error            Error status
 * @apiError {String} error_message     Description of the error
 * @apiError {Number} error_code        Identifier of the error
 */
Flight::route('GET /dialogs/create', function () {
    verifyRequiredParams(array('api_key', 'peer_uuid', 'signature'));

    $connection = DbConnect::connect();
    $dialogsHandler = new DialogsHandler($connection);
    $usersHandler = new UsersHandler($connection);
    $dbSecurity = new DB_Security($connection);

    $apiKey = $_GET['api_key'];
    $peerUUID = $_GET['peer_uuid'];
    $signature = $_GET['signature'];

    if (!$dbSecurity->verifyUserApiKey($apiKey)) {
        Flight::jsonError(true, 'Bad api key', ERROR_BAD_API_KEY);
    }

    if (!$dbSecurity->validateSignature(array($peerUUID), $signature, $apiKey)) {
        Flight::jsonError(true, 'Bad signature', ERROR_BAD_SIGNATURE);
    }

    // Get user
    $user = $usersHandler->get(true, array(), new Filter('api_key', $apiKey));

    if ($user === NULL) {
        Flight::jsonError(TRUE, 'User not found', ERROR_USER_NOT_FOUND);
    }

    // Check if dialog already exists
    $dialog = $dialogsHandler->isDialogAlreadyCreated($user->getUUID(), $peerUUID);
    if ($dialog === false) {
        // Dialog doesn't exist. Let's create new one
        $dialog = $dialogsHandler->createDialog($user->getUUID(), $peerUUID);

        if (!$dialog) {
            echo $dialog;
            //Flight::jsonError(true, "Internal server occurred. Please, try again later", ERROR_INTERNAL_SERVER);
        } else {
            echo $dialog;
            //Flight::json(addErrorStatusToArray($dialogsHandler->getDialog($dialog)));
        }
    }

    Flight::json(addErrorStatusToArray($dialog));
});

/**
 * @api {get} /messages/:dialog_id          Get dialog messages
 * @apiDescription Get list of dialog messages.
 * @apiName GetMessagesOfDialog
 * @apiGroup Messages
 *
 * @apiParam {String} api_key       User's API key.
 * @apiParam {Number} limit         Result limit.
 * @apiParam {Number} offset        Result offset.
 * @apiParam {String} signature     MD5 signature - limit, offset, secret.
 *
 * @apiSuccess {Object[]} messages      Messages.
 *
 * @apiError {Boolean} error            Error status
 * @apiError {String} error_message     Description of the error
 * @apiError {Number} error_code        Identifier of the error
 */
Flight::route('GET /messages/@dialog_id:[0-9]+', function ($dialogId) {
    verifyRequiredParams(array('api_key', 'limit', 'offset', 'signature'));

    $connection = DbConnect::connect();
    $messagesHandler = new MessagesHandler($connection);
    $dialogsHandler = new DialogsHandler($connection);
    $dbSecurity = new DB_Security($connection);

    $limit = $_GET['limit'];
    $offset = $_GET['offset'];
    $apiKey = $_GET['api_key'];
    $signature = $_GET['signature'];

    if (!$dbSecurity->verifyUserApiKey($apiKey)) {
        Flight::jsonError(true, 'Bad api key', ERROR_BAD_API_KEY);
    }

    if (!$dbSecurity->validateSignature(array($limit, $offset), $signature, $apiKey)) {
        Flight::jsonError(true, 'Bad signature', ERROR_BAD_SIGNATURE);
    }

    if (!$dialogsHandler->isDialogExists($dialogId)) {
        Flight::jsonError(true, 'Dialog does not exists', ERROR_DIALOG_NOT_EXISTS);
    }

    if (!$dbSecurity->isMyDialog($apiKey, $dialogId)) {
        Flight::jsonError(true, 'It is not your dialog', ERROR_NOT_YOUR_DIALOG);
    }

    Flight::json($messagesHandler->getAll(array(), $limit, $offset, new Filter('dialog_id', $dialogId)));
});

/**
 * @api {post} /messages/send          Send message
 * @apiDescription Send message.
 * @apiName PostSendMessage
 * @apiGroup Messages
 *
 * @apiParam {String} api_key           User's API key.
 * @apiParam (Switchable parameters (one must be filled))    {String} dialog_id         Dialog to send message.
 * @apiParam (Switchable parameters (one must be filled))    {String} recipient_uuid    User to send message.
 * @apiParam {String} message           Message body.
 * @apiParam {String} signature         MD5 signature - dialog_id/recipient_uuid, secret.
 *
 * @apiSuccess {Boolean} error            Error status
 * @apiSuccess {String} error_message     Description of the error
 * @apiSuccess {Number} error_code        Identifier of the error
 *
 * @apiError {Boolean} error            Error status
 * @apiError {String} error_message     Description of the error
 * @apiError {Number} error_code        Identifier of the error
 */
Flight::route('POST /messages/send', function () {
    verifyRequiredParams(array('api_key', 'signature', array('opt1', 'opt2')));

    $connection = DbConnect::connect();
    $messagesHandler = new MessagesHandler($connection);
    $dialogsHandler = new DialogsHandler($connection);
    $dbSecurity = new DB_Security($connection);

    $dialogId = $_POST['dialog_id'];
});

/**
 * @api {post} /messages/:message_id/read    Mark as read
 * @apiDescription Mark message as read.
 * @apiName PostMarkMessageAsRead
 * @apiGroup Messages
 *
 * @apiParam {String} api_key       User's API key.
 * @apiParam {String} signature     MD5 signature - id (path), secret.
 *
 * @apiSuccess {Boolean} error            Error status
 * @apiSuccess {String} error_message     Description of the error
 * @apiSuccess {Number} error_code        Identifier of the error
 *
 * @apiError {Boolean} error            Error status
 * @apiError {String} error_message     Description of the error
 * @apiError {Number} error_code        Identifier of the error
 */
Flight::route('POST /messages/@id:[0-9]+/read', function ($messageId) {
    verifyRequiredParams(array('api_key', 'signature'));

    $connection = DbConnect::connect();
    $messagesHandler = new MessagesHandler($connection);
    $dialogsHandler = new DialogsHandler($connection);
    $dbSecurity = new DB_Security($connection);

    $apiKey = $_POST['api_key'];
    $signature = $_POST['signature'];

    if (!$dbSecurity->verifyUserApiKey($apiKey)) {
        Flight::jsonError(true, 'Bad api key', ERROR_BAD_API_KEY);
    }

    if (!$dbSecurity->validateSignature(array($messageId), $signature, $apiKey)) {
        Flight::jsonError(true, 'Bad signature', ERROR_BAD_SIGNATURE);
    }

    $dialogId = $messagesHandler->getDialogIdFromMessage($messageId);

    if (!$dialogsHandler->isMyDialog($apiKey, $dialogId)) {
        Flight::jsonError(true, 'It is not your dialog', ERROR_NOT_YOUR_DIALOG);
    }

    if (!$dialogsHandler->amIRecipient($apiKey, $messageId)) {
        Flight::jsonError(true, 'You are not recipient of this message', ERROR_NOT_RECIPIENT);
    }

    if (!$messagesHandler->markAsRead($messageId)) {
        Flight::jsonError(true, 'Server error occurred', ERROR_INTERNAL_SERVER);
    }

    Flight::jsonError(false, 'Message was marked as read', NO_ERROR);
});

/**
 * @api {post} /messages/:message_id/important       Mark as important
 * @apiDescription Mark message as important or common.
 * @apiName PostMarkMessageAsImportant
 * @apiGroup Messages
 *
 * @apiParam {String} api_key           User's API key.
 * @apiParam {Boolean} important        0 - mark as common, 1 - mark as important.
 * @apiParam {String} signature         MD5 signature - message_id (path), important ,secret.
 *
 * @apiSuccess {Boolean} error            Error status
 * @apiSuccess {String} error_message     Description of the error
 * @apiSuccess {Number} error_code        Identifier of the error
 *
 * @apiError {Boolean} error            Error status
 * @apiError {String} error_message     Description of the error
 * @apiError {Number} error_code        Identifier of the error
 */
Flight::route('POST /messages/@id:[0-9]+/important', function ($messageId) {
    verifyRequiredParams(array('api_key', 'important', 'signature'));

    $connection = DbConnect::connect();
    $messagesHandler = new MessagesHandler($connection);
    $dialogsHandler = new DialogsHandler($connection);
    $dbSecurity = new DB_Security($connection);

    $apiKey = $_POST['api_key'];
    $important = $_POST['important'];
    $signature = $_POST['signature'];

    if (!$dbSecurity->verifyUserApiKey($apiKey)) {
        Flight::jsonError(true, 'Bad api key', ERROR_BAD_API_KEY);
    }

    if (!$dbSecurity->validateSignature(array($messageId, $important), $signature, $apiKey)) {
        Flight::jsonError(true, 'Bad signature', ERROR_BAD_SIGNATURE);
    }

    $dialogId = $messagesHandler->getDialogIdFromMessage($messageId);

    if (!$dialogsHandler->isMyDialog($apiKey, $dialogId)) {
        Flight::jsonError(true, 'It is not your dialog', ERROR_NOT_YOUR_DIALOG);
    }

    if (!$messagesHandler->markImportant($messageId, $important)) {
        Flight::jsonError(true, 'Server error occurred', ERROR_INTERNAL_SERVER);
    }

    Flight::jsonError(false, 'Message was marked', NO_ERROR);
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