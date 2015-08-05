<?php

final class CBAdminPageForPreferences {

    const areas = [
        'Site'      => 'CBSitePreferences',
        'Models'    => 'CBModelsPreferences',
        'Pages'     => 'CBPagesPreferences'
    ];

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

        $areaLinks = cb_array_map_assoc(function($name, $class) {
            $ID = $class::ID;
            return "<div><a href=\"/admin/models/edit?ID={$ID}\">{$name}</a></div>";
        }, CBAdminPageForPreferences::areas);

        echo '<main>';
        echo implode("\n", $areaLinks);
        echo '</main>';

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
