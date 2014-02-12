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
    public static function deleteRow($rowID)
    {
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
    public static function insertRow($dataStoreID)
    {
        $sql = self::sqlToInsertRow($dataStoreID);

        Colby::query($sql);

        $rowID = Colby::mysqli()->insert_id;

        $rowData        = new stdClass();
        $rowData->rowID = $rowID;

        return $rowData;
    }

    /**
     * @param array<string> $dataStoreIDs
     *
     * @return array<stdClass>
     */
    public static function insertRows($dataStoreIDs)
    {
    }

    /**
     * @return string
     */
    private static function sqlToInsertRow($dataStoreID)
    {
        $dataStoreIDForSQL = ColbyConvert::textToSQL($dataStoreID);

        $sql = <<<EOT

            INSERT INTO
                `ColbyPages`
            SET
                `archiveID`             = UNHEX('{$dataStoreIDForSQL}'),
                `keyValueData`          = '',
                `titleHTML`             = '',
                `subtitleHTML`          = '',
                `searchText`            = '',
                `publishedYearMonth`    = ''

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
     *  already used by another row.
     */
    public static function tryUpdateRowURI($rowID, $URI)
    {
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
