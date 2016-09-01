<?php

class RestoreCode extends AbstractModel
{
    private $uuid;
    private $code;
    private $createdAt;

    public function getFields()
    {
        return array(
            $this->uuid,
            $this->code,
            $this->createdAt,
        );
    }

    public function getPublicFields()
    {
        return array();
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}