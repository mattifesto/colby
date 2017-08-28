<?php

final class CBUIActionLink {

    /**
     * @return [string]
     */
    static function requiredClassNames() {
        return [];
    }

    /**
     * @return [string]
     */
    static function requiredCSSURLs() {
        return [CBUIActionLink::URL('CBUIActionLink.css')];
    }

    /**
     * @return [string]
     */
    static function requiredJavaScriptURLs() {
        return [CBUIActionLink::URL('CBUIActionLink.js')];
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
