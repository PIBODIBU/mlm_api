<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/projects/mlm/include/db/db_connect.php';

class DB_Security
{
    private $connection;

    public function __construct($connection = NULL)
    {
        $this->connection = $connection == NULL ? DbConnect::connect() : $connection;
    }

    /**
     * @param $apiKey - user's API key
     * @return bool - true if user's key is valid, false - otherwise
     */
    public function verifyUserApiKey($apiKey)
    {
        $result = $this->connection->query("SELECT * FROM users WHERE BINARY api_key='$apiKey'");
        $result_array = $result->fetch_assoc();
        return isset($result_array);
    }

    /**
     * Check if signature of the request is valid
     * @param array $params - required params of the request
     * @param $user_signature - user's signature (may be invalid)
     * @param $apiKey - user's API key
     * @return bool:
     *              true - signature is valid, user is not hacker :)
     *              false - signature is not valid, user may be hacker :(
     */
    public function validateSignature($params = array(), $user_signature, $apiKey)
    {
        $client_secret = $this->getSecretFromApiKey($apiKey);
        $signature = "";

        foreach ($params as $parameter) {
            $signature .= $parameter;
        }

        $signature .= $client_secret;
        $signature = md5($signature);

        return hash_equals($signature, $user_signature);
    }

    public function validatePassword($user_password, $db_hash)
    {
        return hash_equals(md5($user_password), $db_hash);
    }

    /**
     * Get client secret from API key
     * @param $apiKey - user's API key
     * @return string:
     *                  client secret on success, empty string otherwise
     */
    public function getSecretFromApiKey($apiKey)
    {
        $result = $this->connection->query("SELECT * FROM users WHERE BINARY api_key='$apiKey'");
        $user = $result->fetch_assoc();
        return isset($user) ? $user['client_secret'] : "";
    }

    /**
     * SUPPORT METHODS
     */

    /**
     * @return mysqli - current mysqli connection
     */
    public function getConnection()
    {
        return $this->connection;
    }
}