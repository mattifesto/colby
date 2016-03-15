<?php

final class CBPagesAdministrationView {

    /**
     * @return [string|null]
     */
    private static function fetchExistingPageKinds() {
        return CBDB::SQLToArray('SELECT DISTINCT `classNameForKind` FROM `ColbyPages`');
    }

    /**
     * @return [string]
     */
    public static function requiredClassNames() {
        return ['CBUI', 'CBUINavigationView', 'CBUISelector', 'CBUIStringEditor'];
    }

    /**
     * @return [string]
     */
    public static function requiredCSSURLs() {
        return [
            CBSystemURL . '/javascript/CBPageList.css',
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
     * @return [[string, mixed]]
     */
    public static function requiredJavaScriptVariables() {
        return [
            ['CBPagesClassNamesForKinds', CBPagesAdministrationView::fetchExistingPageKinds()]
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
