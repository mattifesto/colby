<?php

final class CBAdminPageForPreferences {

    /**
     * @return null
     */
    public static function renderModelAsHTML(stdClass $model = null) {
        if (!ColbyUser::current()->isOneOfThe('Developers')) {
            return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
        }

        CBHTMLOutput::setTitleHTML('Preferences');
        CBHTMLOutput::setDescriptionHTML('Edit site preferences');
        CBHTMLOutput::begin();

        include CBSystemDirectory . '/sections/admin-page-settings.php';

        CBHTMLOutput::addCSSURL(self::URL('CBAdminPageForPreferences.css'));

        $spec                           = new stdClass();
        $spec->selectedMenuItemName     = 'develop';
        $spec->selectedSubmenuItemName  = 'preferences';
        CBAdminPageMenuView::renderModelAsHTML(CBAdminPageMenuView::specToModel($spec));

        echo '<main><a href="/admin/models/edit/?ID=69b3958b95e87cca628fc2b9cd70f420faf33a0a">Models</a></main>';

        CBAdminPageFooterView::renderModelAsHTML();
        CBHTMLOutput::render();
    }

    /**
     * @return {string}
     */
    public static function URL($filename) {
        return CBSystemURL . "/classes/CBAdminPageForPreferences/{$filename}";
    }
}
