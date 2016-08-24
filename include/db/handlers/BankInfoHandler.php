<?php

require_once dirname(__DIR__) . "/../model/BankInfo.php";

class BankInfoHandler extends AbstractHandler
{
    private $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    /**
     * OVERRIDE
     */

    protected function getTableName()
    {
        return 'info_bank';
    }

    protected function getTableSchema()
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

    public function getPrivateSchema()
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

    public function getConnection()
    {
        return $this->connection;
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
}