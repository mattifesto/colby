<?php

final class CBStandardBodyTextTheme {

    /**
     * @return [string]
     */
    public static function requiredCSSURLs() {
        return [CBStandardBodyTextTheme::URL('CBStandardBodyTextTheme.css')];
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
