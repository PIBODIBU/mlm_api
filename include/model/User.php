<?php

require_once 'AbstractModel.php';

class User extends AbstractModel
{
    private $UUID;
    private $apiKey;
    private $clientSecret;
    private $name;
    private $surname;
    private $email;
    private $phone;
    private $username;
    private $password;
    private $refer;
    private $createdAt;
    private $lastLogin;
    private $isOnline;

    public function __construct($UUID = "",
                                $apiKey = "",
                                $clientSecret = "",
                                $name,
                                $surname,
                                $email,
                                $phone,
                                $username = "",
                                $password = "",
                                $refer,
                                $createdAt,
                                $lastLogin,
                                $isOnline
    )
    {
        $this->UUID = $UUID;
        $this->apiKey = $apiKey;
        $this->clientSecret = $clientSecret;
        $this->name = $name;
        $this->surname = $surname;
        $this->email = $email;
        $this->phone = $phone;
        $this->username = $username;
        $this->password = $password;
        $this->refer = $refer;
        $this->createdAt = $createdAt;
        $this->lastLogin = $lastLogin;
        $this->isOnline = $isOnline;
    }

    public function getFields()
    {
        return array(
            $this->UUID,
            $this->apiKey,
            $this->clientSecret,
            $this->name,
            $this->surname,
            $this->email,
            $this->phone,
            $this->username,
            $this->password,
            $this->refer,
            $this->createdAt,
            $this->lastLogin,
            $this->isOnline,
        );
    }

    public function getPublicFields()
    {
        return array(
            $this->name,
            $this->surname,
            $this->email,
            $this->phone,
            $this->isOnline
        );
    }

    public function getUUID(): string
    {
        return $this->UUID;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getClientSecret(): string
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

    public function getEmail()
    {
        return $this->email;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getRefer()
    {
        return $this->refer;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    public function getIsOnline()
    {
        return $this->isOnline;
    }
}