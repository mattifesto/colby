<?php

/**
 * 2018.02.06
 *
 * Remove the titleHTML and subtitleHTML columns from the ColbyPages and
 * CBPagesInTheTrash tables. These columns are very old and in the wrong format
 * (would be better as text) and table (would be better in CBModels which
 * already has a title column).
 */
final class CBUpgradesForVersion384 {

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $SQL = <<<EOT

            SELECT  COUNT(*)
            FROM    information_schema.COLUMNS
            WHERE   TABLE_SCHEMA = DATABASE() AND
                    TABLE_NAME = 'ColbyPages' AND
                    COLUMN_NAME = 'titleHTML'

EOT;

        if (CBDB::SQLToValue($SQL) == 1) {
            $SQL = <<<EOT

                ALTER TABLE ColbyPages
                DROP COLUMN titleHTML,
                DROP COLUMN subtitleHTML

EOT;

            Colby::query($SQL);

            $SQL = <<<EOT

                ALTER TABLE CBPagesInTheTrash
                DROP COLUMN titleHTML,
                DROP COLUMN subtitleHTML

EOT;

            Colby::query($SQL);
        }
    }

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return ['CBUpgradesForVersion380'];
    }
}
