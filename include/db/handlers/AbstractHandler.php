<?php

require_once dirname(__DIR__) . "/../utils/Filter.php";
require_once dirname(__DIR__) . "/../../libs/sparrow/sparrow.php";

abstract class AbstractHandler
{
    protected $connection;
    protected $sparrow;

    public function __construct($connection)
    {
        $this->connection = $connection;
        $this->sparrow = new Sparrow();
        $this->sparrow->setDb($this->connection);
    }

    public function getConnection():mysqli
    {
        return $this->connection;
    }

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

    public function getAll($ignoreFields = array(), $limit = -1, $offset = -1, Filter... $filters)
    {
        $where = array();

        foreach ($filters as $filter) {
            $where = array_merge($where, $filter->toArray());
        }

        $sql = $this->sparrow
            ->from($this->getTableName())
            ->limit($limit == -1 ? null : $limit)
            ->offset($offset == -1 ? null : $offset)
            ->where($where, true)
            ->select(array_diff($this->getTableSchema(), $ignoreFields))
            ->sql();

        $response = array();
        $result = $this->getConnection()->query($sql);

        while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            $response[] = $this->removeIgnoreFields($row, $ignoreFields);
        }

        return $response;
    }

    /**
     * Get one item from database with filtering
     *
     * @param Filter[] ...$filters - filters for SQL query, which contains name and value (column name & filtering value). @see Filter
     * @param bool $convertToObject - convert result to Filter or not
     * @param array $ignoreFields - field to delete from result
     * @return array|mixed|null|void
     */
    public function get($convertToObject = false, $ignoreFields = array(), Filter... $filters)
    {
        $where = array();

        foreach ($filters as $filter) {
            $where = array_merge($where, $filter->toArray());
        }

        $sql = $this->sparrow
            ->from($this->getTableName())
            ->where($where, true)
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

    public static function getTableName()
    {
        return '';
    }

    public static function getTableSchema()
    {
        return array();
    }

    public static function getPrivateSchema()
    {
        return array();
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