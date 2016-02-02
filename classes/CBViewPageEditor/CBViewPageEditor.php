<?php

final class CBViewPageEditor {

    /**
     * @return [string]
     */
    public static function requiredClassNames() {
        return ['CBArrayEditor', 'CBUI', 'CBViewPageInformationEditor'];
    }

    /**
     * @return [string]
     */
    public static function requiredCSSURLs() {
        return [CBViewPageEditor::URL('CBViewPageEditor.css')];
    }

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [
            CBSystemURL . '/javascript/CBDelayTimer.js', /* deprecated */
            CBViewPageEditor::URL('CBViewPageEditor.js'),
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
