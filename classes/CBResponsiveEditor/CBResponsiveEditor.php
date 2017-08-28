<?php

final class CBResponsiveEditor {

    /**
     * @return [string]
     */
    static function requiredCSSURLs() {
        return [CBSystemURL . '/javascript/CBResponsiveEditorFactory.css'];
    }

    /**
     * @return [string]
     */
    static function requiredJavaScriptURLs() {
        return [CBSystemURL . '/javascript/CBResponsiveEditorFactory.js'];
    }
}
