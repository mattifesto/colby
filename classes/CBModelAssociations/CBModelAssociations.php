<?php

/**
 * @see documentation
 */
final class
CBModelAssociations {

    /* -- CBInstall interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBInstall_requiredClassNames(
    ): array {
        return [
            'CBModelAssociationsTable',
        ];
    }
    /* CBInstall_requiredClassNames() */



    /* -- functions -- */



    /**
     * @deprecated 2021_11_26
     *
     *      Use CBModelAssociations::insertOrUpdate()
     *
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
     * @deprecated 2021_11_26
     *
     *      Use CBModelAssociations::insertOrUpdate()
     *
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

            (
                ID,
                className,
                associatedID
            )

            VALUES {$values}

            ON DUPLICATE KEY UPDATE
                associatedID = associatedID

        EOT;

        Colby::query(
            $SQL
        );
    }
    /* addMultiple() */



    /**
     * @param ?CBID $firstCBID
     * @param ?string $associationKey
     * @param ?CBID $secondCBID
     *
     * @return void
     */
    static function
    delete(
        string $firstCBID = null,
        string $associationKey = null,
        string $secondCBID = null
    ): void {
        $clauses = [];

        if (
            $firstCBID !== null
        ) {
            $firstCBIDAsSQL = CBID::toSQL(
                $firstCBID
            );

            array_push(
                $clauses,
                "ID = {$firstCBIDAsSQL}"
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

        if (
            $secondCBID !== null
        ) {
            $secondCBIDAsSQL = CBID::toSQL(
                $secondCBID
            );

            array_push(
                $clauses,
                "associatedID = {$secondCBIDAsSQL}"
            );
        }

        if (
            empty(
                $clauses
            )
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

            DELETE FROM
            CBModelAssociations

            WHERE
            {$clauses}

        EOT;

        Colby::query(
            $SQL
        );
    }
    /* delete() */



    /**
     * This function is different than CBModelAssociations::delete() in that you
     * must specify the firstCBID, associationKey, and secondCBID for every row
     * that you want deleted.
     *
     * @param [[<firstCBID>, <associationKey>, <secondCBID>]]
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
            function (
                $association
            ) {
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

            DELETE FROM
            CBModelAssociations

            WHERE
            (ID, className, associatedID)
            IN
            ({$values})

        EOT;

        Colby::query(
            $SQL
        );
    }
    /* deleteMultiple() */



    /**
     * @param CBID|[CBID]|null $firstCBIDs
     * @param string|null $associationKey
     * @param CBID|null $secondCBID
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
        $firstCBIDs,
        ?string $associationKey = null,
        ?string $secondCBID = null
    ): array {
        $clauses = [];

        if (
            $firstCBIDs !== null
        ) {
            if (
                !is_array($firstCBIDs)
            ) {
                $firstCBIDs = [$firstCBIDs];
            }

            $firstCBIDsAsSQL = CBID::toSQL(
                $firstCBIDs
            );

            array_push(
                $clauses,
                "ID IN ({$firstCBIDsAsSQL})"
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
            $secondCBID !== null
        ) {
            $secondCBIDAsSQL = CBID::toSQL(
                $secondCBID
            );

            array_push(
                $clauses,
                "associatedID = {$secondCBIDAsSQL}"
            );
        }

        if (
            empty(
                $clauses
            )
        ) {
            throw new Exception(
                'At least one of the parameters to ' .
                'CBModelAssociations::fetch() must be specified.'
            );
        } else {
            $clauses = implode(' AND ', $clauses);
        }

        $SQL = <<<EOT

            SELECT

            LOWER(HEX(ID))
            AS
            ID,

            className,

            LOWER(HEX(associatedID))
            AS
            associatedID

            FROM
            CBModelAssociations

            WHERE
            {$clauses}

        EOT;

        return CBDB::SQLToObjects(
            $SQL
        );
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
        string $firstCBID,
        string $associationKey
    ): array {
        $associatedIDs = CBModelAssociations::fetchAssociatedIDs(
            $firstCBID,
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
     * @return object|null
     *
     *      {
     *          ID: CBID
     *          className: string
     *          associatedID: CBID
     *      }
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
        }

        else if (
            count($rows) === 1
        ) {
            return $rows[0];
        }

        else {
            $rowsAsJSONAsMessage = CBMessageMarkup::stringToMessage(
                CBConvert::valueToPrettyJSON(
                    $rows
                )
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
                CBConvert::stringToCleanLine(<<<EOT

                    More than one CBModelAssociations row was found when at most
                    one row was expected.

                EOT)
            );
        }
    }
    /* fetchOne() */



    /**
     * @param CBID $firstCBID
     * @param string $associationKey
     * @param string $sortingOrder
     * @param int $maximumResultCount
     * @param int|null $sortingValueMinimum
     * @param int|null $sortingValueMaximum
     * @param int|null $sortingValueDifferentiatorMinimum
     * @param int|null $sortingValueDifferentiatorMaximum
     *
     * @return [CB_ModelAssociation]
     */
    static function
    fetchModelAssociationsByFirstCBIDAndAssociationKey(
        string $firstCBID,
        string $associationKey,
        string $sortingOrder = 'ascending',
        int $maximumResultCount = 10,
        ?int $sortingValueMinimum = null,
        ?int $sortingValueMaximum = null,
        ?int $sortingValueDifferentiatorMinimum = null,
        ?int $sortingValueDifferentiatorMaximum = null
    ): array {

        if (
            $maximumResultCount < 0
        ) {
            $maximumResultCount = 10;
        }

        if (
            $sortingOrder === 'descending'
        ) {
            $sortingOrderAsSQL = 'DESC';
        } else {
            $sortingOrderAsSQL = 'ASC';
        }

        $whereClauses = [];

        array_push(
            $whereClauses,
            (
                'ID = ' .
                CBID::toSQL(
                    $firstCBID
                )
            )
        );

        array_push(
            $whereClauses,
            (
                'className = ' .
                CBDB::stringToSQL(
                    $associationKey
                )
            )
        );

        if (
            $sortingValueMinimum !== null &&
            $sortingValueDifferentiatorMinimum !== null
        ) {
            array_push(
                $whereClauses,
                <<<EOT

                    (
                        (
                            CBModelAssociations_sortingValue_2_column =
                            {$sortingValueMinimum} &&

                            CBModelAssociations_sortingValueDifferentiator_2_column >=
                            {$sortingValueDifferentiatorMinimum}
                        ) ||

                        CBModelAssociations_sortingValue_2_column >
                        {$sortingValueMinimum}
                    )

                EOT
            );
        } else if (
            $sortingValueMinimum !== null
        ) {
            array_push(
                $whereClauses,
                <<<EOT

                    CBModelAssociations_sortingValue_2_column >=
                    {$sortingValueMinimum}

                EOT
            );
        }

        if (
            $sortingValueMaximum !== null &&
            $sortingValueDifferentiatorMaximum !== null
        ) {
            array_push(
                $whereClauses,
                <<<EOT

                    (
                        (
                            CBModelAssociations_sortingValue_2_column =
                            {$sortingValueMaximum} &&

                            CBModelAssociations_sortingValueDifferentiator_2_column <=
                            {$sortingValueDifferentiatorMaximum}
                        ) ||

                        CBModelAssociations_sortingValue_2_column <
                        {$sortingValueMaximum}
                    )

                EOT
            );
        } else if (
            $sortingValueMaximum !== null
        ) {
            array_push(
                $whereClauses,
                (
                    'CBModelAssociations_sortingValue_2_column <= ' .
                    $sortingValueMaximum
                )
            );
        }



        $whereClauses = implode(
            ' AND ',
            $whereClauses
        );

        $SQL = <<<EOT

            SELECT

            CBModelAssociations_sortingValue_2_column
            AS
            sortingValue,

            CBModelAssociations_sortingValueDifferentiator_2_column
            AS
            sortingValueDifferentiator,

            LOWER(HEX(associatedID))
            AS
            secondCBID

            FROM
            CBModelAssociations

            WHERE
            {$whereClauses}

            ORDER BY

            CBModelAssociations_sortingValue_2_column
            {$sortingOrderAsSQL},

            CBModelAssociations_sortingValueDifferentiator_2_column
            {$sortingOrderAsSQL}

            LIMIT
            {$maximumResultCount}

        EOT;

        $results = CBDB::SQLToObjects(
            $SQL
        );

        $modelAssociations = array_map(
            function (
                $result
            ) use (
                $firstCBID,
                $associationKey
            ) {
                $modelAssociation = CBModel::createSpec(
                    'CB_ModelAssociation'
                );

                CB_ModelAssociation::setFirstCBID(
                    $modelAssociation,
                    $firstCBID
                );

                CB_ModelAssociation::setAssociationKey(
                    $modelAssociation,
                    $associationKey
                );

                CB_ModelAssociation::setSortingValue(
                    $modelAssociation,
                    $result->sortingValue
                );

                CB_ModelAssociation::setSortingValueDifferentiator(
                    $modelAssociation,
                    $result->sortingValueDifferentiator
                );

                CB_ModelAssociation::setSecondCBID(
                    $modelAssociation,
                    $result->secondCBID
                );

                return $modelAssociation;
            },
            $results
        );

        return $modelAssociations;
    }
    /* fetchModelAssociationsByFirstCBIDAndAssociationKey() */



    /**
     * @param [CB_ModelAssociation]
     *
     * @return [object]
     */
    static function
    fetchSecondModels(
        array $modelAssociations,
        bool $maintainPositions = false
    ): array {
        $secondCBIDs = array_map(
            function (
                stdClass $modelAssociation
            ) {
                $secondCBID = CB_ModelAssociation::getSecondCBID(
                    $modelAssociation
                );

                return $secondCBID;
            },
            $modelAssociations
        );

        return array_values(
            CBModels::fetchModelsByID2(
                $secondCBIDs,
                $maintainPositions
            )
        );
    }
    /* fetchSecondModels() */



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
     * Calling this function will ensure that the model associations exist and
     * that the sorting value is updated.
     *
     * @param CB_ModelAssociation | [CB_ModelAssociation] $modelAssociations
     *
     * @return void
     */
    static function
    insertOrUpdate(
        $modelAssociations
    ): void {
        if (
            !is_array($modelAssociations)
        ) {
            $modelAssociations = [$modelAssociations];
        }

        $values = array_map(
            function (
                stdClass $modelAssociation
            ) {
                return CBModelAssociations::modelAssociationToSQLValue(
                    $modelAssociation
                );
            },
            $modelAssociations
        );

        $values = implode(
            ',',
            $values
        );

        $SQL = <<<EOT

            INSERT INTO CBModelAssociations

            (
                ID,
                className,
                CBModelAssociations_sortingValue_2_column,
                CBModelAssociations_sortingValueDifferentiator_2_column,
                associatedID
            )

            VALUES
            {$values}

            ON DUPLICATE KEY UPDATE
            CBModelAssociations_sortingValue_2_column =
            CBModelAssociations_sortingValue_2_column,
            CBModelAssociations_sortingValueDifferentiator_2_column =
            CBModelAssociations_sortingValueDifferentiator_2_column

        EOT;

        Colby::query(
            $SQL
        );
    }
    /* insertOrUpdate() */



    /**
     * @return string
     *
     *      "(<firstCBID>, <associationKey>, <sortingValue>, <secondCBID>)"
     */
    private static function
    modelAssociationToSQLValue(
        stdClass $modelAssociation
    ): string {
        $firstCBID = CB_ModelAssociation::getFirstCBID(
            $modelAssociation
        );

        $associationKey = CB_ModelAssociation::getAssociationKey(
            $modelAssociation
        );

        $sortingValue = CB_ModelAssociation::getSortingValue(
            $modelAssociation
        );

        $sortingValueDifferentiator = (
            CB_ModelAssociation::getSortingValueDifferentiator(
                $modelAssociation
            )
        );

        $secondCBID = CB_ModelAssociation::getSecondCBID(
            $modelAssociation
        );

        return (
            '(' .
            CBID::toSQL($firstCBID) .
            ',' .
            CBDB::stringToSQL($associationKey) .
            ',' .
            $sortingValue .
            ',' .
            $sortingValueDifferentiator .
            ',' .
            CBID::toSQL($secondCBID) .
            ')'
        );
    }
    /* modelAssociationToSQLValue() */



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
