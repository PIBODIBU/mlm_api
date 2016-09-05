<?php

class Request extends AbstractModel
{
    private $id;
    private $parentUUID;
    private $childUUID;

    public function __construct($id, $parentUUID, $childUUID)
    {
        $this->id = $id;
        $this->parentUUID = $parentUUID;
        $this->childUUID = $childUUID;
    }

    public function getFields()
    {
        return array(
            $this->id,
            $this->parentUUID,
            $this->childUUID,
        );
    }

    public function getPublicFields()
    {
        return array();
    }
}