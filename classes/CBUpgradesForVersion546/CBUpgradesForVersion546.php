<?php

final class CBUpgradesForVersion546 {

    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        CBUpgradesForVersion546::dropTableKey(
            'ColbyUsers',
            'hasBeenVerified_facebookLastName'
        );

        CBUpgradesForVersion546::dropTableColumn(
            'ColbyUsers',
            'facebookAccessToken'
        );

        CBUpgradesForVersion546::dropTableColumn(
            'ColbyUsers',
            'facebookAccessExpirationTime'
        );

        CBUpgradesForVersion546::dropTableColumn(
            'ColbyUsers',
            'facebookFirstName'
        );

        CBUpgradesForVersion546::dropTableColumn(
            'ColbyUsers',
            'facebookLastName'
        );

        CBUpgradesForVersion546::dropTableColumn(
            'ColbyUsers',
            'facebookTimeZone'
        );

        CBUpgradesForVersion546::dropTableColumn(
            'ColbyUsers',
            'hasBeenVerified'
        );
    }
    /* CBInstall_install() */



    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBUsers',
        ];
    }
    /* CBInstall_requiredClassNames() */



    /* -- functions -- -- -- -- -- */

    private static function dropTableColumn(
        string $tableName,
        string $columnName
    ): void {
        $tableNameAsSQL = CBDB::escapeString($tableName);
        $columnNameAsSQL = CBDB::escapeString($columnName);

        $SQL = <<<EOT

            SELECT  COUNT(*)

            FROM    information_schema.COLUMNS

            WHERE   TABLE_SCHEMA = DATABASE() AND
                    TABLE_NAME = '{$tableNameAsSQL}' AND
                    COLUMN_NAME = '{$columnNameAsSQL}'

        EOT;

        $columnExists = CBConvert::valueAsInt(
            CBDB::SQLToValue($SQL)
        );

        if ($columnExists) {
            $SQL = <<<EOT

                ALTER TABLE {$tableNameAsSQL}

                DROP COLUMN {$columnNameAsSQL}

            EOT;

            Colby::query($SQL);
        }
    }
    /* removeTableColumn() */



    private static function dropTableKey(
        string $tableName,
        string $keyName
    ): void {
        $tableNameAsSQL = CBDB::escapeString($tableName);
        $keyNameAsSQL = CBDB::escapeString($keyName);

        $SQL = <<<EOT

            SELECT  COUNT(*)

            FROM    information_schema.STATISTICS

            WHERE   TABLE_SCHEMA = DATABASE() AND
                    TABLE_NAME = '{$tableNameAsSQL}' AND
                    INDEX_NAME = '{$keyNameAsSQL}'

        EOT;

        $keyExists = CBConvert::valueAsInt(
            CBDB::SQLToValue($SQL)
        );

        if ($keyExists) {
            $SQL = <<<EOT

                ALTER TABLE {$tableNameAsSQL}

                DROP KEY {$keyNameAsSQL}

            EOT;

            Colby::query($SQL);
        }
    }
    /* removeTableColumn() */

}
