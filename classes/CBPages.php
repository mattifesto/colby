<?php

/**
 * The CBPages class is a limited functionality class that provides methods to
 * work with the rows of the `ColbyPages` table. The class exists to encourage
 * good coding practices, help with rapid development, and provide the highest
 * performance when working with the `ColbyPages` table.
 */
class CBPages {

    /**
     * @return null
     */
    public static function createPagesTable($args = []) {
        $name = 'ColbyPages'; $temporary = false;
        extract($args, EXTR_IF_EXISTS);

        if (preg_match('/[^a-zA-Z0-9]/', $name)) {
            throw new InvalidArgumentException('name');
        }

        $options = $temporary ? 'TEMPORARY' : '';
        $constraint = $temporary ? '' : ",CONSTRAINT `{$name}_publishedBy` FOREIGN KEY (`publishedBy`) REFERENCES `ColbyUsers` (`id`)";
        $SQL = <<<EOT

            CREATE {$options} TABLE IF NOT EXISTS `{$name}` (
                `ID`                    BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `archiveID`             BINARY(20) NOT NULL,
                `keyValueData`          LONGTEXT NOT NULL,
                `className`             VARCHAR(80),
                `classNameForKind`      VARCHAR(80),
                `iteration`             BIGINT UNSIGNED NOT NULL DEFAULT 1,
                `URI`                   VARCHAR(100),
                `titleHTML`             TEXT NOT NULL,
                `subtitleHTML`          TEXT NOT NULL,
                `thumbnailURL`          VARCHAR(200),
                `searchText`            LONGTEXT,
                `published`             BIGINT,
                `publishedBy`           BIGINT UNSIGNED,
                `publishedMonth`        MEDIUMINT,
                PRIMARY KEY     (`ID`),
                UNIQUE KEY      `archiveID` (`archiveID`),
                KEY             `URI_published` (`URI`, `published`),
                KEY             `classNameForKind_publishedMonth_published` (`classNameForKind`, `publishedMonth`, `published`)
                {$constraint}
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8
            COLLATE=utf8_unicode_ci

EOT;

        Colby::query($SQL);
    }

    /**
     * Deletes rows from the ColbyPages table. This function doesn't do any
     * additional work, such as deleting a data store directory.
     *
     * @return null
     */
    public static function deletePagesByID(array $IDs) {
        if (empty($IDs)) { return; }

        $IDsAsSQL = CBHex160::toSQL($IDs);

        Colby::query("DELETE FROM `ColbyPages` WHERE `archiveID` IN ({$IDsAsSQL})");
    }

    /**
     * @deprecated use deletePagesByID
     *
     * @return null
     */
    public static function deleteRowWithDataStoreID($dataStoreID) {
        CBPages::deletePagesByID([$dataStoreID]);
    }

    /**
     * @deprecated use deletePagesByID
     *
     * @return null
     */
    public static function deleteRowsWithDataStoreIDs($dataStoreIDs) {
        CBPages::deletePagesByID($dataStoreIDs);
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
     * @param [{hex160}] $IDs
     *
     * @return [{stdClass}]
     */
    public static function fetchPageSummaryModelsByID($IDs) {
        if (empty($IDs)) { return []; }

        $IDsAsSQL = CBHex160::toSQL($IDs);
        $SQL = "SELECT `keyValueData` FROM `ColbyPages` WHERE `archiveID` IN ($IDsAsSQL)";

        return CBDB::SQLToArray($SQL, ['valueIsJSON' => true]);
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
        CBPages::createPagesTable();
        CBPages::createPagesTable(['name' => 'CBPagesInTheTrash']);
        CBPagesPreferences::install();
    }

    /**
     * To avoid duplicating property validation this function assumes the model
     * parameter has been generated with the `CBPages::specToModel` function or
     * another function that properly validates and sets the reserved page model
     * properties. It is a the responsibility of the caller to make sure this is
     * true.
     *
     * For instance, the $model->titleAsHTML value is assumed to have already
     * been escaped for use in HTML.
     *
     * @return {string}
     */
    public static function modelToRowValues(stdClass $model) {
        $archiveID = CBHex160::toSQL($model->ID);
        $className = CBDB::stringToSQL($model->className);
        $classNameForKind = CBDB::stringToSQL($model->classNameForKind);
        $iteration = 1;
        $URI = CBDB::stringToSQL($model->dencodedURIPath);
        $titleHTML = CBDB::stringToSQL($model->titleAsHTML);
        $subtitleHTML = CBDB::stringToSQL($model->descriptionAsHTML);
        $thumbnailURL = CBDB::stringToSQL($model->encodedURLForThumbnail);
        $function = "{$model->className}::modelToSearchText";
        $searchText = is_callable($function) ? CBDB::stringToSQL(call_user_func($function, $model)) : "''";
        $published = isset($model->published) ? (int)$model->published : 'NULL';
        $publishedBy = 'NULL'; // Not sure if this will be used in the future
        $publishedMonth = isset($model->published) ? ColbyConvert::timestampToYearMonth($model->published) : 'NULL';

        if (is_callable($function = "{$model->className}::modelToPageSummaryModel")) {
            $pageSummaryModel = call_user_func($function, $model);
        } else {
            $pageSummaryModel = CBPageSummaryView::pageModelToModel($model);
        }

        $keyValueDataAsSQL = CBDB::stringToSQL(json_encode($pageSummaryModel));

        return "($archiveID, $keyValueDataAsSQL, $className, $classNameForKind, $iteration, $URI, $titleHTML, $subtitleHTML, $thumbnailURL, $searchText, $published, $publishedBy, $publishedMonth)";
    }

    /**
     * @return void
     */
    public static function moveRowWithDataStoreIDToTheTrash($dataStoreID) {
        $archiveIDForSQL = CBHex160::toSQL($dataStoreID);
        $sql = <<<EOT

            INSERT INTO `CBPagesInTheTrash` (
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
            SELECT  *
            FROM    `ColbyPages`
            WHERE   `archiveID` = {$archiveIDForSQL}

EOT;

        Colby::query($sql);

        self::deleteRowWithDataStoreID($dataStoreID);
    }

    /**
     * @return void
     */
    public static function recoverRowWithDataStoreIDFromTheTrash($dataStoreID) {
        $archiveIDForSQL = CBHex160::toSQL($dataStoreID);
        $SQL = <<<EOT

            INSERT INTO `ColbyPages` (
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
            SELECT  *
            FROM    `CBPagesInTheTrash`
            WHERE   `archiveID` = {$archiveIDForSQL}

EOT;

        Colby::query($SQL);

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
     * @param [{stdClass}] $models
     *
     * @return null
     */
    public static function save(array $models) {
        $values = array_map('CBPages::modelToRowValues', $models);
        $values = implode(',', $values);

        CBPages::createPagesTable([
            'name' => 'CBPagesTemporary',
            'temporary' => true
        ]);


        try {
            $SQL = <<<EOT

                INSERT INTO `CBPagesTemporary` (
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
                VALUES {$values}

EOT;

            Colby::query($SQL);

            $SQL = <<<EOT

                UPDATE  `ColbyPages`            AS `p`
                JOIN    `CBPagesTemporary`      AS `t` ON `p`.`archiveID` = `t`.`archiveID`
                SET     `p`.`keyValueData`      = `t`.`keyValueData`,
                        `p`.`className`         = `t`.`className`,
                        `p`.`classNameForKind`  = `t`.`classNameForKind`,
                        `p`.`iteration`         = `t`.`iteration`,
                        `p`.`URI`               = `t`.`URI`,
                        `p`.`titleHTML`         = `t`.`titleHTML`,
                        `p`.`subtitleHTML`      = `t`.`subtitleHTML`,
                        `p`.`thumbnailURL`      = `t`.`thumbnailURL`,
                        `p`.`searchText`        = `t`.`searchText`,
                        `p`.`published`         = `t`.`published`,
                        `p`.`publishedBy`       = `t`.`publishedBy`,
                        `p`.`publishedMonth`    = `t`.`publishedMonth`

EOT;

            Colby::query($SQL);

            $SQL = <<<EOT

            INSERT INTO `ColbyPages` (
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
                `t`.`archiveID`,
                `t`.`keyValueData`,
                `t`.`className`,
                `t`.`classNameForKind`,
                `t`.`iteration`,
                `t`.`URI`,
                `t`.`titleHTML`,
                `t`.`subtitleHTML`,
                `t`.`thumbnailURL`,
                `t`.`searchText`,
                `t`.`published`,
                `t`.`publishedBy`,
                `t`.`publishedMonth`
            FROM        `CBPagesTemporary`  AS `t`
            LEFT JOIN   `ColbyPages`        AS `p` ON `t`.`archiveID` = `p`.`archiveID`
            WHERE       `p`.`archiveID` IS NULL

EOT;

            Colby::query($SQL);
        } finally {
            Colby::query("DROP TEMPORARY TABLE `CBPagesTemporary`");
        }
    }

    /**
     * Page classes should call this function first from their own `specToModel`
     * function to process all of the page class reserved properties and ensure
     * that the model is properly set up to be saved.
     *
     * After that, the page class should continue processing its own custom
     * properties.
     *
     * This function defines the official properties of a page class spec and
     * model.
     *
     * @param {stdClass} $spec
     *
     * @return {stdClass}
     */
    public static function specToModel(stdClass $spec) {
        $model = CBModels::modelWithClassName($spec->className, ['ID' => $spec->ID]);
        $model->classNameForKind = ''; // Not sure if this will be used in the future
        $model->dencodedURIPath = isset($spec->URIPath) ? CBPages::stringToDencodedURIPath($spec->URIPath) : '';
        $model->dencodedURIPath = ($model->dencodedURIPath === '') ? $spec->ID : $model->dencodedURIPath;
        $model->description = isset($spec->description) ? trim($spec->description) : '';
        $model->descriptionAsHTML = ColbyConvert::textToHTML($model->description);
        $model->encodedURLForThumbnail = isset($spec->encodedURLForThumbnail) ? trim($spec->encodedURLForThumbnail) : '';
        $model->encodedURLForThumbnailAsHTML = ColbyConvert::textToHTML($model->encodedURLForThumbnail);
        $model->published = isset($spec->published) ? (int)$spec->published : null;
        $model->title = CBModels::specToTitle($spec);
        $model->titleAsHTML = ColbyConvert::textToHTML($model->title);

        return $model;
    }

    /**
     * @return void
     */
    public static function sqlToDeleteRowWithDataStoreIDFromTheTrash($dataStoreID) {
        $archiveIDForSQL = CBHex160::toSQL($dataStoreID);
        $SQL = <<<EOT

            DELETE FROM `CBPagesInTheTrash`
            WHERE       `archiveID` = {$archiveIDForSQL}

EOT;

        return $SQL;
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
     * @return {string}
     *
     * "////Piñata///Örtega Smith//" --> "piata/rtega-smith"
     */
    public static function stringToDencodedURIPath($string) {
        $stubs = CBRequest::decodedPathToDecodedStubs($string);
        $stubs = array_map('ColbyConvert::textToStub', $stubs);
        $stubs = array_filter($stubs, function($stub) { return !empty($stub); });
        return implode('/', $stubs);
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
