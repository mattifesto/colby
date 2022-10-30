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
            CBLibrary::buildLibraryClassFilePath(
                __CLASS__,
                '2022_10_29_1667052384',
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
    CBHTMLOutput_JavaScriptURLs(
    ): array
    {
        $arrayOfJavaScriptURLs =
        [
            CBLibrary::buildLibraryClassFilePath(
                __CLASS__,
                '2022_10_27_1666889444',
                'js',
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
        $arrayOfRequiredClassNames =
        [
            'CBConvert',
            'CBMessageMarkup',
            'Colby',

            'CB_MaterialSymbols',
            'CB_UI',
            'CBContentStyleSheet',
        ];

        return $arrayOfRequiredClassNames;
    }
    // CBHTMLOutput_requiredClassNames()

}
