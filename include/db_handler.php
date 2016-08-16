<?php

/**
 * Class to handle all db operations
 * This class will have CRUD methods for database tables
 *
 * @author Ravi Tamada
 */
class DbHandler
{
    private $conn;

    function __construct()
    {
        require_once dirname(__FILE__) . '/db_connect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }

    public function getConn()
    {
        return $this->conn;
    }

    public function close()
    {
        mysqli_close($this->conn);
    }
}