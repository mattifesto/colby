<?php

final class CBMenuEditor {

    /**
     * @return [string]
     */
    public static function requiredClassNames() {
        return ['CBUI', 'CBUIStringEditor', 'CBMenuItemEditor'];
    }

    /**
     * @return [string]
     */
    public static function requiredCSSURLs() {
        return [
            CBSystemURL . '/javascript/CBSpecArrayEditor.css',
            CBMenuEditor::URL('CBMenuEditor.css')
        ];
    }

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [
            CBSystemURL . '/javascript/CBSpecArrayEditorFactory.js',
            CBSystemURL . '/javascript/CBStringEditorFactory.js',
            CBMenuEditor::URL('CBMenuEditor.js')
        ];
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
