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
        return (object)[];
    }

    /**
     * @param model $model
     *
     * @return void
     */
    static function CBView_render(stdClass $model): void {
        $selectedMenuItemNames = CBModel::valueToArray(
            CBHTMLOutput::pageInformation(),
            'selectedMenuItemNames'
        );

        echo '<div class="CBAdminPageMenuView">';

        $menuIndex = 0;
        $menuID = CBAdminMenu::ID();

        while ($menuID) {
            $menuModel = CBModelCache::fetchModelByID($menuID);
            $selectedMenuItemName = $selectedMenuItemNames[$menuIndex] ?? '';

            CBView::render((object)[
                'className' => 'CBMenuView',
                'CSSClassNames' => ($menuIndex == 0) ? ['CBDarkTheme'] : [],
                'menu' => $menuModel,
                'selectedItemName' => $selectedMenuItemName,
            ]);

            $selectedMenuItem = CBMenu::selectedMenuItem($menuModel, $selectedMenuItemName);
            $menuID = CBModel::valueAsID($selectedMenuItem, 'submenuID');
            $menuIndex += 1;
        }

        echo '</div>';

        return;
    }
}
