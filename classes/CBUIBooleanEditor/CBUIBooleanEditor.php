<?php

/**
 * @deprecated 2018_03_09 use CBUIStringsPart and CBUIBooleanSwitchPart
 */
final class CBUIBooleanEditor {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v485.css', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_CSSURLs() */


    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [
            Colby::flexpath(__CLASS__, 'v485.js', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return [
            'CBUI',
        ];
    }
}
/* CBUIBooleanEditor */
