<?php

final class CBSitePreferencesEditor {

    /**
     * @return null
     */
    static function errorTestForAjax() {
        $response = new CBAjaxResponse();

        throw new RuntimeException("Sample PHP Error");

        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return object
     */
    static function errorTestForAjaxPermissions() {
        return (object)['group' => 'Developers'];
    }

    /**
     * @return [string]
     */
    static function requiredClassNames() {
        return ['CBArrayEditor', 'CBKeyValuePairEditor', 'CBUI', 'CBUIImageChooser', 'CBUIActionLink', 'CBUIStringEditor'];
    }

    /**
     * @return [string]
     */
    static function requiredCSSURLs() {
        return [Colby::flexnameForCSSForClass(CBSystemURL, __CLASS__)];
    }

    /**
     * @return [string]
     */
    static function requiredJavaScriptURLs() {
        return [
            CBSystemURL . '/javascript/CBBooleanEditorFactory.js',
            Colby::flexnameForJavaScriptForClass(CBSystemURL, __CLASS__),
        ];
    }
}
