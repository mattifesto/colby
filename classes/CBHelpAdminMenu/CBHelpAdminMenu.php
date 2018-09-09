<?php

final class CBHelpAdminMenu {

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $adminMenuSpec = CBModels::fetchSpecByID(CBAdminMenu::ID());

        $adminMenuSpec->items[] = (object)[
            'className' => 'CBMenuItem',
            'name' => 'help',
            'submenuID' => CBHelpAdminMenu::ID(),
            'text' => 'Help',
            'URL' => '/admin/?c=CBTitleAndDescriptionDocumentation',
        ];

        $spec = (object)[
            'className' => 'CBMenu',
            'title' => 'Help',
            'titleURI' => '/admin/?c=CBTitleAndDescriptionDocumentation',
            'ID' => CBHelpAdminMenu::ID(),
            'items' => [],
        ];

        /**
         * @deprecated The following code should be replaced with the pages
         * having and install function that will add menu items to this menu.
         */

        $allClassNames = CBAdmin::fetchClassNames();

        foreach ($allClassNames as $className) {
            if (is_callable($function = "{$className}::CBAdmin_menuItems")) {
                $menuItemData = call_user_func($function);

                foreach ($menuItemData as $menuItemDatum) {
                    switch ($menuItemDatum->mainMenuItemName) {
                        case 'help':
                            $spec->items[] = $menuItemDatum->menuItem;
                            break;

                        default:
                            break;
                    }
                }
            }
        }

        CBDB::transaction(function () use ($adminMenuSpec, $spec) {
            CBModels::save($adminMenuSpec);
            CBModels::deleteByID(CBHelpAdminMenu::ID());
            CBModels::save($spec);
        });
    }

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return ['CBPagesAdminMenu'];
    }

    /**
     * @return hex160
     */
    static function ID(): string {
        return '62eeeabc11366b92bf22017903bffb1fead31764';
    }
}
