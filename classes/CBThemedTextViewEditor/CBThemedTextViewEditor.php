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
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v660.js', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBAjax',
            'CBUI',
            'CBUIBooleanEditor',
            'CBUINavigationView',
            'CBUIPanel',
            'CBUISectionItem4',
            'CBUISpecClipboard',
            'CBUISpecEditor',
            'CBUIStringEditor',
            'CBUIStringsPart',
        ];
    }

}
