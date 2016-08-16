<?php

class API
{
    public static function generate_key()
    {
        return bin2hex(openssl_random_pseudo_bytes(32));
    }

    public static function generate_secret()
    {
        return bin2hex(openssl_random_pseudo_bytes(16));
    }
}