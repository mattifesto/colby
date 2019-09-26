<?php

final class CBTextView2Editor {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v529.js', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */


    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBErrorHandler',
            'CBModel',
            'CBUI',
            'CBUINavigationView',
            'CBUIPanel',
            'CBUISpecClipboard',
            'CBUISpecEditor',
            'CBUIStringEditor',
            'Colby',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */
}
/* CBTextView2Editor */
