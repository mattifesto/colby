<?php

final class
CB_CBView_Hero1Editor
{
    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array
    {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.61.3.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array
    {
        return [
            'CB_UI_ImageChooser',
            'CB_UI_StringEditor',
            'CBAjax',
            'CBUIPanel',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}
