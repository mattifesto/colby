<?php

/**
 * This view is meant to be used directly by all admin page handlers to render
 * the administration menu.
 */
class CBAdminPageMenuView extends CBView
{
    private $menuModel;

    /**
     * @return instance type
     */
    public static function init()
    {
        $view                                   = parent::init();
        $view->model->selectedMenuItemName      = null;
        $view->model->selectedSubmenuItemName   = null;

        /**
         * The `Colby::findFile` function is used so that the website can
         * override the file to include its own administrative menu options.
         */

        include_once Colby::findFile('snippets/menu-items-admin.php');

        /**
         * 2014.07.31 TODO
         * In the future the `menu-items-admin.php` snippet will directly
         * modify `$this->menuModel` but currently this file is shared with
         * another implementation so we just assign the global variable it sets.
         */

        global $CBAdminMenu;

        $view->menuModel = $CBAdminMenu;

        return $view;
    }

    /**
     * @return void
     */
    public function renderHTML()
    {
        include __DIR__ . '/CBAdminPageMenuViewHTML.php';
    }

    /**
     * @return void
     */
    public function renderMenu($menu, $selectedMenuItemName, $class)
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
     *
     */
    public function setSelectedMenuItemName($selectedMenuItemName)
    {
        $this->selectedMenuItemName = $selectedMenuItemName;
    }

    /**
     *
     */
    public function setSelectedSubmenuItemName($selectedSubmenuItemName)
    {
        $this->selectedSubmenuItemName = $selectedSubmenuItemName;
    }
}
