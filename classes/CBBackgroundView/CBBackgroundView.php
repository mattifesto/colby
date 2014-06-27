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
        CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/classes/CBBackgroundView/CBBackgroundViewEditor.js');
    }
}
