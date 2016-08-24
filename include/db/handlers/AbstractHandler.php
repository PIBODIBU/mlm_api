<?php

require_once dirname(__DIR__) . "/../utils/Filter.php";

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
    }

    public function update($fields, $values, $primaryKey = array())
    {
        $sql = "UPDATE " . $this->getTableName() . " SET ";
        $primaryKeyName = $primaryKey[0];
        $primaryKeyValue = $primaryKey[1];

        for ($i = 0; $i < count($values); $i++) {
            $sql .= $fields[$i] . "='" . $values[$i] . "'" . ",";
        }

        // Remove last comma in
        $sql = substr($sql, 0, -1);
        $sql .= " WHERE BINARY " . $primaryKeyName . "='$primaryKeyValue'";

        return $this->getConnection()->query($sql);
    }

    public function getAll($ignore_fields = array(), $limit = -1, $offset = -1)
    {
        $sql = "SELECT * FROM " . $this->getTableName();
        if ($limit != -1 && $offset != -1) {
            $sql .= " LIMIT $offset, $limit";
        }

        $response = array();
        $result = $this->getConnection()->query($sql);

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $response[] = $this->removeIgnoreFields($row, $ignore_fields);
        }

        return $response;
    }

    /**
     * Get one item from database with filtering
     *
     * @param Filter $filter - filter for SQL query, which contains name and value (column name & filtering value). @see Filter
     * @param bool $convertToObject - convert result to Filter or not
     * @param array $ignoreFields - field to delete from result
     * @return array|mixed|null|void
     */
    public function get($filter, $convertToObject = false, $ignoreFields = array())
    {
        $filterName = $filter->getName();
        $filterValue = $filter->getValue();
        $sql = "SELECT * FROM " . $this->getTableName() . " WHERE BINARY $filterName='$filterValue'";

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

    protected function getTableName()
    {
        return '';
    }

    protected function getTableSchema()
    {
        return array();
    }

    public function getPrivateSchema()
    {
        return array();
    }

    public function getConnection()
    {
        return new mysqli();
    }

    protected function toObject($mysql_result)
    {
        return;
    }

    protected function removeIgnoreFields($mysql_result, $ignoreFields)
    {
        if (!isset($ignoreFields) || !isset($mysql_result)) {
            return $mysql_result;
        }

        foreach ($ignoreFields as $ignoreField) {
            unset($mysql_result[$ignoreField]);
        }

        return $mysql_result;
    }
}