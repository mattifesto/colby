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
                `created`               BIGINT NOT NULL,
                `iteration`             BIGINT UNSIGNED NOT NULL DEFAULT 1,
                `modified`              BIGINT NOT NULL,
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
                KEY             `classNameForKind_publishedMonth_published` (`classNameForKind`, `publishedMonth`, `published`),

                /* indexes used by the admin interface */
                KEY             `created` (`created`),
                KEY             `modified` (`modified`),
                KEY             `classNameForKind_created` (`classNameForKind`, `created`),
                KEY             `classNameForKind_modified` (`classNameForKind`, `modified`)

                {$constraint}
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8
            COLLATE=utf8_unicode_ci

EOT;

        Colby::query($SQL);
    }

    /**
     * Deletes rows from the ColbyPages table.
     *
     * This function isn't meant to do any additional work. If you want to fully
     * delete a page call:
     *
     *      CBModel::deleteModelsByID($pageID)
     *
     * @return null
     */
    public static function deletePagesByID(array $IDs) {
        if (empty($IDs)) { return; }

        $IDsAsSQL = CBHex160::toSQL($IDs);

        Colby::query("DELETE FROM `ColbyPages` WHERE `archiveID` IN ({$IDsAsSQL})");
    }

    /**
     * Deletes rows from the ColbyPagesIntheTrash table.
     *
     * This function isn't meant to do any additional work. If you want to fully
     * delete a page call:
     *
     *      CBModel::deleteModelsByID($pageID)
     *
     * @return null
     */
    public static function deletePagesFromTrashByID(array $IDs) {
        if (empty($IDs)) { return; }

        $IDsAsSQL = CBHex160::toSQL($IDs);

        Colby::query("DELETE FROM `CBPagesInTheTrash` WHERE `archiveID` IN ({$IDsAsSQL})");
    }

    /**
     * This is called by the "find pages" admin page.
     *
     * 2016.10.27 TODO
     * Should this be moved to the CBAdminPageForPagesFind class?
     *
     * @return null
     */
    public static function fetchPageListForAjax() {
        $response = new CBAjaxResponse();
        $parameters = json_decode($_POST['parametersAsJSON']);
        $conditions = [];

        /* classNameForKind (null means all, 'unspecified' means NULL) */
        if (isset($parameters->classNameForKind)) {
            if ($parameters->classNameForKind === 'unspecified') {
                $conditions[] = '`classNameForKind` IS NULL';
            } else if ($parameters->classNameForKind === 'currentFrontPage') {
                $frontPageID = CBSitePreferences::frontPageID();

                if (empty($frontPageID)) {
                    $conditions[] = 'FALSE'; /* return no results */
                } else {
                    $frontPageIDForSQL = CBHex160::toSQL($frontPageID);
                    $conditions[] = "`archiveID` = {$frontPageIDForSQL}";
                }
            } else {
                $classNameForKindAsSQL = CBDB::stringToSQL($parameters->classNameForKind);
                $conditions[] = "`classNameForKind` = {$classNameForKindAsSQL}";
            }
        }

        /* published */
        if (isset($parameters->published)) {
            if ($parameters->published === true) {
                $conditions[] = '`published` IS NOT NULL';
            } else if ($parameters->published === false) {
                $conditions[] = '`published` IS NULL';
            }
        }

        /* sorting */
        $sorting = CBModel::value($parameters, 'sorting');
        switch ($sorting) {
            case 'modifiedAscending':
                $order = '`modified` ASC';
                break;
            case 'createdDescending':
                $order = '`created` DESC';
                break;
            case 'createdAscending':
                $order = '`created` ASC';
                break;
            default:
                $order = '`modified` DESC';
                break;
        }

        /* search */
        $search = CBModel::value($parameters, 'search', '', 'trim');
        if ($clause = CBPages::searchClauseFromString($search)) {
            $conditions[] = $clause;
        };

        $conditions = implode(' AND ', $conditions);
        if ($conditions) { $conditions = "WHERE {$conditions}"; }

        $SQL = <<<EOT

            SELECT LOWER(HEX(`archiveID`)) AS `ID`, `className`, `keyValueData`
            FROM `ColbyPages`
            {$conditions}
            ORDER BY {$order}
            LIMIT 20

EOT;

        $response->pages = CBDB::SQLToObjects($SQL);
        $response->pages = array_map(function ($item) {
            if (empty($item->keyValueData)) {
                $item->keyValueData = (object)[
                    'ID' => $item->ID,
                    'title' => 'Page Needs to be Updated',
                ];
            } else {
                $item->keyValueData = json_decode($item->keyValueData);
            }
            if (empty($item->className)) {
                $item->className = 'CBViewPage';
            }
            return $item;
        }, $response->pages);
        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return {stdClass}
     */
    public static function fetchPageListForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
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
     * @return null
     */
    public static function install() {
        CBPages::createPagesTable();
        CBPages::createPagesTable(['name' => 'CBPagesInTheTrash']);
        CBPagesPreferences::install();
    }

    /**
     * @param hex160 $model->ID
     *
     * NOTE 2016.03.15 This function is somewhat messed up and it's very
     * important. It makes a ton of assumptions many of which may be wrong. It
     * needs to be reviewed, have its parameters well documented, and fixed to
     * either be private or work for any caller. This function is the gatekeeper
     * to the ColbyPages table and any flaws it has are therefore large flaws.
     *
     * NOTE The comments below may make incorrect assumptions:
     * To avoid duplicating property validation this function assumes the model
     * parameter has been generated with the `CBPages::specToModel` function or
     * another function that properly validates and sets the reserved page model
     * properties. It is a the responsibility of the caller to make sure this is
     * true.
     *
     * For instance, the $model->titleAsHTML value is assumed to have already
     * been escaped for use in HTML.
     *
     * 2016.02.11 CBViewPages are now saved as models, but their model is still
     * non-standard so there are some special cases in this function that should
     * eventually be removed once CBViewPage is changed to follow the standard
     * page model conventions.
     *
     * @return string
     */
    public static function modelToRowValues(stdClass $model) {
        $archiveID = CBHex160::toSQL($model->ID);
        $className = CBDB::valueToOptionalTrimmedSQL($model->className); // NOTE This should probably be required.
        $classNameForKind = CBDB::valueToOptionalTrimmedSQL($model->classNameForKind);
        $created = (int)$model->created;
        $iteration = 0;
        $modified = (int)$model->modified;

        if ($model->className === 'CBViewPage') {
            $published = $model->isPublished ? $model->publicationTimeStamp : 'NULL';
            $publishedBy = ($model->isPublished && isset($model->publishedBy)) ? (int)$model->publishedBy : 'NULL';
            $publishedMonth = $model->isPublished ? ColbyConvert::timestampToYearMonth($model->publicationTimeStamp) : 'NULL';
            $subtitleHTML = CBDB::stringToSQL($model->descriptionHTML);
            $thumbnailURL = CBDB::stringToSQL($model->thumbnailURLAsHTML);
            $titleHTML = CBDB::stringToSQL($model->titleHTML);
            $URI = CBDB::stringToSQL($model->URI);

            $pageSummaryModel = CBPageSummaryView::viewPageModelToModel($model);
        } else {
            $published = isset($model->published) ? (int)$model->published : 'NULL';
            $publishedBy = 'NULL'; // Not sure if this will be used in the future
            $publishedMonth = isset($model->published) ? ColbyConvert::timestampToYearMonth($model->published) : 'NULL';
            $subtitleHTML = CBDB::stringToSQL($model->descriptionAsHTML);
            $thumbnailURL = CBDB::stringToSQL($model->encodedURLForThumbnail);
            $titleHTML = CBDB::stringToSQL($model->titleAsHTML);
            $URI = CBDB::stringToSQL($model->dencodedURIPath);

            if (is_callable($function = "{$model->className}::modelToPageSummaryModel")) {
                $pageSummaryModel = call_user_func($function, $model);
            } else {
                $pageSummaryModel = CBPageSummaryView::pageModelToModel($model);
            }
        }

        $keyValueDataAsSQL = CBDB::stringToSQL(json_encode($pageSummaryModel));

        $function = "{$model->className}::modelToSearchText";
        $searchText = is_callable($function) ? CBDB::stringToSQL(call_user_func($function, $model)) : "''";

        return "($archiveID, $keyValueDataAsSQL, $className, $classNameForKind, $created, $iteration, $modified, $URI, $titleHTML, $subtitleHTML, $thumbnailURL, $searchText, $published, $publishedBy, $publishedMonth)";
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
                `created`,
                `iteration`,
                `modified`,
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

        CBPages::deletePagesByID([$dataStoreID]);
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
                `created`,
                `iteration`,
                `modified`,
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

        CBPages::deletePagesFromTrashByID([$dataStoreID]);
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
                    `created`,
                    `iteration`,
                    `modified`,
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
                        `p`.`created`           = `t`.`created`,
                        `p`.`iteration`         = `t`.`iteration`,
                        `p`.`modified`          = `t`.`modified`,
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
                `created`,
                `iteration`,
                `modified`,
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
                `t`.`created`,
                `t`.`iteration`,
                `t`.`modified`,
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
     * @param string $string
     *
     * @return string|null
     */
    private static function searchClauseFromString($string) {
        $words = preg_split('/[\s,]+/', $string, null, PREG_SPLIT_NO_EMPTY);
        $clauses = [];

        foreach ($words as $word) {
            if (strlen($word) > 2) {
                $wordForSQL = ColbyConvert::textToSQl($word);
                $clauses[] = "`searchText` LIKE '%{$wordForSQL}%'";
            }
        }

        if (empty($clauses)) {
            return null;
        } else {
            $clauses = implode(' AND ', $clauses);
            return "({$clauses})";
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
        $model->classNameForKind = CBModel::value($spec, 'classNameForKind', null, 'CBConvert::valueToOptionalTrimmedString');
        $model->dencodedURIPath = isset($spec->URIPath) ? CBPages::stringToDencodedURIPath($spec->URIPath) : '';
        $model->dencodedURIPath = ($model->dencodedURIPath === '') ? $spec->ID : $model->dencodedURIPath;
        $model->description = isset($spec->description) ? trim($spec->description) : '';
        $model->descriptionAsHTML = ColbyConvert::textToHTML($model->description);
        $model->encodedURLForThumbnail = isset($spec->encodedURLForThumbnail) ? trim($spec->encodedURLForThumbnail) : '';
        $model->encodedURLForThumbnailAsHTML = ColbyConvert::textToHTML($model->encodedURLForThumbnail);
        $model->hidden = isset($spec->hidden) && ($spec->hidden === true);
        $model->published = (isset($spec->published) && $model->hidden === false) ? (int)$spec->published : null;
        $model->title = CBModels::specToTitle($spec);
        $model->titleAsHTML = ColbyConvert::textToHTML($model->title);

        return $model;
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
}
