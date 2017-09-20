<?php

final class CBUIImageUploader {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [CBUIImageUploader::URL('CBUIImageUploader.css')];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
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
