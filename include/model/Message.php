<?php

require_once 'AbstractModel.php';

class Message extends AbstractModel
{
    private $id;
    public $date;
    public $dialogId;
    public $important;
    public $body;

    public function __construct($id, $date, $dialogId, $important, $body)
    {
        $this->id = $id;
        $this->date = $date;
        $this->dialogId = $dialogId;
        $this->important = $important;
        $this->body = $body;
    }

    public function getFields()
    {
        return array(
            $this->id,
            $this->date,
            $this->dialogId,
            $this->important,
            $this->body
        );
    }

    public function getPublicFields()
    {
        return array(
            $this->id,
            $this->date,
            $this->dialogId,
            $this->important,
            $this->body
        );
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date)
    {
        $this->date = $date;
    }

    public function getDialogId()
    {
        return $this->dialogId;
    }

    public function setDialogId($dialogId)
    {
        $this->dialogId = $dialogId;
    }

    public function getImportant()
    {
        return $this->important;
    }

    public function setImportant($important)
    {
        $this->important = $important;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setBody($body)
    {
        $this->body = $body;
    }
}