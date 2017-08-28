<?php

final class CBBackgroundViewEditor {

    /**
     * @return [string]
     */
    static function requiredClassNames() {
        return ['CBArrayEditor', 'CBImageEditor', 'CBUI', 'CBUIActionLink', 'CBUIBooleanEditor', 'CBUISpec', 'CBUIStringEditor'];
    }

    /**
     * @return [string]
     */
    static function requiredCSSURLs() {
        return [CBBackgroundViewEditor::URL('CBBackgroundViewEditor.css')];
    }

    /**
     * @return [string]
     */
    static function requiredJavaScriptURLs() {
        return [CBBackgroundViewEditor::URL('CBBackgroundViewEditor.js')];
    }

    /**
     * @return [[string, mixed]]
     */
    static function requiredJavaScriptVariables() {
        return [
            ['CBBackgroundViewAddableViews', CBPagesPreferences::classNamesForAddableViews()]
        ];
    }

    /**
     * @param string $filename
     *
     * @return string
     */
    static function URL($filename) {
        $className = __CLASS__;
        return CBSystemURL . "/classes/{$className}/{$filename}";
    }
}
