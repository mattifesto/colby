<?php

final class CBPagesAdministrationView {

    /**
     * @return [string]
     */
    public static function requiredClassNames() {
        return ['CBUI'];
    }

    /**
     * @return [string]
     */
    public static function requiredCSSURLs() {
        return [
            CBSystemURL . '/javascript/CBPageList.css',
            CBPagesAdministrationView::URL('CBPagesAdministrationView.css'),
        ];
    }

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [
            CBSystemURL . '/javascript/CBPageList.js',
            CBPagesAdministrationView::URL('CBPagesAdministrationView.js'),
        ];
    }

    /**
     * @return string
     */
    public static function URL($filename) {
        $className = __CLASS__;
        return CBSystemURL . "/classes/{$className}/{$filename}";
    }
}
