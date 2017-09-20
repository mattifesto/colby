<?php

final class CBUISpecEditor {

    /**
     * @return [string]
     */
    static function requiredClassNames() {
        return ['CBDefaultEditor'];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [CBUISpecEditor::URL('CBUISpecEditor.js')];
    }

    /**
     * @return string
     */
    static function URL($filename) {
        $className = __CLASS__;
        return CBSystemURL . "/classes/{$className}/{$filename}";
    }
}
