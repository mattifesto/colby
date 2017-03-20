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
     * @return stdClass
     */
    static function errorTestForAjaxPermissions() {
        return (object)['group' => 'Developers'];
    }

    /**
     * @return [string]
     */
    public static function requiredClassNames() {
        return ['CBArrayEditor', 'CBKeyValuePairEditor', 'CBUI', 'CBUIActionLink', 'CBUIStringEditor'];
    }

    /**
     * @return [string]
     */
    public static function requiredCSSURLs() {
        return [CBSitePreferencesEditor::URL('CBSitePreferencesEditor.css')];
    }

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [
            CBSystemURL . '/javascript/CBBooleanEditorFactory.js',
            CBSitePreferencesEditor::URL('CBSitePreferencesEditor.js')];
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    public static function URL($filename) {
        $className = __CLASS__;
        return CBSystemURL . "/classes/{$className}/{$filename}";
    }
}
