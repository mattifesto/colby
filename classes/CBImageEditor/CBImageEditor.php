<?php

final class CBImageEditor {

    /**
     * @return [string]
     */
    public static function editorURLsForCSS() {
        return [CBSystemURL . '/javascript/CBImageEditorFactory.css'];
    }

    /**
     * @return [string]
     */
    public static function editorURLsForJavaScript() {
        return [CBSystemURL . '/javascript/CBImageEditorFactory.js'];
    }
}
