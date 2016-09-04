<?php

require_once 'AbstractHandler.php';
require_once dirname(__DIR__) . "/../model/RestoreCode.php";

class RestoreCodeHandler extends AbstractHandler
{
    /**
     * OVERRIDE
     */

    public static function getTableName()
    {
        return 'restore_codes';
    }

    public static function getTableSchema()
    {
        return array(
            'uuid',
            'code',
            'created_at',
        );
    }

    public static function getPrivateSchema()
    {
        return array(
            'uuid',
            'code',
            'created_at',
        );
    }

    protected function toObject($mysql_result) : RestoreCode
    {
        return new RestoreCode(
            $mysql_result['uuid'],
            $mysql_result['code'],
            $mysql_result['created_at']
        );
    }

    /**
     * PUBLIC METHODS
     */

    public function isCodeValid($email, $code):bool
    {
        $userHandler = new UsersHandler($this->getConnection());
        $uuid = $userHandler->getUserByEmail($email, true)->getUUID();
        $codeEntry = $this->get(false, array(), new Filter('uuid', $uuid), new Filter('code', $code));
        return isset($codeEntry);
    }

    public function deleteCode($code)
    {

    }

    public function isCodeAlreadyCreated($email):bool
    {
        $userHandler = new UsersHandler($this->getConnection());
        $uuid = $userHandler->getUserByEmail($email, true)->getUUID();
        $codeEntry = $this->get(new Filter('uuid', $uuid));
        return isset($codeEntry);
    }

    /**
     * Create restore code by email
     *
     * @param $email - user's email
     * @param bool $insertToDB - insert code to the database or not
     * @return bool|string:
     *                      string - restore code
     *                      bool:
     *                          true - code successfully inserted to the database
     *                          false - error occurred during INSERT query
     */
    public function createRestoreCode($email, $insertToDB = false)
    {
        $restoreCode = APISec::generate_restore_code();

        if ($insertToDB === true) {
            $userHandler = new UsersHandler($this->getConnection());
            $user = $userHandler->get(true, array(), new Filter('email', $email));

            if ($user === NULL) {
                return false;
            }

            $sql = $this->sparrow
                ->from(RestoreCodeHandler::getTableName())
                ->insert(array(
                    'uuid' => $user->getUuid(),
                    'code' => $restoreCode
                ))
                ->sql();

            return $this->connection->query($sql);
        }

        return $restoreCode;
    }
}