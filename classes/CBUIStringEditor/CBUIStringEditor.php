<?php

final class CBUIStringEditor {

    /**
     * @deprecated use requiredCSSURLs
     *
     * @return [string]
     */
    public static function editorURLsForCSS() {
        return CBUIStringEditor::requiredCSSURLs();
    }

    /**
     * @deprecated use requiredJavaScriptURLs
     *
     * @return [string]
     */
    public static function editorURLsForJavaScript() {
        return CBUIStringEditor::requiredJavaScriptURLs();
    }

    /**
     * @return [string]
     */
    public static function requiredCSSURLs() {
        return [CBUIStringEditor::URL('CBUIStringEditor.css')];
    }

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [CBUIStringEditor::URL('CBUIStringEditor.js')];
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
