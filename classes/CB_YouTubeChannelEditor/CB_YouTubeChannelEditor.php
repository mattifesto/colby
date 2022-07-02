<?php

final class
CB_YouTubeChannelEditor
{
    // -- CBHTMLOutput interfaces



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array
    {
        return
        [
            Colby::flexpath(
                __CLASS__,
                '2022_06_12_1655049746.js',
                cbsysurl()
            ),
        ];
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
            'CB_UI_StringEditor',
            'CBModel',
        ];
    }
    // CBHTMLOutput_requiredClassNames()

}
