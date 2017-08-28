<?php

final class CBThemedMenuViewEditor {

    /**
     * @return [string]
     */
    static function requiredClassNames() {
        return ['CBUI', 'CBResponsiveEditor', 'CBUISelector', 'CBUIThemeSelector'];
    }

    /**
     * @return [string]
     */
    static function requiredCSSURLs() {
        return [CBThemedMenuViewEditor::URL('CBThemedMenuViewEditor.css')];
    }

    /**
     * @return [string]
     */
    static function requiredJavaScriptURLs() {
        return [CBThemedMenuViewEditor::URL('CBThemedMenuViewEditor.js')];
    }

    /**
     * @return string
     */
    static function URL($filename) {
        $className = __CLASS__;
        return CBSystemURL . "/classes/{$className}/{$filename}";
    }

}
