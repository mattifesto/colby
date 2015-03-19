<?php

/**
 * This view is meant to be used directly by all admin page handlers to render
 * the standard administration page footer.
 */
final class CBAdminPageFooterView {

    /**
     * @deprecated
     *
     * @return stdClass
     */
    public static function init() {
        $view           = new self();
        $view->model    = CBView::modelWithClassName(__CLASS__);

        return $view;
    }

    /**
     * @note functional programming
     *
     * @return void
     */
    public static function renderModelAsHTML(stdClass $model = null) {
        include __DIR__ . '/CBAdminPageFooterViewHTML.php';
    }

    /**
     * @deprecated
     *
     * @return void
     */
    public function renderHTML() {
        self::renderModelAsHTML($this->model);
    }
}
