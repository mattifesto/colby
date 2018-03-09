<?php

/**
 * @deprecated 2018.03.09 use CBUIStringsPart and CBUIBooleanSwitchPart
 */
final class CBUIBooleanEditor {

    /**
     * @return [string]
     */
    static function requiredClassNames() {
        return ['CBUI'];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [CBUIBooleanEditor::URL('CBUIBooleanEditor.css')];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [CBUIBooleanEditor::URL('CBUIBooleanEditor.js')];
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
