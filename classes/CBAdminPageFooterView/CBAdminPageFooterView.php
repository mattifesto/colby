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
        include __DIR__ . '/CBAdminPageFooterViewHTML.php';
    }
}
