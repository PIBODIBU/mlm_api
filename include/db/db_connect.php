<?php

require_once 'db_config.php';

class DbConnect
{
    /**
     * Establishing database connection
     * @return mysqli connection handler
     */
    static function connect()
    {
        $connection = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME) or die('Can\'t connect to database');

        if (mysqli_connect_errno()) {
            echo "Failed to connect to MySQL: " . mysqli_connect_error();
        }

        // Returning connection resource
        return $connection;
    }
}