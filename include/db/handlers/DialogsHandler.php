<?php

require_once 'AbstractHandler.php';
require_once 'MessagesHandler.php';
require_once dirname(__DIR__) . "/../model/Dialog.php";
require_once dirname(__DIR__) . "/../config/loc_config.php";

class DialogsHandler extends AbstractHandler
{
    public static function getTableName()
    {
        return 'dialogs';
    }

    public static function getTableSchema()
    {
        return array(
            'id',
            'created_at',
            'owner_uuid',
            'peer_uuid',
        );
    }

    public static function getPrivateSchema()
    {
        return array();
    }

    protected function toObject($mysql_result)
    {
        return new Dialog(
            $mysql_result['id'],
            $mysql_result['created_at'],
            $mysql_result['owner_uuid'],
            $mysql_result['peer_uuid']
        );
    }

    /**
     * SELF
     */

    public function getDialog($dialogId)
    {
        $messagesHandler = new MessagesHandler($this->getConnection());
        $sql_dialogs_list = $this->sparrow
            ->from($this->getTableName())
            ->where(array('id' => $dialogId))
            ->select()
            ->sql();

        $dialog = $this->getConnection()->query($sql_dialogs_list)->fetch_assoc();
        $dialog['last_message'] = $messagesHandler->getLastMessageForDialog($dialog['id']);

        return $dialog;
    }

    public function getDialogs($userUUID)
    {
        $messagesHandler = new MessagesHandler($this->getConnection());
        $response = array();
        $sql_dialogs_list = $this->sparrow
            ->from($this->getTableName())
            ->where(array('owner_uuid' => $userUUID), true)
            ->where(array('|peer_uuid' => $userUUID), true)
            ->select()
            ->sql();

        $result = $this->getConnection()->query($sql_dialogs_list);

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            // Get last message for dialog
            $lastMessage = $messagesHandler->getLastMessageForDialog($row['id']);

            // If the is no messages in this dialog - just ignore it  and continue looping
            if ($lastMessage === null)
                continue;

            $row['last_message'] = $lastMessage;
            $response[] = $row;
        }

        return $response;
    }

    public function isDialogExists($dialogId)
    {
        $dialog = $this->get(false, array(), new Filter('id', $dialogId));
        return isset($dialog);
    }
}