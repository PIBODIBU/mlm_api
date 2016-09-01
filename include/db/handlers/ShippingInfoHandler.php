<?php

require_once dirname(__DIR__) . "/../model/ShippingInfo.php";

class ShippingInfoHandler extends AbstractHandler
{
    /**
     * OVERRIDE
     */

    public static function getTableName()
    {
        return 'info_shipping';
    }

    public static function getTableSchema()
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

    public static function getPrivateSchema()
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