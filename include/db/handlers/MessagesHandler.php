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
            'sender_uuid',
            'recipient_uuid',
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
            'sender_uuid',
            'recipient_uuid',
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
            $mysql_result['sender_uuid'],
            $mysql_result['recipient_uuid'],
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

    public function getLastMessageForDialog($dialog_id)
    {
        $sql = $this->sparrow
            ->from(self::getTableName())
            ->where(array('dialog_id' => $dialog_id))
            ->sortDesc('id')
            ->limit(1)
            ->offset(0)
            ->select()
            ->sql();

        $message = $this->getConnection()->query($sql)->fetch_assoc();

        return $message;
    }

    public function getDialogIdFromMessage($messageId)
    {
        $sql = $this->sparrow
            ->from(self::getTableName())
            ->where(array('id' => $messageId))
            ->select(array('dialog_id'))
            ->sql();

        return $this->getConnection()->query($sql)->fetch_assoc()['dialog_id'];
    }

    public function markAsRead($messageId)
    {
        $sql = $this->sparrow
            ->from(self::getTableName())
            ->where(array('id' => $messageId))
            ->update(array('read_state' => 1))
            ->sql();

        return $this->getConnection()->query($sql);
    }

    public function markImportant($messageId, $important)
    {
        $sql = $this->sparrow
            ->from(self::getTableName())
            ->where(array('id' => $messageId))
            ->update(array('important' => $important))
            ->sql();

        return $this->getConnection()->query($sql);
    }

    public function isMyMessage($apiKey, $dialogId)
    {
        $userHandler = new UsersHandler($this->getConnection());
        $dialogsHandler = new DialogsHandler($this->getConnection());
        $user = $userHandler->get(true, array(), new Filter('api_key', $apiKey));

        $dialog = $dialogsHandler->get(true, array(), new Filter('id', $dialogId));

        if ($dialog === NULL) {
            return false;
        }

        if ($dialog->getPeerUUID() == $user->getUUID() || $dialog->getOwnerUUID() == $user->getUUID())
            return true;
        else
            return false;
    }
}