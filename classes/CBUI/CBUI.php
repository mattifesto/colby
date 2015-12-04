<?php

final class CBUI {

    /**
     * @deprecated use requiredCSSURLs
     *
     * @return [string]
     */
    public static function editorURLsForCSS() {
        return CBUI::requiredCSSURLs();
    }

    /**
     * @deprecated use requiredJavaScriptURLs
     *
     * @return [string]
     */
    public static function editorURLsForJavaScript() {
        return CBUI::requiredJavaScriptURLs();
    }

    /**
     * @return [string]
     */
    public static function requiredCSSURLs() {
        return [CBSystemURL . '/javascript/CBUI.css'];
    }

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [CBSystemURL . '/javascript/CBUI.js'];
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
