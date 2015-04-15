<?php

final class CBTextView {

    /**
     * @return void
     */
    public static function includeEditorDependencies() {
        CBView::includeEditorDependencies();

        CBHTMLOutput::addCSSURL(self::URL('CBTextViewEditor.css'));
        CBHTMLOutput::addJavaScriptURL(self::URL('CBTextViewEditorFactory.js'));
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
        $model->HTML    = ColbyConvert::textToHTML($model->text);

        return $model;
    }

    /**
     * @return string
     */
    private static function URL($filename) {
        return CBSystemURL . "/classes/CBTextView/{$filename}";
    }
}
