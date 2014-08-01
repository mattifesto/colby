<?php

/**
 * This view is meant to be used directly by all admin page handlers to render
 * the standard administration page footer.
 */
class CBAdminPageFooterView extends CBView
{
    /**
     * @return void
     */
    public function renderHTML()
    {
        include __DIR__ . '/CBAdminPageFooterViewHTML.php';
    }
}
