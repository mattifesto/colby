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
     * @NOTE 2021_11_25 updated 2021_12_31
     *
     *      The columns named CBModelAssociations_sortingValue_2_column and
     *      CBModelAssociations_sortingValueDifferentiator_2_column have been
     *      added to enable this table to hold sorted lists of models, the first
     *      of which was a list of moments sorted the moment time.
     *
     *      These two columns are designed to be able to hold with unique
     *      timestamps:
     *
     *      The CBModelAssociations_sortingValue_2_column can hold the Unix
     *      timestamp portion of a unique timestamp.
     *
     *      The CBModelAssociations_sortingValueDifferentiator_2_column can hold
     *      the fractional sub-second portion of a unique timestamp.
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

                CBModelAssociations_sortingValue_2_column
                BIGINT NOT NULL DEFAULT 0,

                CBModelAssociations_sortingValueDifferentiator_2_column
                BIGINT NOT NULL DEFAULT 0,

                associatedID
                BINARY(20) NOT NULL,




                PRIMARY KEY (
                    ID,
                    className,
                    associatedID
                ),

                INDEX
                CBModelAssociations_sortedListOfModels_2_index (
                    ID,
                    className,
                    CBModelAssociations_sortingValue_2_column,
                    CBModelAssociations_sortingValueDifferentiator_2_column
                ),

                INDEX
                className_associatedID (
                    className,
                    associatedID
                ),

                INDEX
                associatedID (
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
         * Version 675.48
         *
         * The index named CBModelAssociations_sortedListOfModels_key was
         * originally added with one definition and later that definition was
         * modified and the index was renamed to
         * CBModelAssociations_sortedListOfModels_2_index. If an index with the
         * orignal name exists it should be removed.
         */
        if (
            CBDBA::tableHasIndexNamed(
                'CBModelAssociations',
                'CBModelAssociations_sortedListOfModels_key'
            )
        ) {
            $SQL = <<<EOT

                ALTER TABLE
                CBModelAssociations

                DROP INDEX
                CBModelAssociations_sortedListOfModels_key

            EOT;

            Colby::query(
                $SQL
            );
        }



        /**
         * Version 675.48
         *
         * The index named CBModelAssociations_sortingValueDifferentiator_key
         * was originally added but its logic was flawed and if it exists it
         * needs to be removed.
         */
        if (
            CBDBA::tableHasIndexNamed(
                'CBModelAssociations',
                'CBModelAssociations_sortingValueDifferentiator_key'
            )
        ) {
            $SQL = <<<EOT

                ALTER TABLE
                CBModelAssociations

                DROP INDEX
                CBModelAssociations_sortingValueDifferentiator_key

            EOT;

            Colby::query(
                $SQL
            );
        }



        /**
         * Version 675.48
         *
         * The column named CBModelAssociations_sortingValue_column was added
         * but its definition has changed and it has been renamed to
         * CBModelAssociations_sortingValue_2_column.
         */
        if (
            CBDBA::tableHasColumnNamed(
                'CBModelAssociations',
                'CBModelAssociations_sortingValue_column'
            )
        ) {
            $SQL = <<<EOT

                ALTER TABLE
                CBModelAssociations

                CHANGE COLUMN
                CBModelAssociations_sortingValue_column
                CBModelAssociations_sortingValue_2_column
                BIGINT NOT NULL DEFAULT 0

            EOT;

            Colby::query(
                $SQL
            );
        }



        /**
         * Version 675.48
         *
         * If the column named CBModelAssociations_sortingValue_2_column doesn't
         * exist then create it.
         */
        if (
            !CBDBA::tableHasColumnNamed(
                'CBModelAssociations',
                'CBModelAssociations_sortingValue_2_column'
            )
        ) {
            $SQL = <<<EOT

                ALTER TABLE
                CBModelAssociations

                ADD COLUMN
                CBModelAssociations_sortingValue_2_column
                BIGINT NOT NULL DEFAULT 0
                AFTER className

            EOT;

            Colby::query(
                $SQL
            );
        }



        /**
         * Version 675.48
         *
         * The column named
         * CBModelAssociations_sortingValueDifferentiator_column was added but
         * its definition has changed and it has been renamed to
         * CBModelAssociations_sortingValueDifferentiator_2_column.
         */
        if (
            CBDBA::tableHasColumnNamed(
                'CBModelAssociations',
                'CBModelAssociations_sortingValueDifferentiator_column'
            )
        ) {
            $SQL = <<<EOT

                ALTER TABLE
                CBModelAssociations

                CHANGE COLUMN
                CBModelAssociations_sortingValueDifferentiator_column
                CBModelAssociations_sortingValueDifferentiator_2_column
                BIGINT NOT NULL DEFAULT 0

            EOT;

            Colby::query(
                $SQL
            );
        }




        /**
         * Version 675.48
         *
         * If the column named CBModelAssociations_sortingValue_2_column doesn't
         * exist then create it.
         */
        if (
            !CBDBA::tableHasColumnNamed(
                'CBModelAssociations',
                'CBModelAssociations_sortingValueDifferentiator_2_column'
            )
        ) {
            $SQL = <<<EOT

                ALTER TABLE
                CBModelAssociations

                ADD COLUMN
                CBModelAssociations_sortingValueDifferentiator_2_column
                BIGINT NOT NULL DEFAULT 0
                AFTER CBModelAssociations_sortingValue_column

            EOT;

            Colby::query(
                $SQL
            );
        }



        /**
         * Version 675.48
         */
        if (
            !CBDBA::tableHasIndexNamed(
                'CBModelAssociations',
                'CBModelAssociations_sortedListOfModels_2_index'
            )
        ) {
            $SQL = <<<EOT

                ALTER TABLE
                CBModelAssociations

                ADD INDEX
                CBModelAssociations_sortedListOfModels_2_index (
                    ID,
                    className,
                    CBModelAssociations_sortingValue_2_column,
                    CBModelAssociations_sortingValueDifferentiator_2_column
                )

            EOT;

            Colby::query(
                $SQL
            );
        }

    }
    /* CBInstall_install() */

}
