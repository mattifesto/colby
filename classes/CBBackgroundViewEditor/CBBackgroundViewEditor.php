<?php

final class CBBackgroundViewEditor {

    /**
     * @return [string]
     */
    public static function requiredClassNames() {
        return ['CBArrayEditor', 'CBImageEditor', 'CBUI', 'CBUIActionLink', 'CBUIBooleanEditor', 'CBUIStringEditor'];
    }

    /**
     * @return [string]
     */
    public static function requiredCSSURLs() {
        return [CBBackgroundViewEditor::URL('CBBackgroundViewEditor.css')];
    }

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [CBBackgroundViewEditor::URL('CBBackgroundViewEditor.js')];
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
