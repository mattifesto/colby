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
        return
        [
            Colby::flexpath(
                __CLASS__,
                'v675.61.7.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [[<name>, <value>]]
     */
    static function
    CBHTMLOutput_JavaScriptVariables(
    ): array
    {
        return
        [
            [
                'CB_CBView_Hero1Editor_addableClassNames',
                CBPagesPreferences::classNamesForAddableViews(),
            ]
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array
    {
        return
        [
            'CB_UI_ImageChooser',
            'CB_UI_StringEditor',
            'CBAjax',
            'CBModel',
            'CBUIPanel',
            'CBUISpecArrayEditor',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}
