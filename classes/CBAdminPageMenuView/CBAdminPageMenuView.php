<?php

/**
 * This view is meant to be used directly by all admin page handlers to render
 * the administration menu.
 */
final class CBAdminPageMenuView {

    /**
     * @param model $spec
     *
     * @return ?model
     */
    static function CBModel_build(stdClass $spec): ?stdClass {
        return (object)[
            'selectedMenuItemName' => CBModel::value($spec, 'selectedMenuItemName', '', 'strval'),
            'selectedSubmenuItemName' => CBModel::value($spec, 'selectedSubmenuItemName', '', 'strval'),
        ];
    }

    /**
     * @param object $model
     *
     *      {
     *          selectedMenuItemName: string?
     *          selectedSubmenuItemName: string?
     *      }
     *
     * @return void
     */
    static function CBView_render(stdClass $model): void {
        echo '<div class="CBAdminPageMenuView">';

        $selectedMenuItemName = CBModel::value($model, 'selectedMenuItemName');
        $adminMenu = CBModels::fetchModelByID(CBAdminMenu::ID);

        CBView::render((object)[
            'className' => 'CBMenuView',
            'CSSClassNames' => ['CBDarkTheme'],
            'menu' => $adminMenu,
            'selectedItemName' => $selectedMenuItemName,
        ]);

        $selectedMenuItem = CBMenu::selectedMenuItem($adminMenu, $selectedMenuItemName);
        $submenuID = CBModel::valueAsID($selectedMenuItem, 'submenuID');

        if ($submenuID) {
            $selectedSubmenuItemName = CBModel::value($model, 'selectedSubmenuItemName');

            CBView::render((object)[
                'className' => 'CBMenuView',
                /* 'CSSClassNames' => ['CBLightTheme'], */
                'menuID' => $submenuID,
                'selectedItemName' => $selectedSubmenuItemName,
            ]);
        }

        echo '</div>';

        return;
    }
}
