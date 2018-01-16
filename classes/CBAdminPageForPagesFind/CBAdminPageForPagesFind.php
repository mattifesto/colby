<?php

final class CBAdminPageForPagesFind {

    /**
     * @return [string]
     */
    static function adminPageMenuNamePath() {
        return ['pages', 'find'];
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
        CBHTMLOutput::setTitleHTML('Find Pages');
        CBHTMLOutput::setDescriptionHTML('Find pages to edit, copy, or delete.');
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
         return ['CBUI', 'CBUINavigationView', 'CBUISelector', 'CBUIStringEditor'];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [Colby::flexpath(__CLASS__, 'css', cbsysurl())];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'v368.js', cbsysurl())];
    }

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
}
