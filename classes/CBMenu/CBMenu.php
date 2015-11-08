<?php

final class CBMenu {

    /**
     * @return [{string}]
     */
    public static function editorURLsForCSS() {
        return array_merge(
            [
                CBSystemURL . '/javascript/CBSpecArrayEditor.css',
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
     * @return {string}
     */
    public static function info() {
        return CBModelClassInfo::specToModel((object)[
            'pluralTitle' => 'Menus',
            'singularTitle' => 'Menu'
        ]);
    }

    /**
     * @return {stdClass}
     */
    public static function specToModel(stdClass $spec) {
        $model          = CBModels::modelWithClassName(__CLASS__);
        $model->title   = isset($spec->title) ? (string)$spec->title : '';
        $model->items   = isset($spec->items) ? array_map('CBMenuItem::specToModel', $spec->items) : [];

        return $model;
    }

    /**
     * @return {string}
     */
    public static function URL($filename) {
        return CBSystemURL . "/classes/CBMenu/{$filename}";
    }
}
