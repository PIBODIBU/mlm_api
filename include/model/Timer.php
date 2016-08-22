<?php

require_once 'AbstractModel.php';

class Timer extends AbstractModel
{
    private $timeStart;
    private $userId;

    public function __construct($timeStart,$userId)
    {
        $this->timeStart = $timeStart;
        $this->userId = $userId;
    }

    public function getFields()
    {
        return array(
            $this->userId,
            $this->timeStart
        );
    }

    public function getPublicFields()
    {
        return array();
    }

    public function getTimeStart()
    {
        return $this->timeStart;
    }

    public function getUserId()
    {
        return $this->userId;
    }
}