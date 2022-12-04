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
            Colby::flexpath(
                __CLASS__,
                'v675.7.js',
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
