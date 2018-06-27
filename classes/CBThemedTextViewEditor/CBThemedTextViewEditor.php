<?php

final class CBThemedTextViewEditor {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return ['CBUI', 'CBUIBooleanEditor', 'CBUIStringEditor'];
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
        return [Colby::flexpath(__CLASS__, 'v433.js', cbsysurl())];
    }
}
