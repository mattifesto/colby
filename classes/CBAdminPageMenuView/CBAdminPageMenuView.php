<?php

/**
 * This view is meant to be used directly by all admin page handlers to render
 * the administration menu.
 */
final class CBAdminPageMenuView {

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
        $submenuID = CBConvert::valueAsHex160(CBModel::value($selectedMenuItem, 'submenuID'));

        if (empty($submenuID)) {
            switch ($selectedMenuItemName) {
                case 'develop':
                    $submenuID = CBDevelopAdminMenu::ID;
                    break;
                case 'general':
                    $submenuID = CBGeneralAdminMenu::ID;
                    break;
                case 'help':
                    $submenuID = CBHelpAdminMenu::ID;
                    break;
                case 'models':
                    $submenuID = CBModelsAdminMenu::ID;
                    break;
                case 'pages':
                    $submenuID = CBPagesAdminMenu::ID;
                    break;
                default:
                    $submenuID = null;
                    break;
            }
        }

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
