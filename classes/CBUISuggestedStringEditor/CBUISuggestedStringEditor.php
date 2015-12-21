<?php

final class CBUISuggestedStringEditor {

    /**
     * @return [string]
     */
    public static function requiredCSSURLs() {
        return [CBUISuggestedStringEditor::URL('CBUISuggestedStringEditor.css')];
    }

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [CBUISuggestedStringEditor::URL('CBUISuggestedStringEditor.js')];
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
