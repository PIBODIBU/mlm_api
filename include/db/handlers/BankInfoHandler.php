<?php

require_once dirname(__DIR__) . "/../model/BankInfo.php";

class BankInfoHandler extends AbstractHandler
{
    /**
     * OVERRIDE
     */

    public static function getTableName()
    {
        return 'info_bank';
    }

    public static function getTableSchema()
    {
        return array(
            'uuid',
            'name',
            'surname',
            'iban',
            'swift_code',
            'paypal',
            'debit_card',
            'personal_code',
        );
    }

    public static function getPrivateSchema()
    {
        return array(
            'uuid',
            'bank_name',
            'bank_surname',
            'bank_iban',
            'bank_swift_code',
            'bank_paypal',
            'bank_debit_card',
            'bank_personal_code',
        );
    }

    protected function toObject($mysql_result) : BankInfo
    {
        return new BankInfo(
            $mysql_result['uuid'],
            $mysql_result['name'],
            $mysql_result['surname'],
            $mysql_result['iban'],
            $mysql_result['swift_code'],
            $mysql_result['paypal'],
            $mysql_result['debit_card'],
            $mysql_result['personal_code']
        );
    }

    public function getByToken($token, $convertToObject = false, $ignoreFields = array())
    {
        $sql = "SELECT * FROM " . $this->getTableName() . " WHERE BINARY token='$token'";
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

    public function isIBANOccupied($iban) : bool
    {
        $sql = "SELECT * FROM " . $this->getTableName() . " WHERE BINARY iban='$iban'";
        $result = $this->getConnection()->query($sql)->fetch_assoc();
        return isset($result);
    }

    public function isSwiftCodeOccupied($swiftCode) : bool
    {
        $sql = "SELECT * FROM " . $this->getTableName() . " WHERE BINARY swift_code='$swiftCode'";
        $result = $this->getConnection()->query($sql)->fetch_assoc();
        return isset($result);
    }

    public function isPaypalOccupied($paypal) : bool
    {
        $sql = "SELECT * FROM " . $this->getTableName() . " WHERE BINARY paypal='$paypal'";
        $result = $this->getConnection()->query($sql)->fetch_assoc();
        return isset($result);
    }

    public function isDebitCardOccupied($debitCard) : bool
    {
        $sql = "SELECT * FROM " . $this->getTableName() . " WHERE BINARY debit_card='$debitCard'";
        $result = $this->getConnection()->query($sql)->fetch_assoc();
        return isset($result);
    }

    public function isPersonalCodeOccupied($personalCode) : bool
    {
        $sql = "SELECT * FROM " . $this->getTableName() . " WHERE BINARY personal_code='$personalCode'";
        $result = $this->getConnection()->query($sql)->fetch_assoc();
        return isset($result);
    }
}