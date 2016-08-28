<?php

class Filter
{
    private $name;
    private $value;

    /**
     * Filter constructor.
     * @param $name - name of the filer
     * @param $value - filter's value
     */
    public function __construct($name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function toArray():array
    {
        return array($this->getName() => $this->getValue());
    }
}