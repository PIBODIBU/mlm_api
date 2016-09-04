<?php

require_once 'AbstractModel.php';

class Dialog extends AbstractModel
{
    private $id;
    private $createdAt;
    private $ownerUUID;
    private $peerUUID;

    public function __construct($id, $createdAt, $ownerUUID, $peerUUID)
    {
        $this->id = $id;
        $this->createdAt = $createdAt;
        $this->ownerUUID = $ownerUUID;
        $this->peerUUID = $peerUUID;
    }

    public function getFields()
    {
        return array(
            $this->id,
            $this->createdAt,
            $this->ownerUUID,
            $this->peerUUID,
        );
    }

    public function getPublicFields()
    {
        return array(
            $this->id,
            $this->createdAt,
            $this->ownerUUID,
            $this->peerUUID,
        );
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getOwnerUUID()
    {
        return $this->ownerUUID;
    }

    public function setOwnerUUID($ownerUUID)
    {
        $this->ownerUUID = $ownerUUID;
        return $this;
    }

    public function getPeerUUID()
    {
        return $this->peerUUID;
    }

    public function setPeerUUID($peerUUID)
    {
        $this->peerUUID = $peerUUID;
        return $this;
    }
}