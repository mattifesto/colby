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

        $retrieved      = LEHex160::SQLToHex160Array([
            'SQL'       => 'SELECT LOWER(HEX(`ID`)) FROM `SQLToArrayTest`']);
        $originalOnly   = implode(',', array_diff($original, $retrieved));
        $retrievedOnly  = implode(',', array_diff($retrieved, $original));

        if ($originalOnly || $retrievedOnly) {
            throw new Exception(
                "The original array and the retrieve array don't match. " .
                "Items only in original: {$originalOnly} Items only in retrieved: {$retrievedOnly}");
        }
    }
}
