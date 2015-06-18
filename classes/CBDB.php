<?php

final class CBDB {

    /**
     * @deprecated use `CBHex160::toSQL`
     */
    public static function hex160ToSQL($values) {
        return CBHex160::toSQL($values);
    }

    /**
     * This function returns a new function that takes a single value as a
     * parameter. If that value is `null` the returned function will return
     * 'NULL'; otherwise it will pass the value to the inner function and
     * return the result.
     *
     *      $optionalStringToSQL    = CBDB::optional('CBDB::stringToSQL');
     *      $value                  = $optionalStringToSQL(null); // 'NULL'
     *
     * @return {function}
     */
    public static function optional(callable $func) {
        return function($value) use ($func) {
            if ($value === null) {
                return 'NULL';
            } else {
                return call_user_func($func, $value);
            }
        };
    }

    /**
     * If the query returns one column the values for that column will be
     * place in an array.
     *
     * If the query returns two or more columns the value from the first column
     * will be used as the associative array key and the value from the second
     * column will be used as the associative array value.
     *
     * If the first column of a multicolumn query is not unique, some data will
     * not be returned.
     *
     * @return {array}
     */
    public static function SQLToArray($SQL, $args = []) {
        $valueIsJSON = false;
        extract($args, EXTR_IF_EXISTS);

        $result = Colby::query($SQL);
        $values = [];

        while ($row = $result->fetch_array(MYSQLI_NUM)) {
            if (count($row) > 1) {
                $values[$row[0]] = $valueIsJSON ? json_decode($row[1]) : $row[1];
            } else {
                $values[] = $valueIsJSON ? json_decode($row[0]) : $row[0];
            }
        }

        return $values;
    }

    /**
     * Takes a SQL statement and returns the value of the first column of the
     * first row.
     * @return {string}|false
     */
    public static function SQLToValue($SQL, $args = []) {
        $valueIsJSON = false;
        extract($args, EXTR_IF_EXISTS);

        $result = Colby::query($SQL);

        if ($row = $result->fetch_array(MYSQLI_NUM)) {
            $value = $valueIsJSON ? json_decode($row[0]) : $row[0];
        } else {
            $value = false;
        }

        $result->free();

        return $value;
    }

    /**
     * Takes a SQL statement and returns an object for the first row.
     * @return {stdClass}|false
     */
    public static function SQLToObject($SQL) {
        $result = Colby::query($SQL);
        $row    = $result->fetch_object();

        $result->free();

        if ($row) {
            return $row;
        } else {
            return false;
        }
    }

    /**
     * Takes a SQL statement and returns an array of objects representing each
     * row.
     * @return [{stdClass}]
     */
    public static function SQLToObjects($SQL, $args = []) {
        $keyField = null;
        extract($args, EXTR_IF_EXISTS);

        $result     = Colby::query($SQL);
        $objects    = [];

        while ($object = $result->fetch_object()) {
            if ($keyField) {
                $key            = $object[$keyField];
                $objects[$key]  = $object;
            } else {
                $objects[]      = $object;
            }
        }

        $result->free();
        return $objects;
    }

    /**
     * Converts a string to a SQL string in single quotes ready to be used as a
     * value in a query without any alteration.
     *
     * echo CBDB::stringToSQL("The 'amazing' race.")
     *
     *      'The \'amazing\' race.'
     *
     * @return {string}
     */
    public static function stringToSQL($value) {
        $value = (string)$value;
        $value = Colby::mysqli()->escape_string($value);
        return "'{$value}'";
    }
}
