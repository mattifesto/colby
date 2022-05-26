<?php

/**
 * @NOTE 2021_09_04
 *
 *      This class was created in part to replace CBUI, but mostly to represent
 *      the new common look of Colby websites that is more standardized across
 *      different sites.
 *
 * @TODO 2022_03_08
 *
 *      This file works together with CBEqualizePageSettingsPart. See that file
 *      for more informaton.
 */
final class
CB_UI
{
    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(
    ): array
    {
        return
        [
            Colby::flexpath(
                __CLASS__,
                'v2022.05.26.1653599619.css',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_CSSURLs() */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs_Immediate(
    ): array
    {
        return
        [
            Colby::flexpath(
                __CLASS__,
                'v675.55.js',
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
    ): array
    {
        return
        [
            [
                'CB_UI_CBSitePreferences_appearance',
                CBSitePreferences::getAppearance(
                    CBSitePreferences::model()
                ),
            ]
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */

}
