<?php

/**
 * This view is meant to be used directly by all admin page handlers to render
 * the administration menu.
 */
final class CBAdminPageMenuView {

    /**
     * @deprecated
     *
     * @return instance type
     */
    public static function init() {
        $view                                   = new self();
        $view->model                            = CBView::modelWithClassName(__CLASS__);
        $view->model->selectedMenuItemName      = null;
        $view->model->selectedSubmenuItemName   = null;

        return $view;
    }

    /**
     * @deprecated
     *
     * @return void
     */
    public function renderHTML() {
        self::renderModelAsHTML($this->model);
    }

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
     * @note functional programming
     *
     * @return void
     */
    public static function renderModelAsHTML($model) {

        CBHTMLOutput::addCSSURL('https://fonts.googleapis.com/css?family=Source+Sans+Pro:400');
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
     * @deprecated
     *
     * @return void
     */
    public function setSelectedMenuItemName($selectedMenuItemName)
    {
        $this->model->selectedMenuItemName = $selectedMenuItemName;
    }

    /**
     * @deprecated
     *
     * @return void
     */
    public function setSelectedSubmenuItemName($selectedSubmenuItemName)
    {
        $this->model->selectedSubmenuItemName = $selectedSubmenuItemName;
    }
}
