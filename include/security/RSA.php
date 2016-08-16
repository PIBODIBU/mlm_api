<?php

require_once 'KeyStore.php';

class RSA
{
    private $private_key;
    private $public_key;

    public function __construct()
    {
        $this->get_keys_from_file();
    }

    /**
     * PRIVATE METHODS
     */

    private function get_keys_from_file()
    {
        if (!file_exists(RSA_PUBLIC_KEY_LOCATION) || !file_exists(RSA_PRIVATE_KEY_LOCATION)) {
            return;
        }

        $this->public_key = KeyStore::get_key_from_file(RSA_PUBLIC_KEY_LOCATION);
        $this->private_key = KeyStore::get_key_from_file(RSA_PRIVATE_KEY_LOCATION);
    }

    /**
     * PUBLIC METHODS
     */

    public function generate_new_key_pair()
    {
        $key_pair = openssl_pkey_new(array(
            "private_key_bits" => 4096,
            "private_key_type" => OPENSSL_KEYTYPE_RSA
        ));

        openssl_pkey_export($key_pair, $private_key);

        $details = openssl_pkey_get_details($key_pair);
        $public_key = $details['key'];

        $this->private_key = $private_key;
        $this->public_key = $public_key;

        // Save new keys to the key storage
        if (!KeyStore::rsa_export_pub($this->public_key) || !KeyStore::rsa_export_prv($this->private_key)) {
            return true;
        }

        return false;
    }

    public function encrypt($text, $raw_output = false, $key = NULL)
    {
        openssl_public_encrypt($text, $encrypted, $key == NULL ? $this->getPublicKey() : $key);
        return $raw_output ? base64_encode($encrypted) : $encrypted;
    }

    public function decrypt($text)
    {
        openssl_private_decrypt($text, $decrypted, $this->getPrivateKey());
        return $decrypted == NULL ? openssl_error_string() : $decrypted;
    }

    /**
     * GETTERS
     */

    public function getPrivateKey()
    {
        return $this->private_key;
    }

    public function getPublicKey()
    {
        return $this->public_key;
    }
}