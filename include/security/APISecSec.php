<?php

class APISec
{
    public static function generate_key()
    {
        return bin2hex(openssl_random_pseudo_bytes(32));
    }

    public static function generate_secret()
    {
        return bin2hex(openssl_random_pseudo_bytes(16));
    }

    public static function generate_restore_code()
    {
        return bin2hex(openssl_random_pseudo_bytes(4));
    }

    public static function generate_file_name()
    {
        return bin2hex(openssl_random_pseudo_bytes(16));
    }
}