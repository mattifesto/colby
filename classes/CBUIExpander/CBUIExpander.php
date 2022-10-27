<?php

final class
CBUIExpander
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
                '2022_06_21_1655785586.css',
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
    CBHTMLOutput_JavaScriptURLs(
    ): array
    {
        $arrayOfJavaScriptURLs =
        [
            Colby::flexpath(
                __CLASS__,
                'v503.js',
                cbsysurl()
            ),
        ];

        return $arrayOfJavaScriptURLs;
    }
    // CBHTMLOutput_JavaScriptURLs()



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array
    {
        return
        [
            'CBContentStyleSheet',
            'CBConvert',
            'CBMessageMarkup'
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}
