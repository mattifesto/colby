<?php

final class CBSitePreferencesEditor {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return ['CBArrayEditor', 'CBKeyValuePairEditor', 'CBUI',
                'CBUIBooleanEditor', 'CBUIImageChooser', 'CBUIStringEditor'];
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
