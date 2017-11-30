<?php

/**
 * This view is meant to be used directly by all admin page handlers to render
 * the administration menu.
 */
final class CBAdminPageMenuView {

    const helpMenuID = '62eeeabc11366b92bf22017903bffb1fead31764';

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $helpMenuSpec = CBModels::fetchSpecByID(CBAdminPageMenuView::helpMenuID);

        if ($helpMenuSpec === false) {
            $helpMenuSpec = (object)[
                'ID' => CBAdminPageMenuView::helpMenuID,
            ];
        }

        $helpMenuSpec->className = 'CBMenu';
        $helpMenuSpec->title = 'Help';
        $helpMenuSpec->titleURI = '/admin/help/title-description/';
        $helpMenuSpec->items = [
            (object)[
                'className' => 'CBMenuItem',
                'name' => 'title-description',
                'text' => 'Titles & Descriptions',
                'URL' => '/admin/help/title-description/',
            ],
            (object)[
                'className' => 'CBMenuItem',
                'name' => 'caption-alternative-text',
                'text' => 'Captions & Alternative Text',
                'URL' => '/admin/help/caption-alternative-text',
            ],
            (object)[
                'className' => 'CBMenuItem',
                'name' => 'CBArtworkElement',
                'text' => 'CBArtworkElement',
                'URL' => '/admin/page/?class=CBAdminPageForCBArtworkElement',
            ],
            (object)[
                'className' => 'CBMenuItem',
                'name' => 'cssvariables',
                'text' => 'CSS Variables',
                'URL' => '/admin/page/?class=CBAdminPageForCSSVariables'
            ],
        ];

        $allClassNames = CBAdmin::fetchClassNames();

        foreach ($allClassNames as $className) {
            if (is_callable($function = "{$className}::CBAdmin_menuItems")) {
                $menuItemData = call_user_func($function);

                foreach ($menuItemData as $menuItemDatum) {
                    switch ($menuItemDatum->mainMenuItemName) {
                        case 'help':
                            $helpMenuSpec->items[] = $menuItemDatum->menuItem;
                            break;

                        default:
                            break;
                    }
                }
            }
        }

        CBDB::transaction(function () use ($helpMenuSpec) {
            CBModels::save($helpMenuSpec);
        });
    }

    /**
     * @return [string]
     */
    static function CBInstall_requireClassNames(): array {
        return ['CBModels'];
    }

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
                            'menuID' => CBAdminPageMenuView::helpMenuID,
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
