<?php

final class CBFlexBoxViewEditor {

    /**
     * @return [string]
     */
    public static function requiredClassNames() {
        return ['CBArrayEditor', 'CBUI', 'CBUIActionLink', 'CBUISpec'];
    }

    /**
     * @return [string]
     */
    public static function requiredCSSURLs() {
        return [
            CBSystemURL . '/javascript/CBImageEditorFactory.css',
            CBFlexBoxViewEditor::URL('CBFlexBoxViewEditor.css'),
        ];
    }

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [
            CBSystemURL . '/javascript/CBImageEditorFactory.js',
            CBSystemURL . '/javascript/CBStringEditorFactory.js',
            CBFlexBoxViewEditor::URL('CBFlexBoxViewEditor.js'),
        ];
    }

    /**
     * @return string
     */
    public static function URL($filename) {
        $className = __CLASS__;
        return CBSystemURL . "/classes/{$className}/{$filename}";
    }
}
