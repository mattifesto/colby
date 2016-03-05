<?php

final class CBThemedTextViewEditor {

    /**
     * @return [string]
     */
    public static function requiredClassNames() {
        return ['CBUI', 'CBUIStringEditor', 'CBUIThemeSelector'];
    }

    /**
     * @return [string]
     */
    public static function requiredCSSURLs() {
        return [CBThemedTextViewEditor::URL('CBThemedTextViewEditor.css')];
    }

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [
            CBSystemURL . '/javascript/CBStringEditorFactory.js',
            CBThemedTextViewEditor::URL('CBThemedTextViewEditor.js'),
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
