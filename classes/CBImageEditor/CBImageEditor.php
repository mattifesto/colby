<?php

final class CBImageEditor {

    /**
     * @return [string]
     */
    public static function requiredCSSURLs() {
        return [CBSystemURL . '/javascript/CBImageEditorFactory.css'];
    }

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [CBSystemURL . '/javascript/CBImageEditorFactory.js'];
    }
}
