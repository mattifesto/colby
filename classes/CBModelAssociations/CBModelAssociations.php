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

            SELECT  COUNT(*)
            FROM    `CBModelAssociations`
            WHERE   `ID` = {$IDAsSQL} AND
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
     * @return hex160|false
     */
    static function fetchAssociatedID($ID, $className) {
        $IDAsSQL = CBHex160::toSQL($ID);
        $classNameAsSQL = CBDB::stringToSQL($className);

        $SQL = <<<EOT

            SELECT  LOWER(HEX(`associatedID`))
            FROM    `CBModelAssociations`
            WHERE   `ID` = {$IDAsSQL} AND
                    `className` = {$classNameAsSQL}

EOT;

        return CBDB::SQLToValue($SQL);
    }

    /**
     * @param hex160 $ID
     * @param string $className
     *
     * @return stdClass|false
     */
    static function fetchModel($ID, $className) {
        $associatedID = CBModelAssociations::fetchAssociatedID($ID, $className);

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
        $associatedID = CBModelAssociations::fetchAssociatedID($ID, $className);

        if (empty($associatedID)) {
            return false;
        } else {
            return CBModels::fetchSpecByID($associatedID);
        }
    }

    /**
     * @NOTE 2018.06.11
     *
     *      This function needs more documenation. I'm not sure what it's used
     *      for.
     *
     * Best in a transaction.
     */
    static function makeSpec($ID, $className) {
        $spec = CBModelAssociations::fetchSpec($ID, $className);

        if ($spec === false) {
            $associatedID = CBHex160::random();
            $spec = (object)[
                'className' => $className,
                'ID' => $associatedID,
            ];

            CBModels::save([$spec]);
            CBModelAssociations::createAssociation($ID, $className, $spec->ID);

            $spec = CBModels::fetchSpecByID($associatedID);
        }

        return $spec;
    }

    /**
     * @return null
     */
    static function makeSpecForAjax() {
        $response = new CBAjaxResponse();
        $ID = $_POST['ID'];
        $className = $_POST['className'];

        try {

            Colby::query('START TRANSACTION');

            $spec = CBModelAssociations::makeSpec($ID, $className);

            Colby::query('COMMIT');

        } catch (Throwable $exception) {

            Colby::query('ROLLBACK');

            throw $exception;

        }

        if (CBModels::currentUserCanRead($spec)) {
            $response->spec = $spec;
        } else {
            $response->message = "You do not have permission to read the associated spec for ID: {$ID} and className: {$className}";
        }

        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return stdClass
     */
    static function makeSpecForAjaxPermissions() {
        return (object)['group' => 'Public'];
    }

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $SQL = <<<EOT

            CREATE TABLE IF NOT EXISTS `CBModelAssociations` (
                `ID` BINARY(20) NOT NULL,
                `className` VARCHAR(80) NOT NULL,
                `associatedID` BINARY(20) NOT NULL,

                PRIMARY KEY (`ID`, `className`, `associatedID`)
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_520_ci

EOT;

        Colby::query($SQL);
    }

    /**
     * This function will remove all the associatedID entries for a given ID and
     * class name.
     *
     * @param ID $ID
     * @param string $className
     *
     * @return void
     */
    static function removeAssociations(string $ID, string $className): void {
        $IDAsSQL = CBHex160::toSQL($ID);
        $classNameAsSQL = CBDB::stringToSQL($className);
        $SQL = <<<EOT

            DELETE FROM CBModelAssociations
            WHERE   className = {$classNameAsSQL} AND
                    ID = {$IDAsSQL}

EOT;

        Colby::query($SQL);
    }
}
