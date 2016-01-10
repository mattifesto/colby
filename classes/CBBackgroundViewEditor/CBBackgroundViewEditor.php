<?php

final class CBBackgroundViewEditor {

    /**
     * @return [string]
     */
    public static function requiredClassNames() {
        return ['CBUI'];
    }

    /**
     * @return [string]
     */
    public static function requiredCSSURLs() {
        return [
            CBSystemURL . '/javascript/CBImageEditorFactory.css',
            CBSystemURL . '/javascript/CBSpecArrayEditor.css',
            CBBackgroundViewEditor::URL('CBBackgroundViewEditor.css')
        ];
    }

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [
            CBSystemURL . '/javascript/CBBooleanEditorFactory.js',
            CBSystemURL . '/javascript/CBImageEditorFactory.js',
            CBSystemURL . '/javascript/CBSpecArrayEditorFactory.js',
            CBSystemURL . '/javascript/CBStringEditorFactory.js',
            CBBackgroundViewEditor::URL('CBBackgroundViewEditorFactory.js')
        ];
    }

    /**
     * @return [[string, mixed]]
     */
    public static function requiredJavaScriptVariables() {
        return [
            ['CBBackgroundViewAddableViews', CBPagesPreferences::classNamesForAddableViews()]
        ];
    }

    /**
     * @return string
     */
    public static function URL($filename) {
        $className = __CLASS__;
        return CBSystemURL . "/classes/{$className}/{$filename}";
    }
}
