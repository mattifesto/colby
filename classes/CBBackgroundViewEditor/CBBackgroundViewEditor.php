<?php

final class CBBackgroundViewEditor {

    /**
     * @return [string]
     */
    public static function requiredClassNames() {
        return ['CBArrayEditor', 'CBUI', 'CBUIActionLink', 'CBUIStringEditor'];
    }

    /**
     * @return [string]
     */
    public static function requiredCSSURLs() {
        return [
            CBSystemURL . '/javascript/CBImageEditorFactory.css',
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
            CBBackgroundViewEditor::URL('CBBackgroundViewEditor.js')
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
     * @param string $filename
     *
     * @return string
     */
    public static function URL($filename) {
        $className = __CLASS__;
        return CBSystemURL . "/classes/{$className}/{$filename}";
    }
}
