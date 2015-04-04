<?php

/**
 * 2015.04.04
 * This upgrade makes a number of important changes to the `ColbyPages` and
 * `CBPagesInTheTrash` tables.
 *
 * First it adds the `classNameForKind` column which holds a class name
 * indicating the kind of post this is. This will be something like
 * 'MDBlogPost', 'MDNewsItem', or 'MDProductPage'.
 *
 * Often page kinds will be sorted into months and to support that we have
 * added the `publishedMonth` column to replace the `publishedYearMonth`
 * column. It has a more appropriate type.
 *
 * The indexes related to `groupID` are being removed to simplify the table since
 * `groupID` is a deprecated column. This also allows us to remove the
 * `publishedYearMonth` column.
 */
class CBUpgradesForVersion136 {

    /**
     * @return void
     */
    public static function run() {
        $SQL = <<<EOT

            SELECT
                COUNT(*) as `columnExists`
            FROM
                information_schema.COLUMNS
            WHERE
                TABLE_SCHEMA    = DATABASE() AND
                TABLE_NAME      = 'ColbyPages' AND
                COLUMN_NAME     = 'classNameForKind'

EOT;

        $result = Colby::query($SQL);

        $columnExists = $result->fetch_object()->columnExists;

        $result->free();

        if ($columnExists) {
            return;
        }

        /**
         * Add `classNameForKind` column to `ColbyPages`. This column is used
         * to store the "kind" of the page such as "LEProductPage",
         * "MCBlogPost", or "MCNewsItem".
         */

        $SQL = <<<EOT

            ALTER TABLE
                `ColbyPages`
            ADD COLUMN
                `classNameForKind` VARCHAR(80)
            AFTER
                `className`

EOT;

        Colby::query($SQL);

        /**
         * Add `classNameForKind` column to `CBPagesInTheTrash`.
         */

        $SQL = <<<EOT

            ALTER TABLE
                `CBPagesInTheTrash`
            ADD COLUMN
                `classNameForKind` VARCHAR(80)
            AFTER
                `className`

EOT;

        Colby::query($SQL);

        /**
         * Add `publishedMonth` column to `ColbyPages`. This column replaces
         * the `publishedYearMonth` column.
         */

        $SQL = <<<EOT

            ALTER TABLE
                `ColbyPages`
            ADD COLUMN
                `publishedMonth` MEDIUMINT
            AFTER
                `publishedBy`

EOT;

        Colby::query($SQL);

        /**
         * Add `publishedMonth` column to `CBPagesInTheTrash`.
         */

        $SQL = <<<EOT

            ALTER TABLE
                `CBPagesInTheTrash`
            ADD COLUMN
                `publishedMonth` MEDIUMINT
            AFTER
                `publishedBy`

EOT;

        Colby::query($SQL);

        /**
         * Populate the `publishedMonth` column in `ColbyPages`.
         */

        $SQL = <<<EOT

            UPDATE
                `ColbyPages`
            SET
                `publishedMonth` = `publishedYearMonth`
            WHERE
                `publishedYearMonth` != ''

EOT;

        Colby::query($SQL);

        /**
         * Populate the `publishedMonth` column in `CBPagesInTheTrash`.
         */

        $SQL = <<<EOT

            UPDATE
                `CBPagesInTheTrash`
            SET
                `publishedMonth` = `publishedYearMonth`
            WHERE
                `publishedYearMonth` != ''

EOT;

        Colby::query($SQL);

        /**
         * Drop the `groupID_published` index because the `groupID` column has
         * been deprecated.
         */

        $SQL = <<<EOT

            ALTER TABLE
                `ColbyPages`
            DROP INDEX
                `groupID_published`

EOT;

        Colby::query($SQL);

        /**
         * Drop the `groupID_publishedYearMonth_published` index because the
         * `groupID` column has been deprecated.
         */

        $SQL = <<<EOT

            ALTER TABLE
                `ColbyPages`
            DROP INDEX
                `groupID_publishedYearMonth_published`

EOT;

        Colby::query($SQL);

        /**
         * Drop the `publishedYearMonth` from `ColbyPages` because it has been
         * replaced by `publishedMonth`.
         */

        $SQL = <<<EOT

            ALTER TABLE
                `ColbyPages`
            DROP
                `publishedYearMonth`

EOT;

        Colby::query($SQL);

        /**
         * Drop the `publishedYearMonth` from `CBPagesInTheTrash`.
         */

        $SQL = <<<EOT

            ALTER TABLE
                `CBPagesInTheTrash`
            DROP
                `publishedYearMonth`

EOT;

        Colby::query($SQL);

        /**
         * Add the `classNameForKind_publishedMonth_published` index to
         * `ColbyPages`.
         */

        $SQL = <<<EOT

            ALTER TABLE
                `ColbyPages`
            ADD
                KEY `classNameForKind_publishedMonth_published` (`classNameForKind`, `publishedMonth`, `published`)

EOT;

        Colby::query($SQL);
    }
}
