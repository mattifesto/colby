<?php

final class CBUIOptionArrayEditor {

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [CBUIOptionArrayEditor::URL('CBUIOptionArrayEditor.js')];
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
