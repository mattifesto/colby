<?php

final class CBThemeEditor {

    /**
     * @return [string]
     */
    static function requiredClassNames() {
        return ['CBUI', 'CBUIStringEditor'];
    }

    /**
     * @return [string]
     */
    static function requiredCSSURLs() {
        return [CBThemeEditor::URL('CBThemeEditor.css')];
    }

    /**
     * @return [string]
     */
    static function requiredJavaScriptURLs() {
        return [CBThemeEditor::URL('CBThemeEditor.js')];
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
