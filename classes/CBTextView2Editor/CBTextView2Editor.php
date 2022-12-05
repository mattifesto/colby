<?php

final class
CBTextView2Editor
{
    // -- CBHTMLOutput interfaces



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
                '2022_12_04_1670178526',
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
            'CBAjax',
            'CBModel',
            'CBUI',
            'CBUINavigationView',
            'CBUIPanel',
            'CBUISpecClipboard',
            'CBUISpecEditor',
            'CBUIStringEditor2',
        ];

        return $arrayOfRequiredClassNames;
    }
    // CBHTMLOutput_requiredClassNames()

}
/* CBTextView2Editor */
