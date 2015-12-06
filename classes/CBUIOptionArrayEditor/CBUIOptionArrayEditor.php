<?php

final class CBUIOptionArrayEditor {

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [CBUIOptionArrayEditor::URL('CBUIOptionArrayEditor.js')];
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
