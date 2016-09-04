<?php

require_once 'AbstractHandler.php';
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

    public function getDialogs($userUUID)
    {
        $sql = $this->sparrow
            ->from($this->getTableName())
            ->where(array('owner_uuid' => $userUUID), true)
            ->where(array('|peer_uuid' => $userUUID), true)
            ->select()
            ->sql();

        $response = array();
        $result = $this->getConnection()->query($sql);

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
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