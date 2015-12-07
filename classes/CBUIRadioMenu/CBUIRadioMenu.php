<?php

final class CBUIRadioMenu {

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [CBUIRadioMenu::URL('CBUIRadioMenu.js')];
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
