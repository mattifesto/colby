<?php

final class CBAdminPageForPagesFind {

    /* -- CBAdmin interfaces -- -- -- -- -- */



    /**
     * @return string
     */
    static function CBAdmin_getUserGroupClassName(): string {
        return 'CBAdministratorsUserGroup';
    }



    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return [
            'pages',
            'find',
        ];
    }



    /**
     * @return null
     */
    static function CBAdmin_render() {
        CBHTMLOutput::pageInformation()->title = 'Pages Administration: Find';
    }



    /* -- CBAjax interfaces -- -- -- -- -- */



    /**
     * @param object args
     *
     * @return array
     */
    static function CBAjax_fetchPages(stdClass $args): array {
        $parameters = $args;
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
                    $frontPageIDForSQL = CBID::toSQL($frontPageID);
                    $conditions[] = "`archiveID` = {$frontPageIDForSQL}";
                }
            } else {
                $classNameForKindAsSQL = CBDB::stringToSQL(
                    $parameters->classNameForKind
                );
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

        $pages = CBDB::SQLToObjects($SQL);

        $pages = array_map(
            function ($item) {
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
            },
            $pages
        );

        return $pages;
    }
    /* CBAjax_fetchPages() */



    /**
     * @return string
     */
    static function CBAjax_fetchPages_getUserGroupClassName(): string {
        return 'CBAdministratorsUserGroup';
    }



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [
            Colby::flexpath(__CLASS__, 'css', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v591.js', cbsysurl()),
        ];
    }



    /**
     * @return [[string, mixed]]
     */
    static function CBHTMLOutput_JavaScriptVariables() {
        $pageKinds = CBDB::SQLToArray(
            'SELECT DISTINCT classNameForKind FROM ColbyPages'
        );

        $pageKinds = array_map(function ($pageKind) {
            if ($pageKind === null) {
                return (object)[
                    'title' => 'Unspecified',
                    'value' => 'unspecified',
                ];
            } else {
                return (object)[
                    'title' => $pageKind,
                    'value' => $pageKind,
                ];
            }
        }, $pageKinds);


        array_unshift($pageKinds,
            (object)[
                'title' => 'All',
                /* value unspecified */
            ],
            (object)[
                'title' => 'Current Front Page',
                'value' => 'currentFrontPage',
            ]
        );

        return [
            ['CBPageKindsOptions', $pageKinds]
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBConvert',
            'CBErrorHandler',
            'CBImage',
            'CBUI',
            'CBUINavigationView',
            'CBUISelector',
            'CBUIStringEditor',
            'CBUIThumbnailPart',
            'Colby',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $updater = CBModelUpdater::fetch(
            (object)[
                'ID' => CBPagesAdminMenu::ID(),
            ]
        );

        $pagesAdminMenuSpec = $updater->working;

        CBMenu::addOrReplaceItem(
            $pagesAdminMenuSpec,
            (object)[
                'className' => 'CBMenuItem',
                'name' => 'find',
                'text' => 'Find',
                'URL' => CBAdmin::getAdminPageURL(
                    'CBAdminPageForPagesFind'
                ),
            ]
        );

        CBModelUpdater::save($updater);
    }



    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBPagesAdminMenu',
        ];
    }

}
/* CBAdminPageForPagesFind */
