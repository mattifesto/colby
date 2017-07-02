<?php

/**
 * This class is related to CBDB and contains less frequently used
 * administrative functions.
 */
final class CBDBA {

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
}
