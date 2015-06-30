<?php

final class CBTextBoxViewTheme {

    /**
     * @return [{string}]
     */
    public static function editorURLsForCSS() {
        return [ CBTextBoxViewTheme::URL('CBTextBoxViewThemeEditor.css') ];
    }

    /**
     * @return [{string}]
     */
    public static function editorURLsForJavaScript() {
        return [
            CBSystemURL . '/javascript/CBStringEditorFactory.js',
            CBTextBoxViewTheme::URL('CBTextBoxViewThemeEditorFactory.js')
        ];
    }

    /**
     * NOTE: The styles array will be directly output into HTML and it's
     * important that only trusted users (developers) are allowed to create a
     * CBTextBoxViewTheme. Themes are not intended to be created by any
     * untrusted or unskilled users and certainly not website visitors.
     *
     * @return {stdClass}
     */
    public static function specToModel(stdClass $spec) {
        $model                  = CBModels::modelWithClassName(__CLASS__);
        $model->styles          = isset($spec->styles) ? ColbyConvert::textToLines($spec->styles) : [];
        $model->styles          = array_filter($model->styles, function($style) {
            return preg_match('/^[^{}]*{[^{}]+}\s*$/', $style);
        });

        return $model;
    }

    /**
     * @return {string}
     */
    public static function URL($filename) {
        return CBSystemURL . "/classes/CBTextBoxViewTheme/{$filename}";
    }
}
