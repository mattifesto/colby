<?php

final class CBThemedTextViewEditor {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [
            Colby::flexpath(__CLASS__, 'css', cbsysurl()),
        ];
    }



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
            'CBUI',
            'CBUIBooleanEditor',
            'CBUINavigationView',
            'CBUIPanel',
            'CBUISpecClipboard',
            'CBUISpecEditor',
            'CBUIStringEditor',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */

}
