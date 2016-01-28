<?php

final class CBUIBooleanEditor {

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
        return [CBUIBooleanEditor::URL('CBUIBooleanEditor.css')];
    }

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [CBUIBooleanEditor::URL('CBUIBooleanEditor.js')];
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
