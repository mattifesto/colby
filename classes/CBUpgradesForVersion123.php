<?php

/**
 * 2015.02.21
 * This upgrade adds the `iteration` column to the `ColbyPages` table.
 */
class CBUpgradesForVersion123 {

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
                COLUMN_NAME     = 'iteration'

EOT;

        $result = Colby::query($SQL);

        $columnExists = $result->fetch_object()->columnExists;

        $result->free();

        if ($columnExists)
        {
            return;
        }

        /**
         * Add the `interation` column.
         */

        $SQL = <<<EOT

        ALTER TABLE
            `ColbyPages`
        ADD COLUMN
            `iteration` BIGINT UNSIGNED NOT NULL DEFAULT 1
        AFTER
            `groupID`

EOT;

        Colby::query($SQL);
    }
}
