<?php

class CBBackgroundView extends CBView
{
    /**
     * @return instance type
     */
    public static function init()
    {
        $view   = parent::init();
        $model  = $view->model;

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
     * @return void
     */
    public function renderHTML()
    {
        include __DIR__ . '/CBBackgroundViewHTML.php';
    }
}
