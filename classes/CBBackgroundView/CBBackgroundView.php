<?php

class CBBackgroundView extends CBView
{
    /**
     * @return instance type
     */
    public static function init()
    {
        $view = parent::init();

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
