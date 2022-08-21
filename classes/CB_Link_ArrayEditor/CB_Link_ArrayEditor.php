<?php

final class
CB_Link_ArrayEditor
{
    // -- CBHTMLOutput interfaces



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(
    ): array
    {
        $cssURLs =
        [
            CBLibrary::buildLibraryClassFilePath(
                __CLASS__,
                '2022_08_18_1660861717',
                'css',
                cbsysurl()
            ),
        ];

        return $cssURLs;
    }
    // CBHTMLOutput_CSSURLs()



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array
    {
        $javaScriptURLs =
        [
            CBLibrary::buildLibraryClassFilePath(
                __CLASS__,
                '2022_08_16_1660611105',
                'js',
                cbsysurl()
            ),
        ];

        return $javaScriptURLs;
    }
    // CBHTMLOutput_JavaScriptURLs()



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array
    {
        $requiredClassNames =
        [
            'CB_Link',
            'CB_Link_Array',
            'CB_UI',
            'CBUINavigationView',
            'CBUISpecArrayEditor',

            'CB_LinkEditor',
        ];

        return $requiredClassNames;
    }
    // CBHTMLOutput_requiredClassNames()

}
