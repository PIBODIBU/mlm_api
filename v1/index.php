<?php

require_once '../include/config.php';
require_once '../include/utils.php';
require '../libs/flight/Flight.php';

require_once '../include/security/UUID.php';
require_once '../include/security/API.php';
require_once '../include/security/RSA.php';
require_once '../include/security/KeyStore.php';

Flight::route('POST /login', function () {
    verifyRequiredParams(array('username', 'password'));

    $username = $_POST['username'];
    $password = $_POST['password'];

    $rsa = new RSA('');

    Flight::json(
        $response = array(
            'uuid' => UUID::generate_v4(),
            'api_key' => API::generate_key(),
            'client_secret' => API::generate_secret(),
        )
    );
});

Flight::route('/rsa/encrypt', function () {
    $data = $_GET['data'];
    $rsa = new RSA('');

    var_dump(base64_encode($rsa->encrypt($data)));
});

Flight::route('/rsa/generate', function () {
    $rsa = new RSA();

    $response = array(
        'error' => $rsa->generate_new_key_pair()
    );

    Flight::json($response);
});

Flight::route('/rsa/public', function () {
    $rsa = new RSA('');

    $response = array(
        'public_key' => $rsa->getPublicKey(),
    );

    Flight::json($response);
});

Flight::route('/test', function () {
    /*$param1 = $_GET['param1'];
    $param2 = $_GET['param2'];
    $param3 = $_GET['param3'];
    $sign = $_GET['sign'];

    $client_secret = "sfsef6se41f6se1s41df5s74fse";

    $response = array(
        'matched' => hash_equals(md5($param1 . $param2 . $param3 . $client_secret), $sign)
    );

    Flight::json($response);*/
    $username = "c2jZ/zI88sokFLfXsP+F2lHBEPHr0UEp5TkjcSwuh+p7LxVdOMkZUxI5CuJBf5xOviZrU/NFTNHxCJxkdkDTQRyBYAbvrlqZ+c158QsxSMR9qUONfwn2G37AC9BybFbcZd6PV2oLSSw0DT+A7SaXXOja+DbxozGqV4//cEdL23R3yVKUCVnl8I3jvRCXc8k4WTFjGlaI/d8m15YRZmzBeR56lyyBYA+MMAatQ+DiM/S25r8UoBh5e4GDbE+zGkYI9TzwF41g0TY0GVQd96dsPC7T10HuZrU/qNJq/2x4SKz0Xn3tOSlBFa7u2s+h4tKBy4NNrkHo+TPACI2eiSEfde44KYO+YlRMarod5XP/n93C83+e1HeP2Z4co20XiDXnkncka4TnDSTld6g2kEoBFmu2Ctc5ZZrlA3KBOq8G673RtqSA+Hq7GJNUQUcVKXljdvhhxw/JKIV09oiFdFANSCnj9rF7nlo+lTrsQhCJic1Vo0YU4VQLq2ebZi6dHeDu/JvH1fkxESqVQ+e2CWG7fwsD/3GdQ4ObmycLfYj5MGIt8on8kOHY7/KTiKWiUn0Z3Ja6/ilAfnoiJQ8K3pws7exDLiOVrusBSlTFz25MzOURVFuvq31nb0JvHcns+Ank7k97iWLSIp73vAOvA977yfgY/CK18ojkM2gTLuiJByM=";
    $rsa = new RSA();
    var_dump($rsa->decrypt(base64_decode($username)));
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
        // Required field(s) are missing or empty
        // echo error json and stop the app
        $response = array();
        $response["error"] = true;
        $response["error_message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        Flight::json($response, 400);
    }
}

// error generation
function error($message)
{
    $response = array();
    $response['error'] = true;
    $response['message'] = $message;
    Flight::json($response, 400);
}

Flight::start();