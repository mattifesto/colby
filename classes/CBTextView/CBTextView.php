<?php

final class CBTextView {

    /**
     * @return instance type
     */
    public static function init() {
        $view               = new self();
        $view->model        = CBView::modelWithClassName(__CLASS__);
        $view->model->text  = '';
        $view->model->HTML  = '';

        return $view;
    }

    /**
     * 2014.10.14
     *  The `contentType` property was added to the model so this method now
     *  sets it if it to the default value if it doesn't already exists.
     *  This value may not be correct for all existing models but it will
     *  produce the current behavior regardless. The number of models existing
     *  in the wild is very low.
     *
     * @return instance type
     */
    public static function initWithModel($model) {
        $view           = new self();
        $view->model    = $model;

        return $view;
    }

    /**
     * @return string
     */
    public function HTML() {
        return $this->model->HTML;
    }

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
    public function renderHTML() {
        echo "<span class=\"CBTextView\">{$this->model->HTML}</span>";
    }

    /**
     * @return void
     */
    public function setText($text) {
        $this->model->text  = (string)$text;
        $this->model->HTML  = self::textToHTML($this->model->text);
    }

    /**
     * @return stdClass
     */
    public static function specToModel($spec) {
        $model          = CBView::modelWithClassName(__CLASS__);
        $model->text    = isset($spec->text) ? (string)$spec->text : '';
        $model->HTML    = self::textToHTML($model->text);

        return $model;
    }

    /**
     * @return string
     */
    public function text() {
        return $this->model->text;
    }

    /**
     * @return string
     */
    public static function textToHTML($text) {
        return ColbyConvert::textToHTML($text);
    }
}
