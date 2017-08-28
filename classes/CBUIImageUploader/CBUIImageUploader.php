<?php

final class CBUIImageUploader {

    /**
     * @return [string]
     */
    static function requiredCSSURLs() {
        return [CBUIImageUploader::URL('CBUIImageUploader.css')];
    }

    /**
     * @return [string]
     */
    static function requiredJavaScriptURLs() {
        return [CBUIImageUploader::URL('CBUIImageUploader.js')];
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
