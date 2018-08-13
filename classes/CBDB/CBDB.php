<?php

final class CBDB {

    private static $transactionIsActive = false;

    /**
     * Converts a string to a SQL safe string.
     *
     * @param string $value
     *
     * @return string
     */
    static function escapeString($value) {
        $value = (string)$value;
        return Colby::mysqli()->escape_string($value);
    }

    /**
     * @deprecated use `CBHex160::toSQL`
     */
    static function hex160ToSQL($values) {
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
    static function optional(callable $func) {
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
     * placed in an array.
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
    static function SQLToArray($SQL, $args = []) {
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
     *
     * @param bool? $args['valueIsJSON']
     *
     * @return mixed|false
     */
    static function SQLToValue($SQL, $args = []) {
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
    static function SQLToObject($SQL) {
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
     *
     * @return [stdClass] | [key => stdClass]
     */
    static function SQLToObjects($SQL, $args = []) {
        $keyField = null;
        extract($args, EXTR_IF_EXISTS);

        $result     = Colby::query($SQL);
        $objects    = [];

        while ($object = $result->fetch_object()) {
            if ($keyField) {
                $key            = $object->{$keyField};
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
    static function stringToSQL($value) {
        $value = CBDB::escapeString($value);
        return "'{$value}'";
    }

    /**
     * This function runs a callback inside a database transaction. It's easier
     * and more stable than writing the transaction code every time.
     *
     * @param callable $callback
     *
     * @return void
     */
    static function transaction(callable $callback): void {
        if (CBDB::$transactionIsActive) {
            throw new RuntimeException('Nested transactions are not allowed.');
        }

        CBDB::$transactionIsActive = true;
        CBLog::bufferStart();
        
        try {
            Colby::query('START TRANSACTION');

            call_user_func($callback);

            Colby::query('COMMIT');
        } catch (Throwable $throwable) {
            Colby::query('ROLLBACK');

            throw $throwable;
        } finally {
            CBDB::$transactionIsActive = false;
            CBLog::bufferEndFlush();
        }
    }

    /**
     * Returns the trimmed string escaped for SQL in single quotes ready to be
     * used as a value in a query. If the trimmed string is empty 'NULL' will be
     * returned.
     *
     * return string
     */
    static function valueToOptionalTrimmedSQL($value) {
        $value = CBConvert::valueToOptionalTrimmedString($value);
        return ($value === null) ? 'NULL' : CBDB::stringToSQL($value);
    }
}
