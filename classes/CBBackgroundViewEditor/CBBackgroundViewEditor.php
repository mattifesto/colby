<?php

final class CBBackgroundViewEditor {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBUI',
            'CBUIActionLink',
            'CBUIBooleanEditor',
            'CBUIImageChooser',
            'CBUISpec',
            'CBUISpecArrayEditor',
            'CBUIStringEditor'
        ];
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
        return [Colby::flexpath(__CLASS__, 'v366.js', cbsysurl())];
    }

    /**
     * @return [[string, mixed]]
     */
    static function CBHTMLOutput_JavaScriptVariables() {
        return [
            ['CBBackgroundViewEditor_addableClassNames', CBPagesPreferences::classNamesForAddableViews()]
        ];
    }
}
