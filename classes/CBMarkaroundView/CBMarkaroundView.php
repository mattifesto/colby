<?php

final class CBMarkaroundView {

    /**
     * @return void
     */
    public static function includeEditorDependencies() {
        CBView::includeEditorDependencies();

        CBHTMLOutput::addCSSURL(CBSystemURL . '/classes/CBMarkaroundView/CBMarkaroundViewEditor.css');
        CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBMarkaround.js');
        CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/classes/CBView/CBViewEditor.js');
        CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/classes/CBMarkaroundView/CBMarkaroundViewEditor.js');
    }

    /**
     * @return string
     */
    public static function modelToSearchText(stdClass $model = null) {
        return isset($model->markaround) ? $model->markaround : '';
    }

    /**
     * @return void
     */
    public static function renderModelAsHTML(stdClass $model = null) {
        echo '<section class="CBMarkaroundView">';

        if (exists($model->HTML)) {
            echo $model->HTML;
        }

        echo '</section>';
    }

    /**
     * @return stdClass
     */
    public static function specToModel(stdClass $spec = null) {
        $model              = CBView::modelWithClassName(__CLASS__);
        $model->markaround  = isset($spec->markaround) ? $spec->markaround : null;
        $model->HTML        = ColbyConvert::markaroundToHTML($model->markaround);

        return $model;
    }
}
