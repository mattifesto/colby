<?php

final class CBContainerView {

    protected $subviews;

    /**
     * @return instance type
     */
    public static function init() {

        $view                       = new self();
        $view->model                = CBView::modelWithClassName(__CLASS__);
        $view->model->subviewModels = array();
        $view->subviews             = array();

        return $view;
    }

    /**
     * @return instance type
     */
    public static function initWithModel($model) {

        $view           = new self();
        $view->model    = $model;
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
        CBView::includeEditorDependencies();

        CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/classes/CBContainerView/CBContainerViewEditor.js');
    }

    /**
     * @note functional programming
     *
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
