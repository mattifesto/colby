<?php

final class CBUIRadioMenu {

    /**
     * @return [string]
     */
    static function requiredJavaScriptURLs() {
        return [CBUIRadioMenu::URL('CBUIRadioMenu.js')];
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
