<?php

require_once 'AbstractModel.php';

class User extends AbstractModel
{
    private $UUID;
    private $apiKey;
    private $clientSecret;
    private $name;
    private $surname;
    private $username;
    private $password;

    public function __construct($UUID = "",
                                $apiKey = "",
                                $clientSecret = "",
                                $name,
                                $surname,
                                $username = "",
                                $password = "")
    {
        $this->UUID = $UUID;
        $this->apiKey = $apiKey;
        $this->clientSecret = $clientSecret;
        $this->name = $name;
        $this->surname = $surname;
        $this->username = $username;
        $this->password = $password;
    }

    public function getFields()
    {
        return array(
            $this->UUID,
            $this->apiKey,
            $this->clientSecret,
            $this->name,
            $this->surname,
            $this->username,
            $this->password
        );
    }


    public function getUUID()
    {
        return $this->UUID;
    }

    public function getApiKey()
    {
        return $this->apiKey;
    }

    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSurname()
    {
        return $this->surname;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}