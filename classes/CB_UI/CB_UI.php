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
    // -- CBHTMLOutput interfaces



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(
    ): array
    {
        $arrayOfCSSURLs =
        [
            CBLibrary::buildLibraryClassFilePath(
                __CLASS__,
                '2023_03_14_1678827501',
                'css',
                cbsysurl()
            ),
        ];

        return $arrayOfCSSURLs;
    }
    // CBHTMLOutput_CSSURLs()



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs_Immediate(
    ): array
    {
        $arrayOfJavaScriptURLs =
        [
            CBLibrary::buildLibraryClassFilePath(
                __CLASS__,
                '2022_10_15_1665851883',
                'js',
                cbsysurl()
            ),
        ];

        return $arrayOfJavaScriptURLs;
    }
    // CBHTMLOutput_JavaScriptURLs_Immediate()



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
