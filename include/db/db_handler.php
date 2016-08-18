<?php

require_once 'db_connect.php';

class DbHandler
{
    private $connection;

    function __construct($connection)
    {
        $this->connection = $connection == NULL ? DbConnect::connect() : $connection;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function close()
    {
        mysqli_close($this->connection);
    }
}