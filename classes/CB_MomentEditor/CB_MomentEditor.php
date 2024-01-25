<?php

final class CB_MomentEditor
{
    // -- CBHTMLOutput interfaces



    /**
     * @return [string]
     */
    public static function CBHTMLOutput_JavaScriptURLs(): array
    {
        $javaScriptURLs = [
            CBLibrary::buildLibraryClassFilePath(
                __CLASS__,
                '2024_01_22_07_42',
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
    public static function CBHTMLOutput_requiredClassNames(): array
    {
        $requiredClassNames = [
            'CB_UI_StringEditor',
            'CBModel',
        ];

        return $requiredClassNames;
    }
    // CBHTMLOutput_requiredClassNames()

}
