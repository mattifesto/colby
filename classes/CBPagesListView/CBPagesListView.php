<?php

/**
 * This view is used by admin pages to create a table containing a list of
 * pages. For instance, a list of pages available for editing.
 */
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
