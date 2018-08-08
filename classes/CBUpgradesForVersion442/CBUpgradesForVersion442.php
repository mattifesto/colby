<?php

final class CBUpgradesForVersion442 {

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $SQL = <<<EOT

            SELECT  COUNT(*)
            FROM    information_schema.COLUMNS
            WHERE   TABLE_SCHEMA = DATABASE() AND
                    TABLE_NAME = 'CBModelVersions' AND
                    COLUMN_NAME = 'replaced'

EOT;

        if (CBConvert::valueAsInt(CBDB::SQLToValue($SQL)) === 0) {
            $SQL = <<<EOT

                ALTER TABLE CBModelVersions
                ADD COLUMN  replaced BIGINT
                AFTER       timestamp

EOT;

            Colby::query($SQL);
        }
    }

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return ['CBModelVersionsTable'];
    }
}
