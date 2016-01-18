<?php

final class CBImageLinkViewEditor {

    /**
     * @return [string]
     */
    public static function requiredClassNames() {
        return ['CBUI'];
    }

    /**
     * @return [string]
     */
    public static function requiredCSSURLs() {
        return [
            CBSystemURL . '/javascript/CBImageEditorFactory.css',
            CBImageLinkViewEditor::URL('CBImageLinkViewEditor.css')
        ];
    }

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [
            CBSystemURL . '/javascript/CBImageEditorFactory.js',
            CBSystemURL . '/javascript/CBStringEditorFactory.js',
            CBImageLinkViewEditor::URL('CBImageLinkViewEditor.js')
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
