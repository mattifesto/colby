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
     * Call this function instead of fetch() when you know the result should be
     * either one row or no rows. This function will throw an exception if it
     * finds more than one row.
     *
     * @param ?ID $masterID
     * @param ?string $associationClassName
     * @param ?ID $associateID
     *
     * @return ?object
     */
    static function fetchOne(?string $masterID, ?string $associationClassName = null, ?string $associateID = null): ?stdClass {
        $rows = CBModelAssociations::fetch($masterID, $associationClassName, $associateID);

        if (empty($rows)) {
            return null;
        } else if (count($rows) === 1) {
            return $rows[0];
        } else {
            $rowsAsJSONAsMessage = CBMessageMarkup::stringToMessage(
                CBConvert::valueToPrettyJSON($rows)
            );

            $message = <<<EOT

                More than one CBModelAssociations row was found when at most one
                row was expected.

                (Rows (b))

                --- pre\n{$rowsAsJSONAsMessage}
                ---

EOT;

            CBLog::log((object)[
                'ID' => $masterID ?? $associateID,
                'message' => $message,
                'severity' => 3,
                'sourceClassName' => __CLASS__,
            ]);

            throw new Exception('More than one CBModelAssociations row was found when at most one row was expected.');
        }
    }
}
