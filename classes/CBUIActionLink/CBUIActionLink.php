<?php

final class CBUIActionLink {

    /**
     * @return [string]
     */
    public static function requiredClassNames() {
        return [];
    }

    /**
     * @return [string]
     */
    public static function requiredCSSURLs() {
        return [CBUIActionLink::URL('CBUIActionLink.css')];
    }

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [CBUIActionLink::URL('CBUIActionLink.js')];
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
