<?php

final class CBTextView {

    /**
     * @return void
     */
    public static function includeEditorDependencies() {
        CBView::includeEditorDependencies();

        CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/classes/CBTextView/CBTextViewEditor.js');
    }

    /**
     * @return string
     */
    public static function modelToSearchText(stdClass $model = null) {
        if (isset($model->text)) {
            return $model->text;
        }

        return '';
    }

    /**
     * @return void
     */
    public static function renderModelAsHTML(stdClass $model = null) {
        echo "<span class=\"CBTextView\">{$model->HTML}</span>";
    }

    /**
     * @return stdClass
     */
    public static function specToModel(stdClass $spec = null) {
        $model          = CBView::modelWithClassName(__CLASS__);
        $model->text    = isset($spec->text) ? (string)$spec->text : '';
        $model->HTML    = self::textToHTML($model->text);

        return $model;
    }
}
