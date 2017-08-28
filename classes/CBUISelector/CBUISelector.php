<?php

final class CBUISelector {

    /**
     * @return [string]
     */
    static function requiredClassNames() {
        return ['CBUI'];
    }

    /**
     * @return [string]
     */
    static function requiredCSSURLs() {
        return [CBUISelector::URL('CBUISelector.css')];
    }

    /**
     * @return [string]
     */
    static function requiredJavaScriptURLs() {
        return [CBUISelector::URL('CBUISelector.js')];
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
