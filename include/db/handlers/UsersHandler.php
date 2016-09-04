<?php

require_once 'AbstractHandler.php';
require_once dirname(__DIR__) . "/../model/User.php";
require_once dirname(__DIR__) . "/../config/loc_config.php";

class UsersHandler extends AbstractHandler
{
    /**
     * OVERRIDE
     */

    public static function getTableName()
    {
        return 'users';
    }

    public static function getTableSchema()
    {
        return array(
            'uuid',
            'api_key',
            'client_secret',
            'name',
            'surname',
            'email',
            'phone',
            'photo_name',
            'username',
            'password',
            'refer',
            'created_at',
            'last_login',
            'is_online',
        );
    }

    public static function getPrivateSchema()
    {
        return array(
            'uuid',
            'api_key',
            'client_secret',
            'username',
            'password',
            'refer',
            'created_at',
            'last_login',
        );
    }

    protected function toObject($mysql_result)
    {
        return new User(
            $mysql_result['uuid'],
            $mysql_result['api_key'],
            $mysql_result['client_secret'],
            $mysql_result['name'],
            $mysql_result['surname'],
            $mysql_result['email'],
            $mysql_result['photo_name'],
            $mysql_result['phone'],
            $mysql_result['username'],
            $mysql_result['password'],
            $mysql_result['refer'],
            $mysql_result['created_at'],
            $mysql_result['last_login'],
            $mysql_result['is_online']
        );
    }

    /**
     * SELF
     */

    public function getUserByEmail($email, $convertToObject = false, $ignoreFields = array())
    {
        $sql = $this->sparrow
            ->from(self::getTableName())
            ->where(array('email' => $email), true)
            ->select(array_diff($this->getTableSchema(), $ignoreFields))
            ->sql();

        $result = $this->getConnection()->query($sql)->fetch_assoc();

        if (!isset($result)) {
            return NULL;
        }

        $result = $this->removeIgnoreFields($result, $ignoreFields);

        if ($convertToObject) {
            return $this->toObject($result);
        } else {
            return $result;
        }
    }

    public function uploadAvatar($avatar, User $user)
    {
        $fileName = APISec::generate_file_name();
        $fileLocation = UPLOAD_DIRECTORY . $fileName;
        $fileExtension = pathinfo($avatar['name'], PATHINFO_EXTENSION);
        $fileLocation .= '.' . $fileExtension;

        if (!move_uploaded_file($avatar['tmp_name'], $fileLocation)) {
            return false;
        }

        $fileName .= '.' . $fileExtension;

        $sql = $this->sparrow
            ->from(self::getTableName())
            ->where(array('uuid' => $user->getUUID()), true)
            ->update(array('photo_name' => $fileName))
            ->sql();

        return $this->getConnection()->query($sql);
    }

    public function getUserByUUID($uuid, $convertToObject = false, $ignoreFields = array())
    {
        $sql = $this->sparrow
            ->from(self::getTableName())
            ->where(array('uuid' => $uuid), true)
            ->select(array_diff($this->getTableSchema(), $ignoreFields))
            ->sql();

        $result = $this->getConnection()->query($sql)->fetch_assoc();

        if (!isset($result)) {
            return NULL;
        }

        $result = $this->removeIgnoreFields($result, $ignoreFields);
        $this->transformPhotoUrl($result);

        if ($convertToObject) {
            return $this->toObject($result);
        } else {
            return $result;
        }
    }

    public function getUserByUsername($username, $convertToObject = false, $ignoreFields = array())
    {
        $sql = "SELECT * FROM " . $this->getTableName() . " WHERE BINARY username='$username'";
        $result = $this->getConnection()->query($sql)->fetch_assoc();

        if (!isset($result)) {
            return NULL;
        }

        $result = $this->removeIgnoreFields($result, $ignoreFields);

        if ($convertToObject) {
            return $this->toObject($result);
        } else {
            return $result;
        }
    }

    public function changePassword($email, $newPassword):bool
    {
        $user = $this->getUserByEmail($email, true);

        if ($user === NULL) {
            return false;
        }

        $sql = $this->sparrow
            ->from(self::getTableName())
            ->where(array('uuid' => $user->getUUID()), true)
            ->update(array('password' => password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => PASSWORD_ENCRYPTION_COST])))
            ->sql();

        return $this->getConnection()->query($sql);
    }

    public function isEmailOccupied($email) : bool
    {
        $sql = "SELECT * FROM " . $this->getTableName() . " WHERE BINARY email='$email'";
        $result = $this->getConnection()->query($sql)->fetch_assoc();
        return isset($result);
    }

    public function isUsernameOccupied($username) : bool
    {
        $sql = "SELECT * FROM " . $this->getTableName() . " WHERE BINARY username='$username'";
        $result = $this->getConnection()->query($sql)->fetch_assoc();
        return isset($result);
    }

    public function transformPhotoUrl($mysqliResult)
    {
        $mysqliResult['photo_name'] = UPLOADS_DIR_URL . $mysqliResult['photo_name'];
    }
}