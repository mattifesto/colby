<?php

final class
CBThemedTextViewEditor
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
                '2022_12_04_1670179647',
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
            'CBUI',
            'CBUIBooleanEditor',
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
