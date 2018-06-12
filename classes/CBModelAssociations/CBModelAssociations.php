<?php

final class CBModelAssociations {

    /**
     * @param ID $ID
     * @param string $className
     * @param ID $associatedID
     *
     * @return void
     */
    static function add(string $ID, string $className, string $associatedID): void {
        $IDAsSQL = CBHex160::toSQL($ID);
        $classNameAsSQL = CBDB::stringToSQL($className);
        $associatedIDAsSQL = CBHex160::toSQL($associatedID);

        $SQL = <<<EOT

            INSERT INTO CBModelAssociations
            VALUES
            (
                {$IDAsSQL},
                {$classNameAsSQL},
                {$associatedIDAsSQL}
            )
            ON DUPLICATE KEY UPDATE
                associatedID = associatedID

EOT;

        Colby::query($SQL);
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
     * @param ?ID $ID
     * @param ?string $className
     * @param ?ID $associatedID
     *
     * @return void
     */
    static function delete(string $ID = null, string $className = null, string $associatedID = null): void {
        $clauses = [];

        if ($ID !== null) {
            $IDAsSQL = CBHex160::toSQL($ID);
            array_push($clauses, "ID = {$IDAsSQL}");
        }

        if ($className !== null) {
            $classNameAsSQL = CBDB::stringToSQL($className);
            array_push($clauses, "className = {$classNameAsSQL}");
        }

        if ($associatedID !== null) {
            $associatedIDAsSQL = CBHex160::toSQL($associatedID);
            array_push($clauses, "associatedID = {$associatedIDAsSQL}");
        }

        if (empty($clauses)) {
            throw new Exception('At least one of the parameters to CBModelAssociations::remove() must be specified.');
        } else {
            $clauses = implode(' AND ', $clauses);
        }

        $SQL = <<<EOT

            DELETE FROM CBModelAssociations
            WHERE {$clauses}

EOT;

        Colby::query($SQL);
    }

    /**
     * @deprecated use fetch()
     *
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
     * @param ?ID $ID
     * @param ?string $className
     * @param ?ID $associatedID
     *
     * @return [object]
     *
     *      {
     *          ID: ID
     *          className: string
     *          associatedID: ID
     *      }
     */
    static function fetch(?string $ID, ?string $className = null, ?string $associatedID = null): array {
        $clauses = [];

        if ($ID !== null) {
            $IDAsSQL = CBHex160::toSQL($ID);
            array_push($clauses, "ID = {$IDAsSQL}");
        }

        if ($className !== null) {
            $classNameAsSQL = CBDB::stringToSQL($className);
            array_push($clauses, "className = {$classNameAsSQL}");
        }

        if ($associatedID !== null) {
            $associatedIDAsSQL = CBHex160::toSQL($associatedID);
            array_push($clauses, "associatedID = {$associatedIDAsSQL}");
        }

        if (empty($clauses)) {
            throw new Exception('At least one of the parameters to CBModelAssociations::fetch() must be specified.');
        } else {
            $clauses = implode(' AND ', $clauses);
        }

        $SQL = <<<EOT

            SELECT  LOWER(HEX(ID)) as ID,
                    className,
                    LOWER(HEX(associatedID)) as associatedID
            FROM    CBModelAssociations
            WHERE   {$clauses}

EOT;

        return CBDB::SQLToObjects($SQL);
    }

    /**
     * @NOTE 2018.06.11
     *
     *      I think this function should be removed because it's too easy for
     *      the caller to do. Also it should return an array.
     *
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
     * @NOTE 2018.06.11
     *
     *      I think this function should be removed because it's too easy for
     *      the caller to do. Also it should return an array.
     *
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
}
