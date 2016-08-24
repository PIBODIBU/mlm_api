<?php

class ShippingInfo extends AbstractModel
{
    private $uuid;
    private $name;
    private $surname;
    private $address;
    private $city;
    private $postalCode;
    private $country;
    private $phone;

    public function __construct(
        $uuid,
        $name,
        $surname,
        $address,
        $city,
        $postalCode,
        $country,
        $phone
    )
    {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->surname = $surname;
        $this->address = $address;
        $this->city = $city;
        $this->postalCode = $postalCode;
        $this->country = $country;
        $this->phone = $phone;
    }

    public function getFields()
    {
        return array(
            $this->uuid,
            $this->name,
            $this->surname,
            $this->address,
            $this->city,
            $this->postalCode,
            $this->country,
            $this->phone
        );
    }

    public function getPublicFields()
    {
        return array();
    }


    /**
     * GETTERS
     */

    public function getUuid()
    {
        return $this->uuid;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSurname()
    {
        return $this->surname;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function getCity()
    {
        return $this->city;
    }

    public function getPostalCode()
    {
        return $this->postalCode;
    }

    public function getCountry()
    {
        return $this->country;
    }

    public function getPhone()
    {
        return $this->phone;
    }
}