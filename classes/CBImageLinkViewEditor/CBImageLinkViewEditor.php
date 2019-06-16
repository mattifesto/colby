<?php

final class CBImageLinkViewEditor {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBImage',
            'CBUI',
            'CBUIBooleanEditor',
            'CBUIImageUploader',
            'CBUIImageURLView',
            'CBUIStringEditor',
        ];
    }


    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [
            Colby::flexpath(__CLASS__, 'css', cbsysurl())
        ];
    }


    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v480.js', cbsysurl())
        ];
    }
}
/* CBImageLinkViewEditor */
