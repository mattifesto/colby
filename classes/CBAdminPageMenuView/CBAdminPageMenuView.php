<?php

/**
 * This view is meant to be used directly by all admin page handlers to render
 * the administration menu.
 */
final class CBAdminPageMenuView {

    /**
     * @return null
     */
    static function renderMenu($menu, $selectedMenuItemName, $class) {
        ?>

        <nav class="<?= $class ?>">
            <div class="toggle"><a onclick="this.parentElement.parentElement.classList.toggle('expanded');">menu</a></div>
            <ul>

                <?php

                foreach ($menu as $menuItemName => $menuItem) {
                    $classAttribute = '';

                    if ($menuItemName == $selectedMenuItemName) {
                        $classAttribute = ' class="selected"';
                    }

                    ?>

                    <li <?= $classAttribute ?>>
                         <a href="<?= cbhtml($menuItem->URL) ?>"><?= cbhtml($menuItem->text) ?></a>
                    </li>

                    <?php
                }

                ?>

            </ul>
        </nav>

        <?php
    }

    /**
     * @param object? $model
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

            if (!empty($selectedMenuItemName)) {
                $selectedSubmenuItemName = CBModel::value($model, 'selectedSubmenuItemName');

                switch ($selectedMenuItemName) {
                    case 'help':
                        CBView::render((object)[
                            'className' => 'CBMenuView',
                            'menu' => $CBAdminHelpMenu,
                            'selectedItemName' => $selectedSubmenuItemName,
                        ]);

                        break;

                    default:
                        if (isset($menuModel->{$selectedMenuItemName}->submenu)) {
                            $submenu = $menuModel->{$selectedMenuItemName}->submenu;

                            self::renderMenu($submenu, $selectedSubmenuItemName, 'CBSubmenu');
                        }

                        break;
                }

            }

            ?>

        </section>

        <?php
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [Colby::flexpath(__CLASS__, 'css', cbsysurl())];
    }

    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_toModel(stdClass $spec) {
        return (object)[
            'className' => __CLASS__,
            'selectedMenuItemName' => CBModel::value($spec, 'selectedMenuItemName', '', 'strval'),
            'selectedSubmenuItemName' => CBModel::value($spec, 'selectedSubmenuItemName', '', 'strval'),
        ];
    }
}
