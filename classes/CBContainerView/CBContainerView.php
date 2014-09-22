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
    public function addSubview(CBView $view) {

        $this->model->subviewModels[] = $view->model();
    }

    /**
     * @return void
     */
    public static function includeEditorDependencies() {

        parent::includeEditorDependencies();

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
