<?php

final class CBImageEditor {

    /**
     * @return [string]
     */
    static function requiredCSSURLs() {
        return [CBSystemURL . '/javascript/CBImageEditorFactory.css'];
    }

    /**
     * @return [string]
     */
    static function requiredJavaScriptURLs() {
        return [CBSystemURL . '/javascript/CBImageEditorFactory.js'];
    }
}
