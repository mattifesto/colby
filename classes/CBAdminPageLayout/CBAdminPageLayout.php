<?php

/**
 * This class can be used as the customLayoutClassName class for admin pages
 * created using the CBPageLayout layout class.
 */
final class CBAdminPageLayout {

    /**
     * @param string? $properties->selectedMenuItemName
     * @param string? $properties->selectedSubmenuItemName
     *
     * @return null
     */
    static function renderPageHeader(stdClass $properties) {
        CBView::render((object)[
            'className' => 'CBAdminPageMenuView',
            'selectedMenuItemName' => CBModel::value($properties, 'selectedMenuItemName'),
            'selectedSubmenuItemName' => CBModel::value($properties, 'selectedSubmenuItemName'),
        ]);
    }

    /**
     * @param stdClass $properties
     *
     * @return null
     */
    static function renderPageFooter(stdClass $properties) {
        CBView::render((object)[
            'className' => 'CBAdminPageFooterView',
        ]);
    }
}
