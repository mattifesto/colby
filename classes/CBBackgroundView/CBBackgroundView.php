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

        $model->backgroundColor                 = '';
        $model->backgroundColorHTML             = '';
        $model->children                        = array();
        $model->imageFilename                   = null;
        $model->imageShouldRepeatHorizontally   = false;
        $model->imageShouldRepeatVertically     = false;
        $model->imageHeight                     = null;
        $model->imageWidth                      = null;
        $model->linkURL                         = '';
        $model->linkURLHTML                     = '';
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
}
