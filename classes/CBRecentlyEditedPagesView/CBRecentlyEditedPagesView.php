<?php

final class CBRecentlyEditedPagesView {

    /**
     * @return void
     */
    public static function renderModelAsHTML(stdClass $model = null) {
        $URL = CBSystemURL . '/classes/CBRecentlyEditedPagesView/CBRecentlyEditedPagesViewController.js';
        CBHTMLOutput::addJavaScriptURL($URL);

        $URL = CBSystemURL . '/javascript/CBDelayTimer.js';
        CBHTMLOutput::addJavaScriptURL($URL);

        echo '<div class="CBRecentlyEditedPagesView">';

        CBPagesListView::renderModelAsHTML();

        echo '</div>';
    }
}
