<?php

final class
CBDB {

    private static $transactionIsActive = false;



    /**
     * This function solves a problem with the mysqli property affected_rows
     * which will be zero after an update that doesn't acutally change anything.
     *
     * If you want to see if a row existed zero will make it seem like it did
     * not and you may incorrectly try to perform an insert.
     *
     * This function returns the number of rows found, which in this scenario
     * will let the developer know not to attempt an insert.
     *
     * @return int
     */
    static function countOfRowsMatched(): int {
        $info = Colby::mysqli()->info;

        preg_match('/^\D+(\d+)/', $info, $matches);

        $countOfRowsMatched = CBConvert::valueAsInt(
            $matches[1]
        );

        if ($countOfRowsMatched === null) {
            throw new CBExceptionWithValue(
                'The info was not parseable.',
                $info,
                'db3a3455630d1dae8c78111c206e0a1099afa8f2'
            );
        }

        return $countOfRowsMatched;
    }
    /* countOfRowsMatched() */



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
     * @return string
     */
    static function
    generateDatabasePassword(
    ): string
    {
        $generatedPassword = '';

        /**
         * @NOTE 2023_06_14
         * Matt Calkins
         *
         *      $   was removed as a potential character because of its use in
         *          docker compose files. The only way to escape it is to add
         *          another dollar sign which means you can't just simply copy
         *          the password.
         */

        $characterSets = [
            'abcdefghijklmnopqrstuvwxyz',
            'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
            '0123456789',
            '~!@#%^&*()_-+={}[]/<>,.;?:|'
        ];

        for (
            $characterSetIndex = 0;
            $characterSetIndex < count($characterSets);
            $characterSetIndex += 1
        ) {
            $characters = $characterSets[$characterSetIndex];

            $characterCount = 0;

            while ($characterCount < 2) {
                $generatedPassword .= mb_substr(
                    $characters,
                    random_int(
                        0,
                        mb_strlen($characters) - 1
                    ),
                    1
                );

                $characterCount += 1;
            }
        }

        $allCharacters = implode(
            '',
            $characterSets
        );

        $characterCount = 0;

        while ($characterCount < 22) {
            $generatedPassword .= mb_substr(
                $allCharacters,
                random_int(
                    0,
                    mb_strlen($allCharacters) - 1
                ),
                1
            );

            $characterCount += 1;
        }

        return $generatedPassword;
    }
    /* generateDatabasePassword() */



    /**
     * @return string
     */
    static function
    generateDatabaseUsername(
    ): string {
        $allowedCharacters = (
            '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
        );

        $allowedCharactersMax = strlen(
            $allowedCharacters
        ) - 1;

        $username = '';

        $count = 0;

        while ($count < 10) {
            $allowedCharactersIndex = random_int(
                0,
                $allowedCharactersMax
            );

            $username .= $allowedCharacters[
                $allowedCharactersIndex
            ];

            $count += 1;
        }

        return $username;
    }
    /* generateDatabaseUsername() */



    /**
     * @deprecated use `CBID::toSQL`
     */
    static function hex160ToSQL($values) {
        return CBID::toSQL($values);
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
     * @NOTE 2018_11_27
     *
     *      This function isn't fully deprecated yet, however its method of
     *      specifying arguments and varied behavior is not ideal. Consider
     *      using SQLToArrayOfNullableStrings() instead.
     *
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
     * @return [?string]|[mixed]|[string => ?string]|[string => mixed]|
     */
    static function SQLToArray(
        $SQL,
        $args = []
    ) {
        $valueIsJSON = false;
        extract($args, EXTR_IF_EXISTS);

        $result = Colby::query($SQL);
        $values = [];

        while ($row = $result->fetch_array(MYSQLI_NUM)) {
            if (count($row) > 1) {
                $values[$row[0]] =
                $valueIsJSON ? json_decode($row[1]) : $row[1];
            } else {
                $values[] = $valueIsJSON ? json_decode($row[0]) : $row[0];
            }
        }

        return $values;
    }
    /* SQLToArray() */



    /**
     * @param string $SQL
     *
     * @return [?string]
     *
     *      The values returned represent the values of the first column of the
     *      result. Since mysqli returns all values as either string or null
     *      regardless of type, all of the values will be nullable strings.
     */
    static function SQLToArrayOfNullableStrings(
        string $SQL
    ): array {
        $result = Colby::query($SQL);
        $values = [];

        while ($row = $result->fetch_array(MYSQLI_NUM)) {
            array_push(
                $values,
                $row[0]
            );
        }

        return $values;
    }
    /* SQLToArrayOfNullableStrings() */



    /**
     * @deprecated use SQLToValue2()
     *
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
    /* SQLToValue() */



    /**
     * @param string $SQL
     *
     * @return ?string
     *
     *      Returns the value of the first column of the first row of the result
     *      set of rows. All column values except NULL are returned as strings.
     *      Returns null if there are no rows in the result set.
     *
     * @NOTE
     *
     *      When directly fetching binary column values the result is returned
     *      as a string that is an array of the bytes of the binary data. This
     *      will NOT be a valid UTF-8 string but it can be converted to a
     *      hexadecimal UTF-8 string using the bin2hex() function.
     */
    static function SQLToValue2(
        string $SQL
    ): ?string {
        $result = Colby::query($SQL);

        /**
         * Fetch the first row as a numerically indexed array of column values.
         */
        $row = $result->fetch_array(MYSQLI_NUM);

        if ($row === null) {
            $value = null;
        } else {
            $value = $row[0];
        }

        $result->free();

        return $value;
    }
    /* SQLToValue2() */



    /**
     * @param string $SQL
     *
     * @return object|null
     *
     *      If the result has at least one row then an object representing that
     *      row will be returned.
     */
    static function
    SQLToObjectNullable(
        string $SQL
    ): ?stdClass {
        $result = Colby::query(
            $SQL
        );

        $object = $result->fetch_object();

        $result->free();

        return $object;
    }
    /* SQLToObjectNullable() */



    /**
     * Takes a SQL statement and returns an array of objects representing each
     * row.
     *
     * @return [stdClass] | [key => stdClass]
     */
    static function
    SQLToObjects(
        $SQL,
        $args = []
    ): array {
        $keyField = null;
        extract($args, EXTR_IF_EXISTS);

        $result     = Colby::query($SQL);
        $objects    = [];

        while (
            $object = $result->fetch_object()
        ) {
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
    /* SQLToObjects() */



    /**
     * Converts a string to a SQL string in single quotes ready to be used as a
     * value in a query without any alteration.
     *
     * echo CBDB::stringToSQL("The 'amazing' race.")
     *
     *      'The \'amazing\' race.'
     *
     * @return string
     */
    static function stringToSQL($value): string {
        $value = CBDB::escapeString($value);
        return "'{$value}'";
    }
    /* stringToSQL() */



    /**
     * This function runs a callback inside a database transaction. It's easier
     * and more stable than writing the transaction code every time.
     *
     * @param callable $callback
     *
     *      If the callback returns the string 'CBDB_transaction_rollback' or
     *      throws an exception, the transaction will be rolled back; otherwise
     *      it will be committed.
     *
     * @return void
     */
    static function
    transaction(
        callable $callback
    ): void {
        if (
            CBDB::$transactionIsActive
        ) {
            throw new RuntimeException(
                'Nested transactions are not allowed.'
            );
        }

        CBDB::$transactionIsActive = true;

        CBLog::bufferStart();

        try {
            Colby::query(
                'START TRANSACTION'
            );

            $result = call_user_func(
                $callback
            );

            if (
                $result === 'CBDB_transaction_rollback'
            ) {
                Colby::query(
                    'ROLLBACK'
                );
            } else {
                Colby::query(
                    'COMMIT'
                );
            }
        } catch (
            Throwable $throwable
        ) {
            Colby::query(
                'ROLLBACK'
            );

            throw $throwable;
        } finally {
            CBDB::$transactionIsActive = false;

            CBLog::bufferEndFlush();
        }
    }
    /* transaction() */



    /**
     * @return bool
     */
    static function
    transactionIsActive(
    ): bool {
        return CBDB::$transactionIsActive;
    }
    /* transactionIsActive() */



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
