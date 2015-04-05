<?php

/**
 * The CBPages class is a limited functionality class that provides methods to
 * work with the rows of the `ColbyPages` table. The class exists to encourage
 * good coding practices, help with rapid development, and provide the highest
 * performance when working with the `ColbyPages` table.
 */
class CBPages
{
    /**
     * Disallow creating instances.
     */
    private function __construct()
    {
    }

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
     * @param string $dataStoreID
     *
     * @return stdClass
     *
     *  Returns an object with its `rowID` property set to the row ID of the
     *  new row. This object can be populated with values for other columns
     *  and passed into the `updateRow` method.
     */
    public static function insertRow($ID) {
        $ID         = (string)$ID;
        $IDAsSQL    = ColbyConvert::textToSQL($ID);
        $SQL        = <<<EOT

            INSERT INTO
                `ColbyPages`
            SET
                `archiveID`             = UNHEX('{$IDAsSQL}'),
                `keyValueData`          = '',
                `titleHTML`             = '',
                `subtitleHTML`          = '',
                `searchText`            = '',
                `URI`                   = '{$IDAsSQL}'

EOT;

        Colby::query($SQL);

        $rowData            = new stdClass();
        $rowData->iteration = 1;
        $rowData->rowID     = Colby::mysqli()->insert_id;
        $rowData->URI       = $ID;

        return $rowData;
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
    public static function selectIterationAndURIForUpdate($ID) {
        $IDAsSQL    = ColbyConvert::textToSQL($ID);
        $IDAsSQL    = "UNHEX('{$IDAsSQL}')";
        $SQL        = <<<EOT

            SELECT
                `iteration`,
                `URI`
            FROM
                `ColbyPages`
            WHERE
                `archiveID` = {$IDAsSQL}
            FOR UPDATE

EOT;

        $result = Colby::query($SQL);
        $data   = $result->fetch_object();

        $result->free();

        return $data;
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
        $rowID = (int)$rowData->rowID;

        $sql = array();

        $sql[] = 'UPDATE `ColbyPages` SET';

        $setters = array();

        foreach ($rowData as $columnName => $value)
        {
            if ('rowID' == $columnName)
            {
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

        $sql[] = "WHERE `ID` = {$rowID}";

        $sql = implode(' ', $sql);

        return $sql;
    }

    /**
     * @return bool
     *
     *  Returns true if the row's URI was updated and false of the URI is
     *  already used by another page.
     */
    public static function updateURI($ID, $URI)
    {
        $IDAsSQL    = ColbyConvert::textToSQL($ID);
        $IDAsSQL    = "UNHEX('{$IDAsSQL}')";
        $URIAsSQL   = ColbyConvert::textToSQL($URI);
        $SQL        = <<<EOT

            UPDATE
                `ColbyPages`
            SET
                `URI` = '{$URIAsSQL}'
            WHERE
                `archiveID` = {$IDAsSQL}

EOT;

        try
        {
            Colby::query($SQL);
        }
        catch (Exception $exception)
        {
            if (1062 == Colby::mysqli()->errno)
            {
                return false;
            }
            else
            {
                throw $exception;
            }
        }

        return true;
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
