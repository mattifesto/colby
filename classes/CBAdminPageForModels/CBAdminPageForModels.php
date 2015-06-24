<?php

/**
 * This class is responsible for generating a page that will display lists of
 * models that can be edited and lists of classes that can be created.
 */
final class CBAdminPageForModels {

    /**
     * @deprecated This should be replaced with an editable model or something.
     *
     * @return {array}
     */
    private static function classMenuItems() {
        $model      = CBModels::fetchModelByID(CBModelsPreferences::ID);
        $menuItems  = array_filter($model->classMenuItems, function($menuItem) {
            if (empty($menuItem->group) || ColbyUser::current()->isOneOfThe($menuItem->group)) {
                return true;
            } else {
                return false;
            }
        });

        return $menuItems;
    }

    /**
     * @return {stdClass}
     */
    public static function fetchModelListForAjax() {
        $response                   = new CBAjaxResponse();
        $className                  = $_POST['className'];
        $classNameAsSQL             = CBDB::stringToSQL($className);
        $pageNumber                 = (int)$_POST['pageNumber'];
        $SQL                        = <<<EOT

            SELECT      LOWER(HEX(`ID`)) AS `ID`, `created`, `modified`, `title`
            FROM        `CBModels`
            WHERE       `className` = {$classNameAsSQL}
            ORDER BY    `className`, `modified` DESC
            LIMIT       20

EOT;

        $response->models           = CBDB::SQLToObjects($SQL);
        $response->wasSuccessful    = true;
        $response->send();
    }

    /**
     * @return {stdClass}
     */
    public static function fetchModelListForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @return null
     */
    public static function renderModelAsHTML() {
        if (!ColbyUser::current()->isOneOfThe('Administrators')) {
            return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
        }

        CBHTMLOutput::setTitleHTML('Edit');
        CBHTMLOutput::setDescriptionHTML('Edit models');
        CBHTMLOutput::begin();

        include CBSystemDirectory . '/sections/admin-page-settings.php';

        CBHTMLOutput::addCSSURL(self::URL('CBAdminPageForModels.css'));
        CBHTMLOutput::addJavaScriptURL(self::URL('CBAdminPageForModels.js'));
        CBHTMLOutput::exportVariable('CBClassMenuItems', self::classMenuItems());

        $spec                           = new stdClass();
        $spec->selectedMenuItemName     = 'edit';
        CBAdminPageMenuView::renderModelAsHTML(CBAdminPageMenuView::specToModel($spec));

        echo '<main></main>';

        CBAdminPageFooterView::renderModelAsHTML();
        CBHTMLOutput::render();
    }

    /**
     * @return {string}
     */
    public static function URL($filename) {
        return CBSystemURL . "/classes/CBAdminPageForModels/{$filename}";
    }
}
