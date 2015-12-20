<?php

final class CBUIUnixTimestampEditor {

    /**
     * @return [string]
     */
    public static function requiredCSSURLs() {
        return [CBUIUnixTimestampEditor::URL('CBUIUnixTimestampEditor.css')];
    }

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [CBUIUnixTimestampEditor::URL('CBUIUnixTimestampEditor.js')];
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
