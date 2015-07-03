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
                `typeID`,
                `groupID`,
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
                `typeID`,
                `groupID`,
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

            if (null === $value)
            {
                $valueForSQL = 'NULL';
            }
            else if ('typeID' == $columnName || 'groupID' == $columnName)
            {
                $valueForSQL = ColbyConvert::textToSQL($value);
                $valueForSQL = "UNHEX('{$valueForSQL}')";
            }
            else if (is_int($value))
            {
                $valueForSQL = $value;
            }
            else
            {
                $valueForSQL = ColbyConvert::textToSQL($value);
                $valueForSQL = "'{$valueForSQL}'";
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
     * @param [{hex160} : {string} | null] preferredURIs
     *
     * @return [{hex160} => {string} | null]
     *  Actual URIs
     */
    public static function updateURIs($args) {
        $preferredURIs = [];
        extract($args, EXTR_IF_EXISTS);

        if (empty($preferredURIs)) {
            return [];
        }

        // Set all of the URIs for unpublished pages to NULL. This frees up the
        // URIs they had been using for published pages.

        $unpublishedURIs = array_filter($preferredURIs, function($URI) {
            return $URI === null;
        });

        self::updateURIsForUnpublishedPages(array_keys($unpublishedURIs));

        $publishedURIs = array_filter($preferredURIs, function($URI) {
            return $URI !== null;
        });

        if (empty($publishedURIs)) {
            goto done;
        }

        // Replace duplicate URIs (first in array wins)

        $publishedURIs = cb_array_map_assoc(function($ID, $URI) use ($publishedURIs) {
            if ($ID == array_search($URI, $publishedURIs)) {
                return $URI;
            } else {
                return $ID;
            }
        }, $publishedURIs);

        // Replace URIs already in use

        $URIsAsSQL  = implode(',', array_map('CBDB::stringToSQL', array_values($publishedURIs)));
        $SQL        = <<<EOT

            SELECT  LOWER(HEX(`archiveID`)), `URI`
            FROM    `ColbyPages`
            WHERE   `URI` IN ($URIsAsSQL)

EOT;

        $existingURIs   = CBDB::SQLToArray($SQL);
        $publishedURIs  = cb_array_map_assoc(function($ID, $URI) use ($existingURIs) {
            $existingID = array_search($URI, $existingURIs);

            if ($ID === $existingID || false === $existingID) {
                return $URI;
            } else {
                return $ID;
            }
        }, $publishedURIs);

        self::updateURIsForPublishedPages($publishedURIs);

        done:

        return array_merge($unpublishedURIs, $publishedURIs);
    }

    /**
     * @return null
     */
    private static function updateURIsForPublishedPages($URIs) {
        if (empty($URIs)) {
            return;
        }

        /**
         * Shortcut for a single URI update
         */

        if (count($URIs) == 1) {
            reset($URIs);
            $IDAsSQL    = CBHex160::toSQL(key($URIs));
            $URIAsSQL   = CBDB::stringToSQL(current($URIs));
            Colby::query("UPDATE `ColbyPages` SET `URI` = {$URIAsSQL} WHERE `archiveID` = {$IDAsSQL}");
            return;
        }

        /**
         * Multiple URI updates
         */

        $SQL = <<<EOT

            CREATE TEMPORARY TABLE `CBPagesURIUpdates`
            (
                `ID`    BINARY(20),
                `URI`   VARCHAR(100)
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8
            COLLATE=utf8_unicode_ci

EOT;

        Colby::query($SQL);

        $values = cb_array_map_assoc(function($ID, $URI) {
            $IDAsSQL    = CBHex160::toSQL($ID);
            $URIAsSQL   = CBDB::stringToSQL($URI);
            return "({$IDAsSQL},{$URIAsSQL})";
        }, $URIs);
        $values = implode(',', $values);

        Colby::query("INSERT INTO `CBPagesURIUpdates` VALUES {$values}");

        $SQL = <<<EOT

            UPDATE  `ColbyPages` AS `p`
            JOIN    `CBPagesURIUpdates` AS `u` ON `p`.`archiveID` = `u`.`ID`
            SET     `p`.`URI` = `u`.`URI`
EOT;

        Colby::query($SQL);

        Colby::query('DROP TEMPORARY TABLE `CBPagesURIUpdates`');
    }

    /**
     * @return null
     */
    private static function updateURIsForUnpublishedPages($IDs) {
        if (empty($IDs)) {
            return;
        }

        $IDsAsSQL = CBHex160::toSQL($IDs);
        $SQL        = <<<EOT

            UPDATE  `ColbyPages`
            SET     `URI` = NULL
            WHERE   `archiveID` IN ($IDsAsSQL)

EOT;

        Colby::query($SQL);
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
