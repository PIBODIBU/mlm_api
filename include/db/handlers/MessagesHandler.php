<?php

require_once 'AbstractHandler.php';
require_once dirname(__DIR__) . "/../model/Message.php";
require_once dirname(__DIR__) . "/../config/loc_config.php";

class MessagesHandler extends AbstractHandler
{
    public static function getTableName()
    {
        return 'messages';
    }

    public static function getTableSchema()
    {
        return array(
            'id',
            'date',
            'dialog_id',
            'important',
            'read_state',
            'body',
        );
    }

    public static function getPrivateSchema()
    {
        return array(
            'id',
            'date',
            'dialog_id',
            'important',
            'read_state',
            'body',
        );
    }

    protected function toObject($mysql_result)
    {
        return new Message(
            $mysql_result['id'],
            $mysql_result['date'],
            $mysql_result['dialog_id'],
            $mysql_result['important'],
            $mysql_result['read_state'],
            $mysql_result['body']
        );
    }

    /**
     * SELF
     */
}