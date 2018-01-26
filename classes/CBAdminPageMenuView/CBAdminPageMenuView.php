<?php

/**
 * This view is meant to be used directly by all admin page handlers to render
 * the administration menu.
 */
final class CBAdminPageMenuView {

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        /*
        $helpMenuSpec = CBModels::fetchSpecByID(CBAdminPageMenuView::helpMenuID);

        if ($helpMenuSpec === false) {
            $helpMenuSpec = (object)[
                'ID' => CBAdminPageMenuView::helpMenuID,
            ];
        }

        $helpMenuSpec->className = 'CBMenu';
        $helpMenuSpec->title = 'Help';
        $helpMenuSpec->titleURI = '/admin/?c=CBDocumentation&p=TitlesAndDescriptions';
        $helpMenuSpec->items = [
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
        */
    }

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return ['CBModels'];
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

        CBView::render((object)[
            'className' => 'CBMenuView',
            'CSSClassNames' => ['CBDarkTheme'],
            'menuID' => CBAdminMenu::ID,
            'selectedItemName' => $selectedMenuItemName,
        ]);

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

        if ($submenuID) {
            $selectedSubmenuItemName = CBModel::value($model, 'selectedSubmenuItemName');

            CBView::render((object)[
                'className' => 'CBMenuView',
                'CSSClassNames' => ['CBLightTheme'],
                'menuID' => $submenuID,
                'selectedItemName' => $selectedSubmenuItemName,
            ]);
        }

        echo '</div>';

        return;
    }
}
