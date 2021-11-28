<?php

final class
CBModelAssociationsTable {

    /**
     * @TODO 2021_11_25
     *
     *      Future name changes:
     *
     *          ID --> CBModelAssociations_firstCBID_column
     *
     *          className --> CBModelAssociations_associationKey_column
     *
     *              The "className" column does not represent an actual class
     *              name but instead the context of the association for the row.
     *
     *          associatedID --> CBModelAssociations_secondCBID_column
     *
     * @NOTE 2021_11_25
     *
     *      The CBModelAssociations_sortingValue_column was added to enable this
     *      table to hold sorted lists of models, the first of which was a list
     *      of moments sorted by creation timestamp.
     *
     * @return void
     */
    static function
    CBInstall_install(
    ): void {
        $SQL = <<<EOT

            CREATE TABLE IF NOT EXISTS
            CBModelAssociations (

                ID
                    BINARY(20) NOT NULL,

                className
                    VARCHAR(80) NOT NULL,

                CBModelAssociations_sortingValue_column
                    BIGINT DEFAULT 0,

                associatedID
                    BINARY(20) NOT NULL,




                PRIMARY KEY (
                    ID,
                    className,
                    associatedID
                ),

                KEY CBModelAssociations_sortedListOfModels_key (
                    ID,
                    className,
                    CBModelAssociations_sortingValue_column
                ),

                KEY className_associatedID (
                    className,
                    associatedID
                ),

                KEY associatedID (
                    associatedID
                )
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_520_ci

        EOT;

        Colby::query(
            $SQL
        );

        /**
         * Upgrades added in version 675.45
         */

        if (
            !CBDBA::tableHasColumnNamed(
                'CBModelAssociations',
                'CBModelAssociations_sortingValue_column'
            )
        ) {
            $SQL = <<<EOT

                ALTER TABLE
                CBModelAssociations

                ADD COLUMN
                CBModelAssociations_sortingValue_column
                BIGINT DEFAULT 0
                AFTER
                className,

                ADD KEY
                CBModelAssociations_sortedListOfModels_key (
                    ID,
                    className,
                    CBModelAssociations_sortingValue_column
                )

            EOT;

            Colby::query(
                $SQL
            );
        }
    }
    /* CBInstall_install() */

}
