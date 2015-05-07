<?php

final class CBDB {

    /**
    Takes a SQL statement and places the values from the first column in the result into an array.
    @return {array}
    */
    public static function SQLToArray($SQL) {
        $result = Colby::query($SQL);
        $values = [];

        while ($row = $result->fetch_array(MYSQLI_NUM)) {
            $values[] = $row[0];
        }

        return $values;
    }

    /**
    Takes a SQL statement and returns the value of the first column of the first row.
    @return {string}|false
    */
    public static function SQLToValue($SQL) {
        $result = Colby::query($SQL);

        if ($row = $result->fetch_array(MYSQLI_NUM)) {
            $value = $row[0];
        } else {
            $value = false;
        }

        $result->free();

        return $value;
    }

    /**
    Takes a SQL statment and returns an object for the first row.
    @return {stdClass}|false
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
}
