<?php

final class CBResponsiveEditor {

    /**
     * @return [string]
     */
    public static function editorURLsForCSS() {
        return [CBSystemURL . '/javascript/CBResponsiveEditorFactory.css'];
    }

    /**
     * @return [string]
     */
    public static function editorURLsForJavaScript() {
        return [CBSystemURL . '/javascript/CBResponsiveEditorFactory.js'];
    }
}
