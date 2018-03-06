<?php

/**
 * This class can be used as the customLayoutClassName class for admin pages
 * created using the CBPageLayout layout class.
 */
final class CBAdminPageLayout {

    /**
     * @param stdClass $properties
     *
     * @return void
     */
    static function renderPageHeader(stdClass $properties): void {
        CBView::render((object)[
            'className' => 'CBAdminPageMenuView',
        ]);
    }

    /**
     * @param stdClass $properties
     *
     * @return void
     */
    static function renderPageFooter(stdClass $properties): void {
        CBView::render((object)[
            'className' => 'CBAdminPageFooterView',
        ]);
    }
}
