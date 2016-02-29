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
    public static function requiredClassNames() {
        return ['CBUI', 'CBUIActionLink', 'CBUISpec'];
    }

    /**
     * @return [string]
     */
    public static function requiredCSSURLs() {
        return [CBUISpecPropertyEditor::URL('CBUISpecPropertyEditor.css')];
    }

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [CBUISpecPropertyEditor::URL('CBUISpecPropertyEditor.js')];
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
