<?php

require_once '../include/config.php';
require_once '../include/utils.php';
require '../libs/flight/Flight.php';

require_once '../include/security/UUID.php';
require_once '../include/security/API.php';
require_once '../include/security/RSA.php';
require_once '../include/security/KeyStore.php';

Flight::route('/login', function () {
    // Fake input
    $username = "QfeysZ5hauF+r2af1zCN9DbiTpLGjuKBwvPZJB1MBQNs1WwnqIzaAz75CxAZVNNl9gsDosrMmukX6jkHlZWBQ0jpVpGKGZ/MfaGdoZzR6N19oqTGpida2ngygnqUjPXtEdeqCOaDtnWQLo/GLlNxwAURuAB+isG8eV6/GVmVASZWlgxk0U6ZTkCwxIrO59j0SpfFuhBojphQNaJ1o1Wb33Vmdr/bzVH8yYYKZSArdVl7QnOyfgyqACcNhw8CdcuNaxdYfnr69Zz1kq0wIqeNtt9cr0NYl16FIGUcM4+mcj0WtAd8SBvMX8YOCWZibyPlNI9vIJCU9vO2NSi8TCGWlOwGAraUlm1aldkOtFrBpwB9EISOTFW4jmwEAr/G16lwlI3j4PRlNWV2kOTn5FdYw4+248HFPYTqy0F2eM4fHEKDIJWCS1EJPNUEiossgXUgTakY0OwMeV/AODva1BHj80KyksMflI5DKZ9t48hDhVn58BX6J6W75CAh/OkBM4LdBACbbzjFKXsojTDls3aT5XH3baSEkFHoDf+wQ8YNpfiWP89wzY484elzNuwZI97PGrPJwBD16U9Vwotz8pKEiNAN0/49ZlaVDcBLQ3yVesXVaq9J5cFcLWGIKzJoXUKvPRHH3OvPLD4X+fLWcdQ0UvrNsgnrq4IBZCxebwmBUlA=";
    $password = "bSkwj5CgKT42mtMsdkuoEedaJNZbh8m91OBYxYkyy6Fj/gdoveyYhl17wmnGjKnghehElqenjdU2K1+aMFnCUjBRzhGL3/upltlR+/01nqaawEiklC0Z99A0c9bYOrhvCU6wJiiiYl1p8seqFcgIWNp8qh8tSRvuy+N6YMMkMpngsvmAxx+xc5KTfhRefJcOtrF7No+rtrpF6YkCCQ3mcyQivf598mT8dnrR/ItFkhy4gjr+izc1BoSoMfvUPGEcYH/dJ1hYkK9bv4CX1PZCi+cy96GVIvRYjQmfAxLvCNJbnz8uzAArWoEVGTGghG1hqTKOhOA7Ym5UUIALHnJ8Up80aW/wn/ISNJBUwJuG916pKOnPdDjdmOVmAe674vH6ZmRYb81GhNB8W5grBi5t29ABPB7TBO3g7FRYjjj/Rs5IGX+B1HqNoIoYar2WmoXpCswIUmsVXmk9AeH10QjAseSe9i7tLUvr468fqDrCxNee7lOXbTJCnmW60gpOhtX/nZtYbRaBtvVZsXnvzospN9ul0j1FCzsDuSr7tOOwsdBJc7nrgJmaqDr6U8uokr/n07Sfs2ypNOkMh0Mv/Cdz5MIxTeLQLDyL1IKeGsc6vtzvLWll76Rt5qrhC4yYJhWBMyp/JGnZZ6LZ/U0qTickLxNNfdrGY4vhVRmB7ViyOHM=";

    $rsa = new RSA('');

    $response = array(
        'username' => $rsa->decrypt(base64_decode($username)),
        'password' => $rsa->decrypt(base64_decode($password)),

        'api_key' => $rsa->encrypt(API::generate_key(), true),
        'client_secret' => $rsa->encrypt(API::generate_secret(), true),
    );

    Flight::json($response);
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

Flight::route('/keys/public', function () {
    $rsa = new RSA('');

    $response = array(
        'pubic_key' => $rsa->getPublicKey(),
    );

    Flight::json($response);
});

Flight::route('/test', function () {
    $param1 = $_GET['param1'];
    $param2 = $_GET['param2'];
    $param3 = $_GET['param3'];
    $sign = $_GET['sign'];

    $client_secret = "sfsef6se41f6se1s41df5s74fse";

    $response = array(
        'matched' => hash_equals(md5($param1 . $param2 . $param3 . $client_secret), $sign)
    );

    Flight::json($response);
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
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
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