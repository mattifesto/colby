<?php

final class CBMenu {

    /**
     * @return [{string}]
     */
    public static function editorURLsForCSS() {
        return array_merge(
            [
                CBMenu::URL('CBMenuEditor.css')
            ],
            CBMenuItem::editorURLsForCSS()
        );
    }

    /**
     * @return [{string}]
     */
    public static function editorURLsForJavaScript() {
        return array_merge(
            [
                CBSystemURL . '/javascript/CBSpecArrayEditorFactory.js',
                CBSystemURL . '/javascript/CBStringEditorFactory.js',
                CBMenu::URL('CBMenuEditorFactory.js')
            ],
            CBMenuItem::editorURLsForJavaScript()
        );
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
