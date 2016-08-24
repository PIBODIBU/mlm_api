<?php

class BankInfo extends AbstractModel
{
    private $uuid;
    private $name;
    private $surname;
    private $iban;
    private $swiftCode;
    private $paypal;
    private $debitCard;
    private $personalCode;

    public function __construct(
        $uuid,
        $name,
        $surname,
        $iban,
        $swiftCode,
        $paypal,
        $debitCard,
        $personalCode
    )
    {
        $this->uuid = $uuid;
        $this->name = $name;
        $this->surname = $surname;
        $this->iban = $iban;
        $this->swiftCode = $swiftCode;
        $this->paypal = $paypal;
        $this->debitCard = $debitCard;
        $this->personalCode = $personalCode;
    }

    public function getFields()
    {
        return array(
            $this->uuid,
            $this->name,
            $this->surname,
            $this->iban,
            $this->swiftCode,
            $this->paypal,
            $this->debitCard,
            $this->personalCode
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

    public function getIban()
    {
        return $this->iban;
    }

    public function getSwiftCode()
    {
        return $this->swiftCode;
    }

    public function getPaypal()
    {
        return $this->paypal;
    }

    public function getDebitCard()
    {
        return $this->debitCard;
    }

    public function getPersonalCode()
    {
        return $this->personalCode;
    }
}