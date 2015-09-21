<?php

/**
 * This view is meant to be used directly by all admin page handlers to render
 * the administration menu.
 */
final class CBAdminPageMenuView {

    /**
     * @return void
     */
    public static function renderMenu($menu, $selectedMenuItemName, $class)
    {
        echo "\n\n<nav class=\"{$class}\"><ul>";

        foreach ($menu as $menuItemName => $menuItem)
        {
            $classAttribute = '';

            if ($menuItemName == $selectedMenuItemName)
            {
                $classAttribute = ' class="selected"';
            }

            echo "<li{$classAttribute}>",
                 "<a href=\"{$menuItem->URI}\">{$menuItem->nameHTML}</a>",
                 "</li>";
        }

        echo "</ul></nav>\n\n";
    }

    /**
     * @param stdClass $model
     *
     * @return void
     */
    public static function renderModelAsHTML(stdClass $model = null) {
        CBHTMLOutput::addCSSURL(CBSystemURL . '/classes/CBAdminPageMenuView/CBAdminPageMenuViewHTML.css');

        /**
         * The `Colby::findFile` function is used so that the website can
         * override the file to include its own administrative menu options.
         */

        include_once Colby::findFile('snippets/menu-items-admin.php');

        /**
         * 2015.03.18
         * While moving this view to the latest API paradigm I notice that the
         * use of a global variable here is somewhat clunky. This will need to
         * be changed eventually.
         */

        global $CBAdminMenu;
        $menuModel = $CBAdminMenu;

        include __DIR__ . '/CBAdminPageMenuViewHTML.php';
    }

    /**
     * @param stdClass $spec
     *
     * @return stdClass
     */
    public static function specToModel(stdClass $spec = null) {
        $model                          = CBView::modelWithClassName(__CLASS__);
        $model->selectedMenuItemName    = isset($spec->selectedMenuItemName) ?
                                            (string)$spec->selectedMenuItemName : '';
        $model->selectedSubmenuItemName = isset($spec->selectedSubmenuItemName) ?
                                            (string)$spec->selectedSubmenuItemName : '';

        return $model;
    }
}
