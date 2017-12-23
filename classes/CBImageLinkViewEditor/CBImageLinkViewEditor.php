<?php

final class CBImageLinkViewEditor {

    /**
     * @return [string]
     */
    static function requiredClassNames() {
        return ['CBUI', 'CBUIBooleanEditor', 'CBUIImageUploader',
                'CBUIImageURLView', 'CBUIStringEditor'];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [Colby::flexpath(__CLASS__, 'css', cbsysurl())];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'v360.js', cbsysurl())];
    }
}
