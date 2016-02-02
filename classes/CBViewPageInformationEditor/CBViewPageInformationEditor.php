<?php

final class CBViewPageInformationEditor {

    /**
     * @return [string]
     */
    public static function requiredClassNames() {
        return ['CBImageEditor', 'CBUI', 'CBUIStringEditor'];
    }

    /**
     * @return [string]
     */
    public static function requiredCSSURLs() {
        return [CBViewPageInformationEditor::URL('CBViewPageInformationEditor.css')];
    }

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [
            CBSystemURL . '/javascript/CBPageURIControl.js',
            CBSystemURL . '/javascript/CBPublicationControl.js',
            CBViewPageInformationEditor::URL('CBViewPageInformationEditor.js'),
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
