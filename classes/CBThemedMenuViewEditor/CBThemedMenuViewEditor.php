<?php

final class CBThemedMenuViewEditor {

    /**
     * @return [string]
     */
    public static function requiredClassNames() {
        return ['CBUI', 'CBResponsiveEditor', 'CBUISelector', 'CBUIThemeSelector'];
    }

    /**
     * @return [string]
     */
    public static function requiredCSSURLs() {
        return [CBThemedMenuViewEditor::URL('CBThemedMenuViewEditor.css')];
    }

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [CBThemedMenuViewEditor::URL('CBThemedMenuViewEditor.js')];
    }

    /**
     * @return string
     */
    public static function URL($filename) {
        $className = __CLASS__;
        return CBSystemURL . "/classes/{$className}/{$filename}";
    }

}
