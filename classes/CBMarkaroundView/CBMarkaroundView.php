<?php

class CBMarkaroundView extends CBView {

    /**
     * @return instance type
     */
    public static function init() {

        $view   = parent::init();
        $model  = $view->model;

        $model->markaround  = '';
        $model->HTML        = '';

        return $view;
    }

    /**
     * @return void
     */
    public static function includeEditorDependencies() {

        CBHTMLOutput::addCSSURL(CBSystemURL . '/classes/CBMarkaroundView/CBMarkaroundViewEditor.css');
        CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/classes/CBView/CBViewEditor.js');
        CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/classes/CBMarkaroundView/CBMarkaroundViewEditor.js');
    }

    /**
     * @return void
     */
    public function renderHTML() {

        include __DIR__ . '/CBMarkaroundViewHTML.php';
    }
}
