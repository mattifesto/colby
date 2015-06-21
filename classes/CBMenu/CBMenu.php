<?php

final class CBMenu {

    /**
     * @return [{string}]
     */
    public static function editorURLsForCSS() {
        return [
            CBMenu::URL('CBMenuEditor.css')
        ];
    }

    /**
     * @return [{string}]
     */
    public static function editorURLsForJavaScript() {
        return [
            CBSystemURL . '/javascript/CBStringEditorFactory.js',
            CBMenu::URL('CBMenuEditorFactory.js')
        ];
    }

    /**
     * @return {stdClass}
     */
    public static function specToModel(stdClass $spec) {
        $model          = CBModels::modelWithClassName(__CLASS__);
        $model->title   = isset($spec->title) ? (string)$spec->title : '';

        return $model;
    }

    /**
     * @return {string}
     */
    public static function URL($filename) {
        return CBSystemURL . "/classes/CBMenu/{$filename}";
    }
}
