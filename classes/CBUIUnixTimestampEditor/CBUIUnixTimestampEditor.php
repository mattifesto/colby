<?php

final class CBUIUnixTimestampEditor {

    /**
     * @return [string]
     */
    static function requiredCSSURLs() {
        return [CBUIUnixTimestampEditor::URL('CBUIUnixTimestampEditor.css')];
    }

    /**
     * @return [string]
     */
    static function requiredJavaScriptURLs() {
        return [CBUIUnixTimestampEditor::URL('CBUIUnixTimestampEditor.js')];
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
