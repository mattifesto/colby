<?php

final class CBSitePreferencesEditor {

    /**
     * @return null
     */
    static function CBAjax_errorTest() {
        //throw new RuntimeException(str_repeat("This is a test of a long message. ", 1000));
        throw new RuntimeException("Sample PHP Error");
    }

    /**
     * @return string
     */
    static function CBAjax_errorTest_group() {
        return 'Developers';
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return ['CBArrayEditor', 'CBKeyValuePairEditor', 'CBUI',
                'CBUIBooleanEditor', 'CBUIImageChooser', 'CBUIActionLink',
                'CBUIStringEditor'];
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
        return [Colby::flexpath(__CLASS__, 'js', cbsysurl())];
    }
}
