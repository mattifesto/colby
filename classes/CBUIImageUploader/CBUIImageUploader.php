<?php

final class CBUIImageUploader {

    /**
     * @return [string]
     */
    public static function requiredCSSURLs() {
        return [CBUIImageUploader::URL('CBUIImageUploader.css')];
    }

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [CBUIImageUploader::URL('CBUIImageUploader.js')];
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
