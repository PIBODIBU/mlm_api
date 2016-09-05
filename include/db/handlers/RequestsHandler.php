<?php

require_once 'AbstractHandler.php';
require_once dirname(__DIR__) . "/../model/RestoreCode.php";

class RequestsHandler extends AbstractHandler
{
    /**
     * OVERRIDE
     */

    public static function getTableName()
    {
        return 'requests';
    }

    public static function getTableSchema()
    {
        return array(
            'id',
            'parent_uuid',
            'child_uuid',
        );
    }

    public static function getPrivateSchema()
    {
        return array();
    }

    protected function toObject($mysql_result) : RestoreCode
    {
        return new RestoreCode(
            $mysql_result['id'],
            $mysql_result['parent_uuid'],
            $mysql_result['child_uuid']
        );
    }

    /**
     * PUBLIC METHODS
     */

    public function createRequest($parentUUID, $childUUID)
    {
        return $this->sparrow
            ->from(self::getTableName())
            ->insert(array(
                'parent_uuid' => $parentUUID,
                'child_uuid' => $childUUID
            ))
            ->execute();
    }
}