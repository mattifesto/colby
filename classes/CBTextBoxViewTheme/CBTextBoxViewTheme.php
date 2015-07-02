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
     * @return null
     */
    public static function modelsWillSave(array $tuples) {
        array_walk($tuples, function($tuple) {
            CBDataStore::makeDirectoryForID($tuple->model->ID);
            $filepath       = CBDataStore::filepath([
                'ID'        => $tuple->model->ID,
                'filename'  => 'theme.css'
            ]);
            file_put_contents($filepath, $tuple->model->styles);
        });
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
        $model                      = CBModels::modelWithClassName(__CLASS__);
        $model->styles              = isset($spec->styles) ? $spec->styles : '';
        $model->URLsForCSS          = isset($spec->URLsForCSS) ? ColbyConvert::textToLines($spec->URLsForCSS) : [];
        $model->URLsForCSS          = array_filter($model->URLsForCSS, function($URL) {
            return !empty(trim($URL));
        });
        $model->URLsForCSSAsHTML    = array_map(function($URL) {
            return ColbyConvert::textToHTML($URL);
        }, $model->URLsForCSS);

        return $model;
    }

    /**
     * @return {string}
     */
    public static function URL($filename) {
        return CBSystemURL . "/classes/CBTextBoxViewTheme/{$filename}";
    }
}
