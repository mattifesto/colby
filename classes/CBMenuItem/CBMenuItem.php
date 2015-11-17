<?php

final class CBMenuItem {

    /**
     * @return [{string}]
     */
    public static function editorURLsForCSS() {
        return [
            CBMenuItem::URL('CBMenuItemEditor.css')
        ];
    }

    /**
     * @return [{string}]
     */
    public static function editorURLsForJavaScript() {
        return [
            CBSystemURL . '/javascript/CBStringEditorFactory.js',
            CBMenuItem::URL('CBMenuItemEditorFactory.js')
        ];
    }

    /**
     * @return {stdClass}
     */
    public static function specToModel(stdClass $spec) {
        $model              = CBModels::modelWithClassName(__CLASS__);
        $model->name        = isset($spec->name) ? ColbyConvert::textToStub($spec->name) : '';
        $model->text        = isset($spec->text) ? (string)$spec->text : '';
        $model->textAsHTML  = ColbyConvert::textToHTML($model->text);
        $model->URL         = isset($spec->URL) ? trim($spec->URL) : '';
        $model->URLAsHTML   = ColbyConvert::textToHTML($model->URL);

        return $model;
    }

    /**
     * @return {string}
     */
    public static function URL($filename) {
        return CBSystemURL . "/classes/CBMenuItem/{$filename}";
    }
}
