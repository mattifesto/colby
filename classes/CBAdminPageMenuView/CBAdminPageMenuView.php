<?php

/**
 * This view is meant to be used directly by all admin page handlers to render
 * the administration menu.
 */
final class CBAdminPageMenuView {

    /**
     * @return void
     */
    static function renderMenu($menu, $selectedMenuItemName, $class) {

        ?>

        <nav class="<?= $class ?>">
            <div class="toggle"><a onclick="this.parentElement.parentElement.classList.toggle('expanded');">menu</a></div>
            <ul>

        <?php

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
     * @param stdClass? $model
     * @param string? $model->selectedMenuItemName
     * @param string? $model->selectedSubmenuItemName
     *
     * @return null
     */
    static function CBView_render(stdClass $model = null) {

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

        ?>

        <section class="CBAdminPageMenuView">

            <?php

            $selectedMenuItemName = CBModel::value($model, 'selectedMenuItemName');

            self::renderMenu($menuModel, $selectedMenuItemName, 'CBMenu');

            if (!empty($selectedMenuItemName) &&
                isset($menuModel->{$selectedMenuItemName}->submenu))
            {
                $submenu = $menuModel->{$selectedMenuItemName}->submenu;
                $selectedSubmenuItemName = CBModel::value($model, 'selectedSubmenuItemName');

                self::renderMenu($submenu, $selectedSubmenuItemName, 'CBSubmenu');
            }

            ?>

        </section>

        <?php
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [Colby::flexnameForCSSForClass(CBSystemURL, __CLASS__)];
    }

    /**
     * @param stdClass $spec
     *
     * @return stdClass
     */
    static function CBModel_toModel(stdClass $spec = null) {
        $model                          = CBView::modelWithClassName(__CLASS__);
        $model->selectedMenuItemName    = isset($spec->selectedMenuItemName) ?
                                            (string)$spec->selectedMenuItemName : '';
        $model->selectedSubmenuItemName = isset($spec->selectedSubmenuItemName) ?
                                            (string)$spec->selectedSubmenuItemName : '';

        return $model;
    }
}
