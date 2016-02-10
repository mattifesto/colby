<?php

final class CBUISpecEditor {

    /**
     * @return [string]
     */
    public static function requiredClassNames() {
        return ['CBDefaultEditor'];
    }

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [CBUISpecEditor::URL('CBUISpecEditor.js')];
    }

    /**
     * @return string
     */
    public static function URL($filename) {
        $className = __CLASS__;
        return CBSystemURL . "/classes/{$className}/{$filename}";
    }
}
