<?php

define('RSA_KEYS_LOCATION_DIRECTORY', "keys/rsa");
define('RSA_PUBLIC_KEY_LOCATION', $_SERVER['DOCUMENT_ROOT'] . "/" . RSA_KEYS_LOCATION_DIRECTORY . "/pub.key");
define('RSA_PRIVATE_KEY_LOCATION', $_SERVER['DOCUMENT_ROOT'] . "/" . RSA_KEYS_LOCATION_DIRECTORY . "/prv.key");
define('RSA_PRIVATE_KEY_PASSPHRASE', "tietoh7eeth3Izai8oovohpiesei7oizees");

class KeyStore
{
    public static function get_key_from_file($location)
    {
        return file_get_contents($location);
    }

    /**
     * RSA
     */

    public static function rsa_export_prv($prv_key)
    {
        return openssl_pkey_export_to_file($prv_key, RSA_PRIVATE_KEY_LOCATION);
    }

    public static function rsa_export_pub($pub_key)
    {
        return file_put_contents(RSA_PUBLIC_KEY_LOCATION, $pub_key);
    }
}