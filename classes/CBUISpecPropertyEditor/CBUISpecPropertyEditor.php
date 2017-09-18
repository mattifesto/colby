<?php

/**
 * This class edits a property that holds a spec.
 *
 * It is similar to the CBArrayEditor class which does the same thing but for an
 * array of specs.
 */
final class CBUISpecPropertyEditor {

    /**
     * @return [string]
     */
    static function requiredClassNames() {
        return ['CBUI', 'CBUIActionLink', 'CBUISpec'];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [CBUISpecPropertyEditor::URL('CBUISpecPropertyEditor.css')];
    }

    /**
     * @return [string]
     */
    static function requiredJavaScriptURLs() {
        return [CBUISpecPropertyEditor::URL('CBUISpecPropertyEditor.js')];
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
