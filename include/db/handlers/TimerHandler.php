<?php

require_once 'AbstractHandler.php';
require_once dirname(__DIR__) . "/../model/Timer.php";

class TimerHandler extends AbstractHandler
{
    public static function getTableName()
    {
        return 'timer';
    }

    public static function getTableSchema()
    {
        return array(
            'uuid',
            'time_start'
        );
    }

    public static function getPrivateSchema()
    {
        return array();
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