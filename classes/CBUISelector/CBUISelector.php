<?php

final class CBUISelector {

    /**
     * @return [string]
     */
    public static function requiredCSSURLs() {
        return [CBUISelector::URL('CBUISelector.css')];
    }

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [CBUISelector::URL('CBUISelector.js')];
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
