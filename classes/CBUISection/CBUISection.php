<?php

/**
 * @deprecated 2020_12_15
 *
 *      Use CBUI.createElement() with the "CBUI_section" class name.
 */
final class
CBUISection
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
            Colby::flexpath(
                __CLASS__,
                'v675.61.css',
                cbsysurl()
            ),
        ];

        return $arrayOfCSSURLs;
    }
    // CBHTMLOutput_CSSURLs()



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v440.js', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array
    {
        return [
            'CB_UI',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}
