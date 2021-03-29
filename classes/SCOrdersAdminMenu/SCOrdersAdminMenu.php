<?php

final class SCOrdersAdminMenu {

    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $adminMenuSpec = CBModels::fetchSpecByID(
            CBAdminMenu::ID()
        );

        $adminMenuSpec->items[] = (object)[
            'className' => 'CBMenuItem',
            'name' => 'orders',
            'submenuID' => SCOrdersAdminMenu::getModelCBID(),
            'text' => 'Orders',
            'URL' => '/admin/orders/new/',
        ];

        $spec = (object)[
            'className' => 'CBMenu',
            'ID' => SCOrdersAdminMenu::getModelCBID(),
            'title' => 'Orders',
            'titleURI' => '/admin/orders/new/',
            'items' => [
                (object)[
                    'className' => 'CBMenuItem',
                    'name' => 'new',
                    'text' => 'New',
                    'URL' => '/admin/orders/new/',
                ],
                (object)[
                    'className' => 'CBMenuItem',
                    'name' => 'archived',
                    'text' => 'Archived',
                    'URL' => '/admin/orders/archived/',
                ],
            ],
        ];

        CBDB::transaction(
            function () use ($adminMenuSpec, $spec) {
                CBModels::save($adminMenuSpec);

                CBModels::deleteByID(
                    SCOrdersAdminMenu::getModelCBID()
                );

                CBModels::save($spec);
            }
        );
    }
    /* CBInstall_install() */



    /**
     * @return [string]
     */
    static function
    CBInstall_requiredClassNames(
    ): array {
        return [
            'CBModelsAdminMenu'
        ];
    }
    /* CBInstall_requiredClassNames() */



    /* -- functions -- -- -- -- -- */



    /**
     * @return CBID
     */
    static function getModelCBID(): string {
        return 'dfb7b34446c44bf94adafe5b6854583300466e8d';
    }



    /**
     * @deprecated 2020_03_06
     *
     *      Use SCOrdersAdminMenu::getModelCBID().
     */
    static function ID(): string {
        return SCOrdersAdminMenu::getModelCBID();
    }

}
