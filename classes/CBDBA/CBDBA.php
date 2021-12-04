<?php

/**
 * This class is related to CBDB and contains less frequently used
 * administrative functions.
 */
final class
CBDBA {

    /* -- functions -- -- -- -- -- */



    /**
     * @param string $tableName
     * @param string $columnName
     *
     * @return void
     */
    static function dropTableColumn(
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
    /* dropTableColumn() */



    /**
     * @param string $tableName
     * @param string $keyName
     *
     * @return void
     */
    static function dropTableKey(
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
    /* dropTableKey() */



    /**
     * @param string $tableName
     *
     * @return bool
     */
    static function
    tableDoesExist(
        string $tableName
    ): bool {
        $tableNameAsSQL = CBDB::stringToSQL(
            $tableName
        );

        $SQL = <<<EOT

            SELECT
            COUNT(*)

            FROM
            information_schema.tables

            WHERE
            table_schema = DATABASE()

            AND
            table_name = $tableNameAsSQL

        EOT;

        $count = CBDB::SQLToValue2(
            $SQL
        );

        return ($count === '1');
    }
    /* tableDoesExist() */



    /**
     * @param string $tableName
     * @param string $columnName
     *
     * @return bool
     */
    static function tableHasColumnNamed($tableName, $columnName) {
        $tableNameAsSQL = CBDB::stringToSQL($tableName);
        $columnNameAsSQL = CBDB::stringToSQL($columnName);

        $SQL = <<<EOT

            SELECT  COUNT(*)

            FROM    information_schema.COLUMNS

            WHERE   TABLE_SCHEMA = DATABASE() AND
                    TABLE_NAME = {$tableNameAsSQL} AND
                    COLUMN_NAME = {$columnNameAsSQL}

        EOT;

        return boolval(CBDB::SQLToValue($SQL));
    }
    /* tableHasColumnNamed() */

}
