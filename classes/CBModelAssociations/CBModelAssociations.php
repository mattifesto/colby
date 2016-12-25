<?php

final class CBModelAssociations {

    /**
     * This function should be called inside a transaction.
     *
     * @param hex160 $ID
     * @param string $className
     * @param hex160 $ID
     *
     * @return bool
     *  Returns true if the association was created, false if another
     *  association already exists.
     */
    static function createAssociation($ID, $className, $associatedID) {
        $IDAsSQL = CBHex160::toSQL($ID);
        $classNameAsSQL = CBDB::stringToSQL($className);
        $associatedIDAsSQL = CBHex160::toSQL($associatedID);

        $SQL = <<<EOT

            SELECT COUNT(*)
            FROM `CBModelAssociations`
            WHERE `ID` = {$IDAsSQL} AND
                  `className` = {$classNameAsSQL}

EOT;

        $count = CBDB::SQLToValue($SQL);

        if ($count > 0) {
            return false;
        } else {
            $SQL = <<<EOT

                INSERT INTO `CBModelAssociations`
                VALUES (
                    $IDAsSQL,
                    $classNameAsSQL,
                    $associatedIDAsSQL
                )

EOT;

            Colby::query($SQL);

            return true;
        }
    }

    /**
     * @param hex160 $ID
     * @param string $className
     *
     * @return stdClass|false
     */
    static function fetchModel($ID, $className) {
        $IDAsSQL = CBHex160::toSQL($ID);
        $classNameAsSQL = CBDB::stringToSQL($className);

        $SQL = <<<EOT

            SELECT LOWER(HEX(`associatedID`))
            FROM `CBModelAssociations`
            WHERE `ID` = {$IDAsSQL} AND
                  `className` = {$classNameAsSQL}

EOT;

        $associatedID = CBDB::SQLToValue($SQL);

        if (empty($associatedID)) {
            return false;
        } else {
            return CBModels::fetchModelByID($associatedID);
        }
    }

    /**
     * @param hex160 $ID
     * @param string $className
     *
     * @return stdClass|false
     */
    static function fetchSpec($ID, $className) {
        $IDAsSQL = CBHex160::toSQL($ID);
        $classNameAsSQL = CBDB::stringToSQL($className);

        $SQL = <<<EOT

            SELECT LOWER(HEX(`associatedID`))
            FROM `CBModelAssociations`
            WHERE `ID` = {$IDAsSQL} AND
                  `className` = {$classNameAsSQL}

EOT;

        $associatedID = CBDB::SQLToValue($SQL);

        if (empty($associatedID)) {
            return false;
        } else {
            return CBModels::fetchSpecByID($associatedID);
        }
    }

    /**
     * @return null
     */
    static function install() {
        $SQL = <<<EOT

            CREATE TABLE IF NOT EXISTS `CBModelAssociations` (
                `ID` BINARY(20) NOT NULL,
                `className` VARCHAR(80) NOT NULL,
                `associatedID` BINARY(20) NOT NULL,

                PRIMARY KEY (`ID`, `className`, `associatedID`)
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8
            COLLATE=utf8_unicode_ci

EOT;

        Colby::query($SQL);
    }
}
