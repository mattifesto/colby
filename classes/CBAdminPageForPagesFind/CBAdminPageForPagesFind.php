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

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBUI',
            'CBUINavigationArrowPart',
            'CBUINavigationView',
            'CBUISectionItem4',
            'CBUISelector',
            'CBUIStringEditor',
            'CBUIStringsPart',
            'CBUIThumbnailPart',
        ];
    }

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
            Colby::flexpath(__CLASS__, 'v411.js', cbsysurl()),
        ];
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
