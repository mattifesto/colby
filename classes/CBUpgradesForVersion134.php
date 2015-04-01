<?php

/**
 * 2015.03.31
 * This upgrade adds the `iteration` column to the `CBPagesInTheTrash` table.
 */
class CBUpgradesForVersion134 {

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
                TABLE_NAME      = 'CBPagesInTheTrash' AND
                COLUMN_NAME     = 'iteration'

EOT;

        $result = Colby::query($SQL);

        $columnExists = $result->fetch_object()->columnExists;

        $result->free();

        if ($columnExists) {
            return;
        }

        /**
         * Add the `interation` column.
         */

        $SQL = <<<EOT

            ALTER TABLE
                `CBPagesInTheTrash`
            ADD COLUMN
                `iteration` BIGINT UNSIGNED NOT NULL DEFAULT 1
            AFTER
                `groupID`

EOT;

        Colby::query($SQL);
    }
}
