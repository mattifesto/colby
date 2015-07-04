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
     * @return {stdClass}
     */
    public static function specToModel(stdClass $spec) {
        $model                      = CBModels::modelWithClassName(__CLASS__);
        $model->styles              = isset($spec->styles) ? $spec->styles : '';

        return $model;
    }

    /**
     * @return {string}
     */
    public static function URL($filename) {
        return CBSystemURL . "/classes/CBTextBoxViewTheme/{$filename}";
    }
}
