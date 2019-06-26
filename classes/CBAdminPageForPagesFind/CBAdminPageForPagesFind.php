<?php

final class CBAdminPageForPagesFind {

    /**
     * @return [string]
     */
    static function adminPageMenuNamePath() {
        return [
            'pages',
            'find',
        ];
    }

    /**
     * @return object
     */
    static function adminPagePermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @return null
     */
    static function adminPageRenderContent() {
        CBHTMLOutput::pageInformation()->title = 'Pages Administration: Find';
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
            Colby::flexpath(__CLASS__, 'v483.js', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */


    /**
     * @return [[string, mixed]]
     */
    static function CBHTMLOutput_JavaScriptVariables() {
        $pageKinds = CBDB::SQLToArray('SELECT DISTINCT `classNameForKind` FROM `ColbyPages`');

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
            'CBImage',
            'CBUI',
            'CBUINavigationArrowPart',
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
        $updater = CBModelUpdater::fetch((object)[
            'ID' => CBPagesAdminMenu::ID(),
        ]);

        $pagesAdminMenuSpec = $updater->working;

        CBMenu::addOrReplaceItem(
            $pagesAdminMenuSpec,
            (object)[
                'className' => 'CBMenuItem',
                'name' => 'find',
                'text' => 'Find',
                'URL' => '/admin/page/?class=CBAdminPageForPagesFind',
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
