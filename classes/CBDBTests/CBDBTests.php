<?php

final class CBDBTests {

    /**
     * @return null
     */
    static function hex160ToSQLTest() {
        $hex160     = '88ae5c11bcb70f15a3ec446cc9144ada7e6e2838';
        $expected   = "UNHEX('{$hex160}')";
        $actual     = CBDB::hex160ToSQL($hex160);

        if ($actual != $expected) {
            throw new Exception("The actual result `{$actual}` does not match the expected result `$expected`.");
        }

        $passed = false;

        try {
            CBDB::hex160ToSQL('a');
        } catch (Exception $exception) {
            $passed = true;
        }

        if (!$passed) {
            throw new Exception('A non 160-bit hexadecimal number was allowed.');
        }

        $passed = false;

        try {
            CBDB::hex160ToSQL('z8ae5c11bcb70f15a3ec446cc9144ada7e6e2838');
        } catch (Exception $exception) {
            $passed = true;
        }

        if (!$passed) {
            throw new Exception('A non hexadecimal character was allowed.');
        }
    }

    /**
     * This also tests `CBDB::stringToSQL`.
     *
     * @return null
     */
    static function optionalTest() {
        $func       = CBDB::optional('CBDB::stringToSQL');
        $input      = ['Fred', '"Hello"', null];
        $expected   = ["'Fred'", "'\\\"Hello\\\"'", 'NULL'];
        $actual     = array_map($func, $input);

        if ($actual !== $expected) {
            $actual     = json_encode($actual);
            $expected   = json_encode($expected);

            throw new Exception("The actual results: {$actual} do no match the expected results: {$expected}");
        }
    }

    /**
     * @return null
     */
    static function SQLToArrayTest() {
        $SQL = <<<EOT

            CREATE TEMPORARY TABLE `SQLToArrayTest`
            (
                `ID` BINARY(20) NOT NULL,
                PRIMARY KEY (`ID`)
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_520_ci

EOT;

        Colby::query($SQL);

        for ($i = 0; $i < 10; $i++) {
            $original[] = CBHex160::random();
        }

        $originalAsSQL = array_map(function($value) {
            return "(UNHEX('{$value}'))";
        }, $original);

        $originalAsSQL = implode(',', $originalAsSQL);

        Colby::query("INSERT INTO `SQLToArrayTest` VALUES {$originalAsSQL}");

        $retrieved      = CBDB::SQLToArray('SELECT LOWER(HEX(`ID`)) FROM `SQLToArrayTest`');
        $originalOnly   = implode(',', array_diff($original, $retrieved));
        $retrievedOnly  = implode(',', array_diff($retrieved, $original));

        if ($originalOnly || $retrievedOnly) {
            throw new Exception(
                "The original array and the retrieve array don't match. " .
                "Items only in original: {$originalOnly} Items only in retrieved: {$retrievedOnly}");
        }
    }

    /**
     * @return null
     */
    static function SQLToAssociativeArrayTest() {
        $SQL = <<<EOT

            CREATE TEMPORARY TABLE `SQLToAssociativeArrayTest`
            (
                `ID`    BINARY(20) NOT NULL,
                `value` BIGINT,
                PRIMARY KEY (`ID`)
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_520_ci

EOT;

        Colby::query($SQL);

        for ($i = 0; $i < 10; $i++) {
            $original[CBHex160::random()] = rand();
        }

        $originalAsSQL = array_map(function($key) use ($original) {
            $value = $original[$key];
            return "(UNHEX('{$key}'), {$value})";
        }, array_keys($original));

        $originalAsSQL = implode(',', $originalAsSQL);

        Colby::query("INSERT INTO `SQLToAssociativeArrayTest` VALUES {$originalAsSQL}");

        $retrieved      = CBDB::SQLToArray('SELECT LOWER(HEX(`ID`)), `value` FROM `SQLToAssociativeArrayTest`');
        $originalOnly   = array_diff_assoc($original, $retrieved);
        $retrievedOnly  = array_diff_assoc($retrieved, $original);

        if ($originalOnly || $retrievedOnly) {
            $originalOnly   = json_encode($originalOnly);
            $retrievedOnly  = json_encode($retrievedOnly);
            throw new Exception(
                "The original array and the retrieve array don't match. " .
                "Items only in original: {$originalOnly} Items only in retrieved: {$retrievedOnly}");
        }
    }

    /**
     * @return null
     */
    static function SQLToValueTest() {
        $SQL = <<<EOT

            CREATE TEMPORARY TABLE `SQLToValueTest`
            (
                `ID` BINARY(20) NOT NULL,
                PRIMARY KEY (`ID`)
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_520_ci

EOT;

        Colby::query($SQL);

        $original = '505b00de474d510d5075a06a94016ed0ce320475';

        Colby::query("INSERT INTO `SQLToValueTest` VALUES (UNHEX('{$original}'))");

        $retrieved = CBDB::SQLToValue('SELECT LOWER(HEX(`ID`)) FROM `SQLToValueTest`');

        if ($original != $retrieved) {
            throw new Exception(
                "The original value and the retrieve value don't match. " .
                "Original: '{$original}' Retrieved: '{$retrieved}'");
        }
    }
}
