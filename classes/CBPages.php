<?php

/**
 * The CBPages class is a limited functionality class that provides methods to
 * work with the rows of the `ColbyPages` table. The class exists to encourage
 * good coding practices, help with rapid development, and provide the highest
 * performance when working with the `ColbyPages` table.
 */
class CBPages {

    /**
     * @return void
     */
    public static function deleteRowWithDataStoreID($dataStoreID)
    {
        $sql = self::sqlToDeleteRowWithDataStoreID($dataStoreID);

        Colby::query($sql);
    }

    /**
     * @return void
     */
    public static function deleteRowsWithDataStoreIDs($dataStoreIDs)
    {
        if (empty($dataStoreIDs))
        {
            return;
        }

        $sqls = array();

        foreach ($dataStoreIDs as $dataStoreID)
        {
            $sqls[] = self::sqlToDeleteRowWithDataStoreID($dataStoreID);
        }

        $sqls = implode(';', $sqls);

        Colby::queries($sqls);
    }

    /**
     * @return void
     */
    public static function deleteRowWithDataStoreIDFromTheTrash($dataStoreID)
    {
        $sql = self::sqlToDeleteRowWithDataStoreIDFromTheTrash($dataStoreID);

        Colby::query($sql);
    }

    /**
     * @param {hex160} $ID
     */
    public static function insertRow($ID) {
        $IDAsSQL    = CBHex160::toSQL($ID);
        $SQL        = <<<EOT

            INSERT INTO `ColbyPages`
            SET         `archiveID`     = {$IDAsSQL},
                        `keyValueData`  = '',
                        `titleHTML`     = '',
                        `subtitleHTML`  = '',
                        `searchText`    = '',
                        `URI`           = NULL

EOT;

        Colby::query($SQL);
    }

    /**
     * @return null
     */
    public static function install() {
        CBPagesPreferences::install();
    }

    /**
     * @return void
     */
    public static function moveRowWithDataStoreIDToTheTrash($dataStoreID)
    {
        $dataStoreIDForSQL = ColbyConvert::textToSQL($dataStoreID);

        $sql = <<<EOT

            INSERT INTO
                `CBPagesInTheTrash`
            (
                `ID`,
                `dataStoreID`,
                `keyValueData`,
                `className`,
                `classNameForKind`,
                `iteration`,
                `URI`,
                `titleHTML`,
                `subtitleHTML`,
                `thumbnailURL`,
                `searchText`,
                `published`,
                `publishedBy`,
                `publishedMonth`
            )
            SELECT
                *
            FROM
                `ColbyPages`
            WHERE
                `archiveID` = UNHEX('{$dataStoreIDForSQL}')

EOT;

        Colby::query($sql);

        self::deleteRowWithDataStoreID($dataStoreID);
    }

    /**
     * @return void
     */
    public static function recoverRowWithDataStoreIDFromTheTrash($dataStoreID)
    {
        $dataStoreIDForSQL = ColbyConvert::textToSQL($dataStoreID);

        $sql = <<<EOT

            INSERT INTO
                `ColbyPages`
            (
                `ID`,
                `archiveID`,
                `keyValueData`,
                `className`,
                `classNameForKind`,
                `iteration`,
                `URI`,
                `titleHTML`,
                `subtitleHTML`,
                `thumbnailURL`,
                `searchText`,
                `published`,
                `publishedBy`,
                `publishedMonth`
            )
            SELECT
                *
            FROM
                `CBPagesInTheTrash`
            WHERE
                `dataStoreID` = UNHEX('{$dataStoreIDForSQL}')

EOT;

        Colby::query($sql);

        self::deleteRowWithDataStoreIDFromTheTrash($dataStoreID);
    }

    /**
     * @return stdClass
     */
    public static function fetchIterationForUpdate($ID) {
        $IDAsSQL    = CBHex160::toSQL($ID);
        $SQL        = <<<EOT

            SELECT  `iteration`
            FROM    `ColbyPages`
            WHERE   `archiveID` = {$IDAsSQL}
            FOR UPDATE

EOT;

        return CBDB::SQLToValue($SQL);
    }

    /**
     * @return  {array}
     */
    public static function pageURLs() {
        $SQL = <<<EOT

            SELECT  `URI`
            FROM    `ColbyPages`
            WHERE   `published` IS NOT NULL

EOT;

        $URIs = CBDB::SQLToArray($SQL);

        return array_map(function($URI) {
            return CBSiteURL . "/{$URI}/";
        }, $URIs);
    }

    /**
     * @return void
     */
    public static function sqlToDeleteRowWithDataStoreID($dataStoreID)
    {
        $dataStoreIDForSQL = ColbyConvert::textToSQL($dataStoreID);

        $sql = <<<EOT

            DELETE FROM
                `ColbyPages`
            WHERE
                `archiveID` = UNHEX('{$dataStoreIDForSQL}')

EOT;

        return $sql;
    }

    /**
     * @return void
     */
    public static function sqlToDeleteRowWithDataStoreIDFromTheTrash($dataStoreID)
    {
        $dataStoreIDForSQL = ColbyConvert::textToSQL($dataStoreID);

        $sql = <<<EOT

            DELETE FROM
                `CBPagesInTheTrash`
            WHERE
                `dataStoreID` = UNHEX('{$dataStoreIDForSQL}')

EOT;

        return $sql;
    }

    /**
     * @return string
     */
    private static function sqlToUpdateRow($rowData)
    {
        $sql = array();

        $sql[] = 'UPDATE `ColbyPages` SET';

        $setters = array();

        foreach ($rowData as $columnName => $value) {
            if ('ID' == $columnName || 'rowID' == $columnName) {
                continue;
            }
            else if ('descriptionHTML' == $columnName)
            {
                /**
                 * This `subtitleHTML` column will be renamed to
                 * `descriptionHTML` in the future so `descriptionHTML` is
                 * allowed so that new code can use the non-deprecated column
                 * name.
                 */

                 $columnName = 'subtitleHTML';
            }

            $columnNameForSQL = ColbyConvert::textToSQL($columnName);

            if (null === $value) {
                $valueForSQL = 'NULL';
            } else if (is_int($value)) {
                $valueForSQL = $value;
            } else {
                $valueForSQL = CBDB::stringToSQL($value);
            }

            $setters[] = "`{$columnNameForSQL}` = {$valueForSQL}";
        }

        $sql[] = implode(',', $setters);

        if (isset($rowData->ID)) {
            $IDAsSQL    = CBHex160::toSQL($rowData->ID);
            $sql[]      = "WHERE `archiveID` = {$IDAsSQL}";
        } else {
            $rowID  = (int)$rowData->rowID;
            $sql[]  = "WHERE `ID` = {$rowID}";
        }

        $sql = implode(' ', $sql);

        return $sql;
    }

    /**
     * @param stdClass $rowData
     *
     *  The `$rowData` object must have the `rowID` property set and any other
     *  column values that need to be updated.
     *
     *  The `$rowData` object should use `null` property values to mean NULL in
     *  the SQL and strictly typed integer values for integers. Any other value
     *  type will be converted to a string and escaped for SQL.
     *
     * @return void
     */
    public static function updateRow($rowData)
    {
        $sql = self::sqlToUpdateRow($rowData);

        Colby::query($sql);
    }

    /**
     * @param array<stdClass> $rowData
     *
     *  See the `updateRow` method for details.
     *
     * @return void
     */
    public static function updateRows($rowData)
    {
    }
}
