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
    static function createPagesTable($args = []) {
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
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_520_ci

EOT;

        Colby::query($SQL);
    }

    /**
     * Deletes rows from the ColbyPages table.
     *
     * This function isn't meant to do any additional work. If you want to fully
     * delete a page call:
     *
     *      CBModels::deleteByID($pageID)
     *
     * @return null
     */
    static function deletePagesByID(array $IDs) {
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
     *      CBModels::deleteByID($pageID)
     *
     * @return null
     */
    static function deletePagesFromTrashByID(array $IDs) {
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
    static function fetchPageListForAjax() {
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
    static function fetchPageListForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @param [{hex160}] $IDs
     *
     * @return [{stdClass}]
     */
    static function fetchPageSummaryModelsByID($IDs) {
        if (empty($IDs)) { return []; }

        $IDsAsSQL = CBHex160::toSQL($IDs);
        $SQL = "SELECT `keyValueData` FROM `ColbyPages` WHERE `archiveID` IN ($IDsAsSQL)";

        return CBDB::SQLToArray($SQL, ['valueIsJSON' => true]);
    }

    /**
     * @return null
     */
    static function install() {
        CBPages::createPagesTable();
        CBPages::createPagesTable(['name' => 'CBPagesInTheTrash']);
        CBPagesPreferences::install();
    }

    /**
     * NOTE 2016.03.15 This function is somewhat messed up and it's very
     * important. It makes a ton of assumptions many of which may be wrong. It
     * needs to be reviewed, have its parameters well documented, and fixed to
     * either be private or work for any caller. This function is the gatekeeper
     * to the ColbyPages table and any flaws it has are therefore large flaws.
     *
     * NOTE The comments below may make incorrect assumptions:
     * To avoid duplicating property validation this function assumes the model
     * parameter has been generated with the `CBModel::toModel()` function or
     * another function that properly validates and sets the reserved page model
     * properties. It is a the responsibility of the caller to make sure this is
     * true.
     *
     * For instance, the $model->titleAsHTML value is assumed to have already
     * been escaped for use in HTML.
     *
     * @return string
     */
    static function modelToRowValues(stdClass $model) {
        $now = time();
        $IDAsSQL = CBHex160::toSQL(CBModel::value($model, 'ID'));
        $className = CBModel::value($model, 'className', '');
        $classNameAsSQL = CBDB::stringToSQL($className);
        $classNameForKindAsSQL = CBModel::value($model, 'classNameForKind', 'NULL', function ($value) {
            if (empty($value)) {
                return 'NULL'; /* falsy value (empty string) should be treated as null */
            } else {
                return CBDB::stringToSQL($value);
            }
        });
        $createdAsSQL = CBModel::value($model, 'created', $now, 'intval');
        $iterationAsSQL = 0; /* deprecated */
        $modifiedAsSQL = CBModel::value($model, 'modified', $now, 'intval');

        $isPublished = CBModel::value($model, 'isPublished', false, 'boolval');

        if ($isPublished) {
            $publishedAsSQL = CBModel::value($model, 'publicationTimeStamp', $now, 'intval');
            $publishedByAsSQL = CBModel::value($model, 'publishedBy', 'NULL', 'intval');
            $publishedMonthAsSQL = ColbyConvert::timestampToYearMonth($publishedAsSQL);
        } else {
            $publishedAsSQL = 'NULL';
            $publishedByAsSQL = 'NULL';
            $publishedMonthAsSQL = 'NULL';
        }

        $titleHTMLAsSQL = CBDB::stringToSQL(CBModel::value($model, 'title', '', 'cbhtml'));
        $subtitleHTMLAsSQL = CBDB::stringToSQL(CBModel::value($model, 'description', '', 'cbhtml'));

        $thumbnailURLAsSQL = CBDB::stringToSQL(CBModel::value($model, 'thumbnailURL', '', 'cbhtml'));
        $URIAsSQL = CBDB::stringToSQL(CBModel::value($model, 'URI', ''));

        $pageSummaryModel = CBPageSummaryView::viewPageModelToModel($model);
        $keyValueDataAsSQL = CBDB::stringToSQL(json_encode($pageSummaryModel));
        $searchTextAsSQL = CBDB::stringToSQL(CBModel::toSearchText($model));

        return "($IDAsSQL, $keyValueDataAsSQL, $classNameAsSQL, $classNameForKindAsSQL, $createdAsSQL, $iterationAsSQL, $modifiedAsSQL, $URIAsSQL, $titleHTMLAsSQL, $subtitleHTMLAsSQL, $thumbnailURLAsSQL, $searchTextAsSQL, $publishedAsSQL, $publishedByAsSQL, $publishedMonthAsSQL)";
    }

    /**
     * @return void
     */
    static function moveRowWithDataStoreIDToTheTrash($dataStoreID) {
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
    static function recoverRowWithDataStoreIDFromTheTrash($dataStoreID) {
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
     * @param hex160 $args->ID
     *
     * @return bool
     */
    static function CBAjax_moveToTrash(stdClass $args) {
        CBPages::moveRowWithDataStoreIDToTheTrash($args->ID);
    }

    /**
     * @return string
     */
    static function CBAjax_moveToTrash_group() {
        return 'Administrators';
    }


    /**
     * @param [{stdClass}] $models
     *
     * @return null
     */
    static function save(array $models) {
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
     * @return {string}
     *
     * "////Piñata///Örtega Smith//" --> "piata/rtega-smith"
     */
    static function stringToDencodedURIPath($string) {
        $stubs = CBRequest::decodedPathToDecodedStubs($string);
        $stubs = array_map('ColbyConvert::textToStub', $stubs);
        $stubs = array_filter($stubs, function($stub) { return !empty($stub); });
        return implode('/', $stubs);
    }
}
