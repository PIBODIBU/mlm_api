<?php

require_once dirname(__DIR__) . "/../model/ShippingInfo.php";

class ShippingInfoHandler extends AbstractHandler
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
        return 'info_shipping';
    }

    protected function getTableSchema()
    {
        return array(
            'uuid',
            'name',
            'surname',
            'address',
            'city',
            'postal_code',
            'country',
            'phone',
        );
    }

    public function getPrivateSchema()
    {
        return array(
            'uuid',
            'name',
            'surname',
            'address',
            'city',
            'postal_code',
            'country',
            'phone',
        );
    }

    public function getConnection()
    {
        return $this->connection;
    }

    protected function toObject($mysql_result) : ShippingInfo
    {
        return new ShippingInfo(
            $mysql_result['uuid'],
            $mysql_result['name'],
            $mysql_result['surname'],
            $mysql_result['address'],
            $mysql_result['city'],
            $mysql_result['postal_code'],
            $mysql_result['country'],
            $mysql_result['phone']
        );
    }
}