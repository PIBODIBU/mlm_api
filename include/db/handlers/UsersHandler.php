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
            'email',
            'phone',
            'username',
            'password',
            'refer',
            'created_at',
            'last_login',
            'is_online',
        );
    }

    public function getPrivateSchema()
    {
        return array(
            'uuid',
            'api_key',
            'client_secret',
            'username',
            'password',
            'refer',
            'created_at',
            'last_login',
        );
    }

    public function getConnection()
    {
        return $this->connection;
    }

    protected function toObject($mysql_result)
    {
        return new User(
            $mysql_result['uuid'],
            $mysql_result['api_key'],
            $mysql_result['client_secret'],
            $mysql_result['name'],
            $mysql_result['surname']
        );
    }

    /**
     * SELF
     */

    public function getUserByUUID($uuid, $convertToObject = false, $ignoreFields = array())
    {
        $sql = "SELECT * FROM " . $this->getTableName() . " WHERE BINARY uuid='$uuid'";
        $result = $this->getConnection()->query($sql)->fetch_assoc();

        if (!isset($result)) {
            return NULL;
        }

        $result = $this->removeIgnoreFields($result, $ignoreFields);

        if ($convertToObject) {
            return $this->toObject($result);
        } else {
            return $result;
        }
    }

    public function getUserByUsername($username, $convertToObject = false, $ignoreFields = array())
    {
        $sql = "SELECT * FROM " . $this->getTableName() . " WHERE BINARY username='$username'";
        $result = $this->getConnection()->query($sql)->fetch_assoc();

        if (!isset($result)) {
            return NULL;
        }

        $result = $this->removeIgnoreFields($result, $ignoreFields);

        if ($convertToObject) {
            return $this->toObject($result);
        } else {
            return $result;
        }
    }

    public function isEmailOccupied($email) : bool
    {
        $sql = "SELECT * FROM " . $this->getTableName() . " WHERE BINARY email='$email'";
        $result = $this->getConnection()->query($sql)->fetch_assoc();
        return isset($result);
    }

    public function isUsernameOccupied($username) : bool
    {
        $sql = "SELECT * FROM " . $this->getTableName() . " WHERE BINARY username='$username'";
        $result = $this->getConnection()->query($sql)->fetch_assoc();
        return isset($result);
    }
}