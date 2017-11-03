<?php

/**
 * 2017.10.27 Simplify the CBLog table and add group support.
 */
final class CBUpgradesForVersion351 {

    /**
     * @return null
     */
    static function run() {
        $SQL = <<<EOT

            SELECT  COUNT(*)
            FROM    information_schema.COLUMNS
            WHERE   TABLE_SCHEMA = DATABASE() AND
                    TABLE_NAME = 'CBLog' AND
                    COLUMN_NAME = 'serial'

EOT;

        if (CBDB::SQLToValue($SQL) == 0) {
            Colby::query('DROP TABLE IF EXISTS `CBLog`');
            CBLog::install();
        }

        $SQL = <<<EOT

            SELECT  COUNT(*)
            FROM    information_schema.COLUMNS
            WHERE   TABLE_SCHEMA = DATABASE() AND
                    TABLE_NAME = 'CBTasks2' AND
                    COLUMN_NAME = 'processID'

EOT;

        if (CBDB::SQLToValue($SQL) == 0) {

            $SQL = <<<EOT

                ALTER TABLE `CBTasks2`
                DROP INDEX `group_started_priority`,
                DROP INDEX `group_completed`,
                DROP COLUMN `IDForGroup`,
                ADD COLUMN `processID` BINARY(20) AFTER `ID`,
                ADD INDEX `processID_started_priority` (`processID`, `started`, `priority`),
                ADD INDEX `processID_completed` (`processID`, `completed`)

EOT;

            Colby::query($SQL);
        }
    }
}
