<?php

require_once '../db/db_connect.php';

class DB_Security
{
    private $connection;

    public function __construct()
    {
        $this->connection = DbConnect::connect();
    }

    /**
     * @param $apiKey - user's API key
     * @return bool - true if user's key is valid, false - otherwise
     */
    public function verifyUserApiKey($apiKey)
    {
        $result = $this->connection->query("SELECT * FROM users WHEN BINARY api_key='$apiKey'");
        return isset($result);
    }
}