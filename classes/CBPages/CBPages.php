<?php

/**
 * The CBPages class is a limited functionality class that provides methods to
 * work with the rows of the `ColbyPages` table. The class exists to encourage
 * good coding practices, help with rapid development, and provide the highest
 * performance when working with the `ColbyPages` table.
 */
class CBPages {

    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        CBPages::createPagesTable();

        CBPages::createPagesTable(
            [
                'name' => 'CBPagesInTheTrash',
            ]
        );
    }



    /* -- functions -- -- -- -- -- */



    /**
     * @return void
     */
    static function createPagesTable(
        $args = []
    ): void {
        $name = 'ColbyPages'; $temporary = false;
        extract($args, EXTR_IF_EXISTS);

        if (preg_match('/[^a-zA-Z0-9]/', $name)) {
            throw new InvalidArgumentException('name');
        }

        $options = $temporary ? 'TEMPORARY' : '';

        $SQL = <<<EOT

            CREATE {$options} TABLE IF NOT EXISTS `{$name}` (
                `archiveID`             BINARY(20) NOT NULL,
                `keyValueData`          LONGTEXT NOT NULL,
                `className`             VARCHAR(80),
                `classNameForKind`      VARCHAR(80),
                `created`               BIGINT NOT NULL,
                `iteration`             BIGINT UNSIGNED NOT NULL DEFAULT 1,
                `modified`              BIGINT NOT NULL,
                `URI`                   VARCHAR(100),
                `thumbnailURL`          VARCHAR(200),
                `searchText`            LONGTEXT,
                `published`             BIGINT,
                `publishedMonth`        MEDIUMINT,

                PRIMARY KEY     (archiveID),

                KEY             `URI_published` (
                                    `URI`,
                                    `published`
                                ),

                KEY             `classNameForKind_publishedMonth_published` (
                                    `classNameForKind`,
                                    `publishedMonth`,
                                    `published`
                                ),

                /* indexes used by the admin interface */

                KEY             `created` (
                                    `created`
                                ),

                KEY             `modified` (
                                    `modified`
                                ),

                KEY             `classNameForKind_created` (
                                    `classNameForKind`,
                                    `created`
                                ),

                KEY             `classNameForKind_modified` (
                                    `classNameForKind`,
                                    `modified`
                                )

            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_520_ci

        EOT;

        Colby::query($SQL);
    }
    /* createPagesTable() */



    /**
     * Deletes rows from the ColbyPages table.
     *
     * This function isn't meant to do any additional work. If you want to fully
     * delete a page call:
     *
     *      CBModels::deleteByID($pageID)
     *
     * @return void
     */
    static function deletePagesByID(array $IDs): void {
        if (empty($IDs)) {
            return;
        }

        $IDsAsSQL = CBID::toSQL($IDs);

        Colby::query(
            "DELETE FROM `ColbyPages` WHERE `archiveID` IN ({$IDsAsSQL})"
        );
    }



    /**
     * Deletes rows from the ColbyPagesIntheTrash table.
     *
     * This function isn't meant to do any additional work. If you want to fully
     * delete a page call:
     *
     *      CBModels::deleteByID($pageID)
     *
     * @return void
     */
    static function deletePagesFromTrashByID(array $IDs): void {
        if (empty($IDs)) {
            return;
        }

        $IDsAsSQL = CBID::toSQL($IDs);

        Colby::query(
            "DELETE FROM `CBPagesInTheTrash` " .
            "WHERE `archiveID` IN ({$IDsAsSQL})"
        );
    }
    /* deletePagesFromTrashByID() */



    /**
     * @param [string] $IDs
     *
     * @return [object]
     */
    static function fetchPageSummaryModelsByID($IDs): array {
        if (empty($IDs)) {
            return [];
        }

        $IDsAsSQL = CBID::toSQL($IDs);

        $SQL = (
            "SELECT `keyValueData` FROM `ColbyPages` " .
            "WHERE `archiveID` IN ($IDsAsSQL)"
        );

        return CBDB::SQLToArray(
            $SQL,
            [
                'valueIsJSON' => true,
            ]
        );
    }



    /**
     * Sometimes pages are considered "well-known" or "special" because they
     * have a specific URI. Use this function to find the published pages
     * associated with a specific URI.
     *
     * @param string $URI
     *
     * @return [ID]
     *
     *      The returned array may be empty if no page is found or have more
     *      than one ID if multiple pages are found.
     */
    static function fetchPublishedPageIDsByURI(string $URI): array {
        $URIAsSQL = CBDB::stringToSQL($URI);

        $SQL = <<<EOT

            SELECT  LOWER(HEX(archiveID))
            FROM    ColbyPages
            WHERE   URI = {$URIAsSQL} AND
                    published IS NOT NULL

EOT;

        return CBDB::SQLToArray($SQL);
    }



    /**
     * @NOTE 2016_03_15
     *
     *      This function is somewhat messed up and it's very important. It
     *      makes a ton of assumptions many of which may be wrong. It needs to
     *      be reviewed, have its parameters well documented, and fixed to
     *      either be private or work for any caller. This function is the
     *      gatekeeper to the ColbyPages table and any flaws it has are
     *      therefore large flaws.
     *
     * @NOTE The comments below may make incorrect assumptions:
     *
     *      To avoid duplicating property validation this function assumes the
     *      model parameter has been generated with the CBModel::build()
     *      function or another function that properly validates and sets the
     *      reserved page model properties. It is a the responsibility of the
     *      caller to make sure this is true.
     *
     *      For instance, the $model->titleAsHTML value is assumed to have
     *      already been escaped for use in HTML.
     *
     * @return string
     */
    static function modelToRowValues(stdClass $model): string {
        $now = time();
        $IDAsSQL = CBID::toSQL(CBModel::value($model, 'ID'));
        $className = CBModel::value($model, 'className', '');
        $classNameAsSQL = CBDB::stringToSQL($className);

        $classNameForKindAsSQL = CBModel::value(
            $model,
            'classNameForKind',
            'NULL',
            function ($value) {
                if (empty($value)) {
                    /* falsy value (empty string) should be treated as null */
                    return 'NULL';
                } else {
                    return CBDB::stringToSQL($value);
                }
            }
        );

        $createdAsSQL = CBModel::value($model, 'created', $now, 'intval');
        $iterationAsSQL = 0; /* deprecated */
        $modifiedAsSQL = CBModel::value($model, 'modified', $now, 'intval');

        $isPublished = CBModel::value($model, 'isPublished', false, 'boolval');

        if ($isPublished) {
            $publishedAsSQL = CBModel::value(
                $model,
                'publicationTimeStamp',
                $now,
                'intval'
            );

            $publishedMonthAsSQL = ColbyConvert::timestampToYearMonth(
                $publishedAsSQL
            );
        } else {
            $publishedAsSQL = 'NULL';
            $publishedMonthAsSQL = 'NULL';
        }

        $thumbnailURLAsSQL = CBDB::stringToSQL(
            CBModel::value($model, 'thumbnailURL', '', 'cbhtml')
        );

        $URIAsSQL = CBDB::stringToSQL(
            CBModel::value($model, 'URI', '')
        );

        $keyValueDataAsSQL = CBDB::stringToSQL(
            json_encode(
                CBPage::toSummary($model)
            )
        );

        $searchTextAsSQL = CBDB::stringToSQL(
            CBModel::toSearchText($model)
        );

        return (
            "(" .
            "$IDAsSQL, " .
            "$keyValueDataAsSQL, " .
            "$classNameAsSQL, " .
            "$classNameForKindAsSQL, " .
            "$createdAsSQL, " .
            "$iterationAsSQL, " .
            "$modifiedAsSQL, " .
            "$URIAsSQL, " .
            "$thumbnailURLAsSQL, " .
            "$searchTextAsSQL, " .
            "$publishedAsSQL, " .
            "$publishedMonthAsSQL" .
            ")"
        );
    }
    /* modelToRowValues() */



    /**
     * @return void
     */
    static function moveRowWithDataStoreIDToTheTrash($dataStoreID): void {
        $archiveIDForSQL = CBID::toSQL($dataStoreID);

        $sql = <<<EOT

            INSERT INTO `CBPagesInTheTrash` (
                `archiveID`,
                `keyValueData`,
                `className`,
                `classNameForKind`,
                `created`,
                `iteration`,
                `modified`,
                `URI`,
                `thumbnailURL`,
                `searchText`,
                `published`,
                `publishedMonth`
            )
            SELECT  *
            FROM    `ColbyPages`
            WHERE   `archiveID` = {$archiveIDForSQL}

EOT;

        Colby::query($sql);

        CBPages::deletePagesByID([$dataStoreID]);
    }
    /* moveRowWithDataStoreIDToTheTrash() */



    /**
     * @return void
     */
    static function recoverRowWithDataStoreIDFromTheTrash($dataStoreID): void {
        $archiveIDForSQL = CBID::toSQL($dataStoreID);

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
                `thumbnailURL`,
                `searchText`,
                `published`,
                `publishedMonth`
            )
            SELECT  *
            FROM    `CBPagesInTheTrash`
            WHERE   `archiveID` = {$archiveIDForSQL}

        EOT;

        Colby::query($SQL);

        CBPages::deletePagesFromTrashByID([$dataStoreID]);
    }
    /* recoverRowWithDataStoreIDFromTheTrash() */



    /**
     * @param hex160 $args->ID
     *
     * @return void
     */
    static function CBAjax_moveToTrash(stdClass $args): void {
        CBPages::moveRowWithDataStoreIDToTheTrash($args->ID);
    }



    /**
     * @return string
     */
    static function CBAjax_moveToTrash_group(): string {
        return 'Administrators';
    }



    /**
     * @param [object] $models
     *
     * @return void
     */
    static function save(array $models): void {
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
                    `thumbnailURL`,
                    `searchText`,
                    `published`,
                    `publishedMonth`
                )
                VALUES {$values}

            EOT;

            Colby::query($SQL);

            $SQL = <<<EOT

                UPDATE  `ColbyPages`            AS `p`

                JOIN    `CBPagesTemporary`      AS `t`
                            ON `p`.`archiveID`  = `t`.`archiveID`

                SET     `p`.`keyValueData`      = `t`.`keyValueData`,
                        `p`.`className`         = `t`.`className`,
                        `p`.`classNameForKind`  = `t`.`classNameForKind`,
                        `p`.`created`           = `t`.`created`,
                        `p`.`iteration`         = `t`.`iteration`,
                        `p`.`modified`          = `t`.`modified`,
                        `p`.`URI`               = `t`.`URI`,
                        `p`.`thumbnailURL`      = `t`.`thumbnailURL`,
                        `p`.`searchText`        = `t`.`searchText`,
                        `p`.`published`         = `t`.`published`,
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
                    `thumbnailURL`,
                    `searchText`,
                    `published`,
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
                    `t`.`thumbnailURL`,
                    `t`.`searchText`,
                    `t`.`published`,
                    `t`.`publishedMonth`

                FROM        `CBPagesTemporary`  AS `t`

                LEFT JOIN   `ColbyPages`        AS `p`
                                ON `t`.`archiveID` = `p`.`archiveID`

                WHERE       `p`.`archiveID` IS NULL

            EOT;

            Colby::query($SQL);
        } finally {
            Colby::query(
                "DROP TEMPORARY TABLE `CBPagesTemporary`"
            );
        }
    }
    /* save() */



    /**
     * @param string $string
     *
     * @return ?string
     */
    static function searchClauseFromString($string): ?string {
        $words = preg_split(
            '/[\s,]+/',
            $string,
            null,
            PREG_SPLIT_NO_EMPTY
        );

        $clauses = [];

        foreach ($words as $word) {
            if (strlen($word) > 2) {
                $wordAsSQL = CBDB::escapeString($word);
                $clauses[] = "`searchText` LIKE '%{$wordAsSQL}%'";
            }
        }

        if (empty($clauses)) {
            return null;
        } else {
            $clauses = implode(' AND ', $clauses);
            return "({$clauses})";
        }
    }
    /* searchClauseFromString() */



    /**
     * @return string
     *
     * "////Piñata///Örtega Smith//" --> "piata/rtega-smith"
     */
    static function stringToDencodedURIPath($string): string {
        $stubs = CBRequest::decodedPathToDecodedStubs($string);

        $stubs = array_map(
            function ($value) {
                return CBConvert::stringToStub($value);
            },
            $stubs
        );

        $stubs = array_filter(
            $stubs,
            function ($stub) {
                return !empty($stub);
            }
        );

        return implode('/', $stubs);
    }
    /* stringToDencodedURIPath() */

}
