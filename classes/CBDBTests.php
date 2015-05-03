<?php

final class CBDBTests {

    /*
    @return null
    */
    public static function SQLToArrayTest() {
        $SQL = <<<EOT

            CREATE TEMPORARY TABLE `SQLToArrayTest`
            (
                `ID` BINARY(20) NOT NULL,
                PRIMARY KEY (`ID`)
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8
            COLLATE=utf8_unicode_ci

EOT;

        Colby::query($SQL);

        for ($i = 0; $i < 10; $i++) {
            $original[] = Colby::random160();
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

    /*
    @return null
    */
    public static function SQLToValueTest() {
        $SQL = <<<EOT

            CREATE TEMPORARY TABLE `SQLToValueTest`
            (
                `ID` BINARY(20) NOT NULL,
                PRIMARY KEY (`ID`)
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8
            COLLATE=utf8_unicode_ci

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
