<?php

/**
 * 2015.03.19
 * This class was intented to be used as a base class for container views. The
 * paradigm has changed so that each view class stands on its own so this class
 * is now more of an example of how to implement a container view. This class
 * may be deprecated and eventually removed if it doesn't provide any useful
 * benefit.
 */
final class CBContainerView {

    /**
     * @return void
     */
    public static function includeEditorDependencies() {
        CBView::includeEditorDependencies();

        CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/classes/CBContainerView/CBContainerViewEditor.js');
    }

    /**
     * @return string
     */
    public static function modelToSearchText(stdClass $model = null) {
        if (isset($model->subviewModels)) {
            $text = array_map('CBView::modelToSearchText', $model->subviewModels);

            return implode(' ', $text);
        }

        return '';
    }

    /**
     * @return void
     */
    public static function renderModelAsHTML(stdClass $model = null) {
        echo '<div class="CBContainerView">';

        if (isset($model->subviewModels)) {
            array_walk($model->subviewModels, 'CBView::renderModelAsHTML');
        }

        echo '</div>';
    }

    /**
     * @return stdClass
     */
    public static function specToModel(stdClass $spec = null) {
        $model                  = CBView::modelWithClassName(__CLASS__);
        $subviewModels          = isset($spec->subviewModels) ? $spec->subviewModels : [];
        $model->subviewModels   = array_map('CBView::specToModel', $spec->subviewModels);

        return $model;
    }
}
