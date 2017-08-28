<?php

/**
 * This class provides JavaScript spec related helper functions.
 */
final class CBUISpec {

    /**
     * @return [string]
     */
    static function requiredJavaScriptURLs() {
        return [CBUISpec::URL('CBUISpec.js')];
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
