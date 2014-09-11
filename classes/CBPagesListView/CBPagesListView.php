<?php

class CBPagesListView extends CBView
{
    /**
     * @return void
     */
    public function renderHTML()
    {
        $baseURL = CBSystemURL . '/classes/CBPagesListView';

        CBHTMLOutput::addCSSURL("{$baseURL}/CBPagesListViewHTML.css");
        CBHTMLOutput::addJavaScriptURL("{$baseURL}/CBPagesListViewController.js");

        include __DIR__ . '/CBPagesListViewHTML.php';
    }
}
