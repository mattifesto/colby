<?php

/**
 * This view is used by admin pages to create a table containing a list of
 * pages. For instance, a list of pages available for editing.
 */
final class CBPagesListView {

    /**
     * @return void
     */
    public static function renderModelAsHTML(stdClass $model = null)
    {
        $baseURL = CBSystemURL . '/classes/CBPagesListView';

        CBHTMLOutput::addCSSURL("{$baseURL}/CBPagesListViewHTML.css");
        CBHTMLOutput::addJavaScriptURL("{$baseURL}/CBPagesListViewController.js");

        include __DIR__ . '/CBPagesListViewHTML.php';
    }
}
