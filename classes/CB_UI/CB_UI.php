<?php

/**
 * @NOTE 2021_09_04
 *
 *      This class was created in part to replace CBUI, but mostly to represent
 *      the new common look of Colby websites that is more standardized across
 *      different sites.
 */
final class
CB_UI {

    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.41.css',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_CSSURLs() */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.37.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [[<name>, <value>]]
     */
    static function
    CBHTMLOutput_JavaScriptVariables(
    ): array {
        return [
            [
                'CB_UI_CBSitePreferences_appearance',
                CBSitePreferences::getAppearance(
                    CBSitePreferences::model()
                ),
            ]
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array {
        return [
            'Colby',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}
