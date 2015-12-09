<?php

final class CBResponsiveEditor {

    /**
     * @return [string]
     */
    public static function requiredCSSURLs() {
        return [CBSystemURL . '/javascript/CBResponsiveEditorFactory.css'];
    }

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [CBSystemURL . '/javascript/CBResponsiveEditorFactory.js'];
    }
}
