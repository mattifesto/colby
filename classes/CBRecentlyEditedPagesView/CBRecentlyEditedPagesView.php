<?php

class CBRecentlyEditedPagesView extends CBView
{
    protected $pagesListView;

    /**
     * @return instance type
     */
    public static function init()
    {
        $view                   = parent::init();
        $view->pagesListView    = CBPagesListView::init();

        return $view;
    }

    /**
     * @return void
     */
    public function renderHTML()
    {
        $URL = CBSystemURL . '/classes/CBRecentlyEditedPagesView/CBRecentlyEditedPagesViewController.js';

        CBHTMLOutput::addJavaScriptURL($URL);

        include __DIR__ . '/CBRecentlyEditedPagesViewHTML.php';
    }
}
