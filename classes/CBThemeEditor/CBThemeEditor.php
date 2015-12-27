<?php

final class CBThemeEditor {

    /**
     * @return [string]
     */
    public static function requiredClassNames() {
        return ['CBUI', 'CBUIStringEditor'];
    }

    /**
     * @return [string]
     */
    public static function requiredCSSURLs() {
        return [CBThemeEditor::URL('CBThemeEditor.css')];
    }

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [CBThemeEditor::URL('CBThemeEditor.js')];
    }

    /**
     * @return string
     */
    public static function URL($filename) {
        $className = __CLASS__;
        return CBSystemURL . "/classes/{$className}/{$filename}";
    }
}
