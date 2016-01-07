<?php

final class CBPageEditor {

    /**
     * @return [string]
     */
    public static function requiredClassNames() {
        return ['CBUI', 'CBUIStringEditor', 'CBUISuggestedStringEditor', 'CBUIUnixTimestampEditor'];
    }


    /**
     * @return [string]
     */
    public static function requiredCSSURLs() {
        return [CBPageEditor::URL('CBPageEditor.css')];
    }

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [CBPageEditor::URL('CBPageEditor.js')];
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
