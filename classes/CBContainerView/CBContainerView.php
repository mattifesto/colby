<?php

class CBContainerView extends CBView {

    protected $subviews;

    /**
     * @return instance type
     */
    public static function init() {

        $view                       = parent::init();
        $view->model->subviewModels = array();
        $view->subviews             = array();

        return $view;
    }

    /**
     * @return instance type
     */
    public static function initWithModel($model) {

        $view           = parent::initWithModel($model);
        $view->subviews = array();

        foreach ($view->model->subviewModels as $subviewModel) {

            $view->subviews[] = CBView::createViewWithModel($subviewModel);
        }

        return $view;
    }

    /**
     * @return void
     */
    public function addSubview(CBView $view) {

        $this->model->subviewModels[] = $view->model();
    }

    /**
     * @return stdClass
     */
    public static function compileSpecificationModelToRenderModel($specificationModel) {
        $r                  = new stdClass();
        $r->className       = $specificationModel->className;
        $r->subviewModels   = array_map('CBView::compile', $specificationModel->subviewModels);

        return $r;
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

        foreach ($this->subviews as $subview) {

            $subview->renderHTML();
        }
    }
}
