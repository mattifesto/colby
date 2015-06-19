<?php

/**
 * This class is responsible for generating a page that will display lists of
 * models that can be edited and lists of classes that can be created.
 */
final class CBAdminPageForModels {

    /**
     * @return null
     */
    public static function renderModelAsHTML() {
        CBHTMLOutput::setTitleHTML('Edit');
        CBHTMLOutput::setDescriptionHTML('Edit models');
        CBHTMLOutput::begin();

        include CBSystemDirectory . '/sections/admin-page-settings.php';

        $spec                           = new stdClass();
        $spec->selectedMenuItemName     = 'edit';
        CBAdminPageMenuView::renderModelAsHTML(CBAdminPageMenuView::specToModel($spec));

        CBAdminPageFooterView::renderModelAsHTML();
        CBHTMLOutput::render();
    }
}
