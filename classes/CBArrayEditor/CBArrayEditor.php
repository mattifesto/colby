<?php

/**
 * This class edits a property holding an array of specs and should potentially
 * be named CBUISpecArrayEditor or CBUISpecArrayPropertyEditor.
 *
 * It is similar to the CBUISpecPropertyEditor which does the same thing but
 * for a single spec instead of an array.
 */
final class CBArrayEditor {

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
        return [CBSystemURL . '/javascript/CBArrayEditor.css'];
    }

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [CBSystemURL . '/javascript/CBArrayEditorFactory.js'];
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
