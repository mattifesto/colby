<?php

final class CBTextView2Editor {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */


    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.7.js',
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
     ): array {
        return [
            'CBAjax',
            'CBModel',
            'CBUI',
            'CBUINavigationView',
            'CBUIPanel',
            'CBUISpecClipboard',
            'CBUISpecEditor',
            'CBUIStringEditor2',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}
/* CBTextView2Editor */
