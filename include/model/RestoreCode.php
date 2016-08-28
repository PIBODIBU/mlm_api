<?php

class RestoreCode extends AbstractModel
{
    private $uuid;
    private $code;

    public function getFields()
    {
        return array(
            $this->uuid,
            $this->code
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
}