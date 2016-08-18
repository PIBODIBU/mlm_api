<?php

abstract class AbstractHandler
{
    public function addItem($item)
    {
        $sql_values = "";

        foreach ($item->getFields() as $field) {
            $sql_values .= "'" . $field . "'" . ",";
        }

        // Remove last comma in
        $sql_values = substr($sql_values, 0, -1);
        $sql = "INSERT INTO " . $this->getTableName() . " VALUES ($sql_values)";

        return $this->getConnection()->query($sql);

        /*foreach ($this->getTableSchema() as $columnName) {

        }*/
    }

    public function getAll($ignore_fields = array())
    {
        return array();
    }

    protected function getTableName()
    {
        return '';
    }

    protected function getTableSchema()
    {
        return array();
    }

    public function getConnection()
    {
        return new mysqli();
    }

    protected function convertToObject($mysql_result)
    {
        return;
    }

    protected function removeIgnoreFields($mysql_result, $ignoreFields)
    {
        return;
    }
}