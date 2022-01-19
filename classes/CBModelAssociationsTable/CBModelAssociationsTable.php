<?php

final class
CBModelAssociationsTable {

    /**
     * CBModelAssociations_sortingValue_2_column
     * CBModelAssociations_sortingValueDifferentiator_2_column
     *
     *      These two columns hold the sorting values for a list. It is best if
     *      there are no duplicate sorting values for items in a list, but this
     *      table doesn't enforce that.
     *
     *      These columns will hold the Unix timestamp and the femtoseconds of
     *      CB_Timestamp models, which are designed to be unique.
     *
     *      It is up to developers to make sure they use unique sorting values
     *      for items in a list. Be mindful of the fact that using unique
     *      sorting values is important, especially for long lists.
     *
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
                BIGINT UNSIGNED NOT NULL DEFAULT 0,

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
         * Version 675.52.1
         *
         * If the column CBModelAssociations_sortingValueDifferentiator_column
         * exists we need to remove the AUTOINCREMENT attribute first so that
         * the unique index associated with it can be deleted.
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
                CBModelAssociations_sortingValueDifferentiator_column
                BIGINT UNSIGNED NOT NULL DEFAULT 0

            EOT;

            Colby::query(
                $SQL
            );
        }



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
                BIGINT UNSIGNED NOT NULL DEFAULT 0

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
                BIGINT UNSIGNED NOT NULL DEFAULT 0
                AFTER CBModelAssociations_sortingValue_2_column

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
