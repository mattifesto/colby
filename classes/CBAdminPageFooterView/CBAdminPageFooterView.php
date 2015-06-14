<?php

/**
 * This view is meant to be used directly by all admin page handlers to render
 * the standard administration page footer.
 */
final class CBAdminPageFooterView {

    /**
     * @return void
     */
    public static function renderModelAsHTML(stdClass $model = null) {
        CBHTMLOutput::addCSSURL('https://fonts.googleapis.com/css?family=Source+Sans+Pro:400');
        CBHTMLOutput::addCSSURL(CBSystemURL . '/classes/CBAdminPageFooterView/CBAdminPageFooterViewHTML.css');

        include __DIR__ . '/CBAdminPageFooterViewHTML.php';
    }
}
