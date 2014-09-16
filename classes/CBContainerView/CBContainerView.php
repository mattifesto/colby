<?php

class CBContainerView extends CBView {

    /**
     * @return instance type
     */
    public static function init() {

        $view   = parent::init();
        $model  = $view->model;

        $model->subviewModels = array();

        return $view;
    }

    /**
     * @return void
     */
    public function addSubview($view) {

        /**
         * TODO:
         *  I'm not exactly sure how to implement this. Does this class just
         *  hang onto the model objects of the subviews? I think that would
         *  work.
         */
    }

    /**
     * @return void
     */
    public static function includeEditorDependencies() {

        //CBHTMLOutput::addCSSURL(CBSystemURL . '/classes/CBBackgroundView/CBBackgroundViewEditor.css');
        CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/classes/CBView/CBViewEditor.js');
        CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/classes/CBContainerView/CBContainerViewEditor.js');
    }

    /**
     * @return void
     */
    public function renderHTML() {

        include __DIR__ . '/CBContainerViewHTML.php';
    }

    /**
     * @return void
     */
    protected function renderSubviews() {

        foreach ($this->model->subviewModels as $subviewModel) {

            $viewClassName  = $subviewModel->className;
            $view           = $viewClassName::initWithModel($subviewModel);

            $view->renderHTML();
        }
    }
}
