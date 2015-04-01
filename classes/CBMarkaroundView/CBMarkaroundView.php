<?php

/**
 * 2015.04.01
 * This class is basically deprecated. It should be removed once it's known to
 * be safe to remove it. The idea was that this would be a sort of test
 * scenario for the markaround parser but the problem is that it's not actually
 * useful as a view which makes it very misleading.
 */
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
        return $model->markaround;
    }

    /**
     * @return void
     */
    public static function renderModelAsHTML(stdClass $model = null) {
        echo "<section class=\"CBMarkaroundView\">{$model->HTML}</section>";
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
