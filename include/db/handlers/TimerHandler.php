<?php

require_once 'AbstractHandler.php';
require_once dirname(__DIR__) . "/../model/Timer.php";

class TimerHandler extends AbstractHandler
{
    private $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    protected function getTableName()
    {
        return 'timer';
    }

    protected function getTableSchema()
    {
        return array(
            'uuid',
            'time_start'
        );
    }

    public function getPrivateSchema()
    {
        return array();
    }

    public function getAll($ignore_fields = array(), $limit, $offset)
    {
        $sql = "SELECT * FROM " . $this->getTableName();
        $result = $this->getConnection()->query($sql)->fetch_assoc();

        $result = $this->removeIgnoreFields($result, $ignore_fields);

        return $result;
    }

    protected function toObject($mysql_result)
    {
        return new Timer(
            $mysql_result['uuid'],
            $mysql_result['time_start']);
    }

    protected function removeIgnoreFields($mysql_result, $ignoreFields)
    {
        if (!isset($ignoreFields) || !isset($mysql_result)) {
            return $mysql_result;
        }

        foreach ($ignoreFields as $ignoreField) {
            unset($mysql_result[$ignoreField]);
        }

        return $mysql_result;
    }

    public function getTimerByUserId($user_id, $convertToObject = false, $ignoreFields = array())
    {
        $sql = "SELECT * FROM " . $this->getTableName() . " WHERE uuid='$user_id'";
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
}