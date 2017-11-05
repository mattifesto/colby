<?php

/**
 * 2017.10.27 Large changes and simplifications to both the CBLog and CBTasks2
 * tables. The method of upgrade is drastic, but the changes are so significant
 * that attemps to migrate the data would not be world the time.
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
                    COLUMN_NAME = 'state'

EOT;

        if (CBDB::SQLToValue($SQL) == 0) {
            Colby::query('DROP TABLE IF EXISTS `CBTasks2`');
            CBTasks2::install();
        }
    }
}
