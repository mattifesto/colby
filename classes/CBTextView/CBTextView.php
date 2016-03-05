<?php

final class CBTextView {

    /**
     * @return string
     */
    public static function modelToSearchText(stdClass $model = null) {
        return $model->text;
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
     * @param string $filename
     *
     * @return string
     */
    public static function URL($filename) {
        $className = __CLASS__;
        return CBSystemURL . "/classes/{$className}/{$filename}";
    }
}
