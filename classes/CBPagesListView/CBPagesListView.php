<?php

class CBPagesListView extends CBView
{
    /**
     * @return void
     */
    public function renderHTML()
    {
        CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/classes/CBPagesListView/CBPagesListViewController.js');

        include __DIR__ . '/CBPagesListViewHTML.php';
    }
}
