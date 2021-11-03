<?php

/**
 * @see documentation
 */
final class
CBModelAssociations {

    /* -- functions -- */



    /**
     * @param CBID $CBID
     * @param string $associationKey
     * @param CBID $associatedCBID
     *
     * @return void
     */
    static function
    add(
        string $CBID,
        string $associationKey,
        string $associatedCBID
    ): void {
        $association = [
            $CBID,
            $associationKey,
            $associatedCBID,
        ];

        CBModelAssociations::addMultiple(
            [
                $association,
            ]
        );
    }
    /* add() */



    /**
     * @param [[<CBID>, <associationKey>, <associatedCBID>]]
     */
    static function
    addMultiple(
        array $associations
    ): void {
        if (
            count($associations) === 0
        ) {
            return;
        }

        $values = array_map(
            function (
                $association
            ) {
                if (
                    !is_array($association) ||
                    count($association) !== 3
                ) {
                    throw CBException::createWithValue(
                        "This is not a valid association.",
                        $association,
                        'c5e1fa8789f4b3217d12674ba6e19ccfe89dc639'
                    );
                }

                return (
                    '(' .
                    CBID::toSQL($association[0]) .
                    ',' .
                    CBDB::stringToSQL($association[1]) .
                    ',' .
                    CBID::toSQL($association[2]) .
                    ')'
                );
            },
            $associations
        );

        $values = implode(
            ',',
            $values
        );

        $SQL = <<<EOT

            INSERT INTO CBModelAssociations

            VALUES {$values}

            ON DUPLICATE KEY UPDATE
                associatedID = associatedID

        EOT;

        Colby::query($SQL);
    }
    /* addMultiple() */



    /**
     * @param ?CBID $ID
     * @param ?string $associationKey
     * @param ?CBID $associatedID
     *
     * @return void
     */
    static function
    delete(
        string $ID = null,
        string $associationKey = null,
        string $associatedID = null
    ): void {
        $clauses = [];

        if ($ID !== null) {
            $IDAsSQL = CBID::toSQL($ID);

            array_push(
                $clauses,
                "ID = {$IDAsSQL}"
            );
        }

        if ($associationKey !== null) {
            $associationKeyAsSQL = CBDB::stringToSQL(
                $associationKey
            );

            array_push(
                $clauses,
                "className = {$associationKeyAsSQL}"
            );
        }

        if ($associatedID !== null) {
            $associatedIDAsSQL = CBID::toSQL(
                $associatedID
            );

            array_push(
                $clauses,
                "associatedID = {$associatedIDAsSQL}"
            );
        }

        if (
            empty($clauses)
        ) {
            throw new Exception(
                'At least one of the parameters to ' .
                'CBModelAssociations::delete() must be specified.'
            );
        } else {
            $clauses = implode(
                ' AND ',
                $clauses
            );
        }

        $SQL = <<<EOT

            DELETE FROM CBModelAssociations
            WHERE {$clauses}

        EOT;

        Colby::query($SQL);
    }
    /* delete() */



    /**
     * This function is different that CBModelAssociations::delete() in that
     * you must specify the CBID, associationKey, and associatedCBID for every
     * row that you want deleted.
     *
     * @param [[<CBID>, <associationKey>, <associatedCBID>]]
     */
    static function
    deleteMultiple(
        array $associations
    ): void {
        if (
            count($associations) === 0
        ) {
            return;
        }

        $values = array_map(
            function ($association) {
                return (
                    '(' .
                    CBID::toSQL($association[0]) .
                    ',' .
                    CBDB::stringToSQL($association[1]) .
                    ',' .
                    CBID::toSQL($association[2]) .
                    ')'
                );
            },
            $associations
        );

        $values = implode(
            ',',
            $values
        );

        $SQL = <<<EOT

            DELETE FROM CBModelAssociations

            WHERE (ID, className, associatedID) IN ({$values})

        EOT;

        Colby::query($SQL);
    }
    /* deleteMultiple() */



    /**
     * @param CBID|[CBID]|null $IDs
     * @param string|null $associationKey
     * @param CBID|null $associatedID
     *
     * @return [object]
     *
     *      {
     *          ID: CBID
     *          className: string
     *          associatedID: CBID
     *      }
     */
    static function
    fetch(
        $IDs,
        ?string $associationKey = null,
        ?string $associatedID = null
    ): array {
        $clauses = [];

        if ($IDs !== null) {
            if (
                !is_array($IDs)
            ) {
                $IDs = [$IDs];
            }

            $IDsAsSQL = CBID::toSQL(
                $IDs
            );

            array_push(
                $clauses,
                "ID IN ({$IDsAsSQL})"
            );
        }

        if (
            $associationKey !== null
        ) {
            $associationKeyAsSQL = CBDB::stringToSQL(
                $associationKey
            );

            array_push(
                $clauses,
                "className = {$associationKeyAsSQL}"
            );
        }

        if (
            $associatedID !== null
        ) {
            $associatedIDAsSQL = CBID::toSQL(
                $associatedID
            );

            array_push(
                $clauses,
                "associatedID = {$associatedIDAsSQL}"
            );
        }

        if (
            empty($clauses)
        ) {
            throw new Exception(
                'At least one of the parameters to ' .
                'CBModelAssociations::fetch() must be specified.'
            );
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
    /* fetch() */



    /**
     * @deprecated 2021_09_28
     *
     *      Use CBModelAssociations::fetchSingularAssociatedCBID()
     *
     * @param CBID $modelID
     * @param string $associationKey
     *
     * @return CBID|null
     */
    static function
    fetchAssociatedID(
        string $primaryID,
        string $associationKey
    ): ?string {
        return CBModelAssociations::fetchSingularSecondCBID(
            $primaryID,
            $associationKey
        );
    }
    /* fetchAssociatedID() */



    /**
     * @param CBID $CBID
     * @param string $associationKey
     *
     * @return [CBID]
     */
    static function
    fetchAssociatedIDs(
        $CBID,
        $associationKey
    ): array {
        $associations = CBModelAssociations::fetch(
            $CBID,
            $associationKey
        );

        return array_map(
            function ($association) {
                return $association->associatedID;
            },
            $associations
        );
    }
    /* fetchAssociatedIDs() */



    /**
     * @param ID $modelID
     * @param string $associationKey
     *
     * @return object|null
     */
    static function
    fetchAssociatedModel(
        string $primaryID,
        string $associationKey
    ): ?stdClass {
        $associatedID = CBModelAssociations::fetchAssociatedID(
            $primaryID,
            $associationKey
        );

        if (
            $associatedID === null
        ) {
            return null;
        } else {
            return CBModelCache::fetchModelByID(
                $associatedID
            );
        }
    }
    /* fetchAssociatedModel() */



    /**
     * @param CBID $CBID
     * @param string $associationKey
     *
     * @return [object]
     */
    static function
    fetchAssociatedModels(
        string $CBID,
        string $associationKey
    ): array {
        $associatedIDs = CBModelAssociations::fetchAssociatedIDs(
            $CBID,
            $associationKey
        );

        if (
            count($associatedIDs) === 0
        ) {
            return [];
        } else {
            return CBModelCache::fetchModelsByID(
                $associatedIDs
            );
        }
    }
    /* fetchAssociatedModels() */



    /**
     * Call this function instead of fetch() when you know the result should be
     * either one row or no rows. This function will throw an exception if it
     * finds more than one row.
     *
     * @param ?CBID $primaryID
     * @param ?string $associationClassName
     * @param ?CBID $associatedID
     *
     * @return ?object
     */
    static function
    fetchOne(
        ?string $primaryID,
        ?string $associationClassName = null,
        ?string $associatedID = null
    ): ?stdClass {
        $rows = CBModelAssociations::fetch(
            $primaryID,
            $associationClassName,
            $associatedID
        );

        if (
            empty($rows)
        ) {
            return null;
        } else if (
            count($rows) === 1
        ) {
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

            CBLog::log(
                (object)[
                    'ID' => $primaryID ?? $associatedID,
                    'message' => $message,
                    'severity' => 3,
                    'sourceClassName' => __CLASS__,
                ]
            );

            throw new Exception(
                'More than one CBModelAssociations row was found when ' .
                'at most one row was expected.'
            );
        }
    }
    /* fetchOne() */



    /**
     * @see documentation
     *
     * @param string $associationKey
     * @param CBID $secondCBID
     *
     * @return string|null
     */
    static function
    fetchSingularFirstCBID(
        string $associationKey,
        string $secondCBID
    ): ?string {
        $associations = CBModelAssociations::fetch(
            null,
            $associationKey,
            $secondCBID
        );

        if (
            count($associations) === 0
        ) {
            return null;
        } else if (
            count($associations) === 1
        ) {
            return $associations[0]->ID;
        } else {
            throw new CBException(
                CBConvert::stringToCleanLine(<<<EOT

                    There is more than one first CBID associated with the
                    association key "{$associationKey}" and the second CBID
                    "{$secondCBID}".

                EOT),
                '',
                'cfc1e2d9c8aa55d6ac302b153d6e10445cfa645e'
            );
        }
    }
    /* fetchSingularFirstCBID() */



    /**
     * @see documentation
     *
     * @param CBID $firstCBID
     * @param string $associationKey
     *
     * @return string|null
     */
    static function
    fetchSingularSecondCBID(
        string $firstCBID,
        string $associationKey
    ): ?string {
        $associations = CBModelAssociations::fetch(
            $firstCBID,
            $associationKey
        );

        if (
            count($associations) === 0
        ) {
            return null;
        } else if (
            count($associations) === 1
        ) {
            return $associations[0]->associatedID;
        } else {
            throw new CBException(
                CBConvert::stringToCleanLine(<<<EOT

                    There is more than one second CBID associated with the first
                    CBID "{$firstCBID}" and the association key
                    "{$associationKey}".

                EOT),
                '',
                'cfc1e2d9c8aa55d6ac302b153d6e10445cfa645e'
            );
        }
    }
    /* fetchSingularSecondCBID() */



    /**
     * @TODO
     *
     *      This function should be replaced with a function named
     *      replaceSecondCBID() to match the new naming standard for
     *      associations.
     *
     * This function is useful in situations where you have a one-to-one
     * association and you want to add or replace the associated ID while making
     * sure the association remains singular.
     *
     * @param ID $ID
     * @param string $associationKey
     * @param ID $associatedID
     *
     * @return void
     */
    static function
    replaceAssociatedID(
        string $firstCBID,
        string $associationKey,
        string $secondCBID
    ): void {
        CBModelAssociations::delete(
            $firstCBID,
            $associationKey
        );

        CBModelAssociations::add(
            $firstCBID,
            $associationKey,
            $secondCBID
        );
    }
    /* replaceAssociatedID() */

}
