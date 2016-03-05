<?php

final class CBUI {

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
