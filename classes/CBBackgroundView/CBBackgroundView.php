<?php

final class CBBackgroundView {

    protected $subviews;

    /**
     * @return instance type
     */
    public static function init()
    {
        $view                                   = new self();
        $view->subviews                         = array();

        $model                                  = CBView::modelWithClassName(__CLASS__);
        $model->children                        = array();
        $model->color                           = null;
        $model->colorHTML                       = null;
        $model->imageHeight                     = null;
        $model->imageShouldRepeatHorizontally   = false;
        $model->imageShouldRepeatVertically     = false;
        $model->imageURL                        = null;
        $model->imageURLHTML                    = null;
        $model->imageWidth                      = null;
        $model->linkURL                         = null;
        $model->linkURLHTML                     = null;
        $model->minimumViewHeightIsImageHeight  = true;

        $view->model                            = $model;

        return $view;
    }

    /**
     * @return instance type
     */
    public static function initWithModel($model) {

        $view           = new self();
        $view->model    = $model;
        $view->subviews = array();

        foreach ($view->model->children as $subviewModel) {
            $view->subviews[] = CBView::createViewWithModel($subviewModel);
        }

        return $view;
    }

    /**
     * @return void
     */
    public static function includeEditorDependencies()
    {
        CBHTMLOutput::addCSSURL(CBSystemURL . '/classes/CBBackgroundView/CBBackgroundViewEditor.css');
        CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/classes/CBView/CBViewEditor.js');
        CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/classes/CBBackgroundView/CBBackgroundViewEditor.js');
    }

    /**
     * @note functional programming
     *
     * @return string
     */
    public static function modelToSearchText(stdClass $model = null) {
        if (isset($model->children)) {
            $text = array_map('CBView::modelToSearchText', $model->children);

            return implode(' ', $text);
        }

        return '';
    }

    /**
     * @return void
     */
    public function renderHTML()
    {
        include __DIR__ . '/CBBackgroundViewHTML.php';
    }
}
