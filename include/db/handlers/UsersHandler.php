<?php

require_once 'AbstractHandler.php';
require_once dirname(__DIR__) . "/../model/User.php";

class UsersHandler extends AbstractHandler
{
    private $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    /**
     * OVERRIDE
     */

    protected function getTableName()
    {
        return 'users';
    }

    protected function getTableSchema()
    {
        return array(
            'uuid',
            'api_key',
            'client_secret',
            'name',
            'surname',
            'username',
            'password',
        );
    }

    protected function getConnection()
    {
        return $this->connection;
    }

    /**
     * SELF
     */

    public function getUserByUUID($uuid, $convertToObject = false)
    {
        $sql = "SELECT * FROM users WHERE BINARY uuid='$uuid'";
        $result = $this->getConnection()->query($sql)->fetch_assoc();

        if (!isset($result)) {
            return NULL;
        }
        if ($convertToObject) {
            return new User(
                $result['uuid'],
                $result['api_key'],
                $result['client_secret'],
                $result['name'],
                $result['surname']
            );
        } else {
            return $result;
        }
    }
}