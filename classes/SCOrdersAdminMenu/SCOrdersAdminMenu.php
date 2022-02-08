<?php

final class
SCOrdersAdminMenu {

    /* -- CBInstall interfaces -- */



    /**
     * @return void
     */
    static function
    CBInstall_install(
    ): void {
        /* add item to main admin menu */

        $mainAdminMenuSpec = CBModels::fetchSpecByID(
            CBAdminMenu::ID()
        );

        $mainAdminMenuItemSpecs = CBMenu::getMenuItems(
            $mainAdminMenuSpec
        );

        array_push(
            $mainAdminMenuItemSpecs,
            (object)[
                'className' => 'CBMenuItem',
                'name' => 'orders',
                'submenuID' => SCOrdersAdminMenu::getModelCBID(),
                'text' => 'Orders',
                'URL' => CBAdmin::getAdminPageURL(
                    'CB_CBAdmin_NewOrders'
                )
            ]
        );

        CBMenu::setMenuItems(
            $mainAdminMenuSpec,
            $mainAdminMenuItemSpecs
        );


        /* create orders admin menu */

        $ordersAdminMenuSpec = (object)[
            'className' => 'CBMenu',
            'ID' => SCOrdersAdminMenu::getModelCBID(),
            'title' => 'Orders',
            'titleURI' => CBAdmin::getAdminPageURL(
                'CB_CBAdmin_NewOrders'
            ),
        ];


        /* save */

        CBDB::transaction(
            function () use (
                $mainAdminMenuSpec,
                $ordersAdminMenuSpec
            ) {
                CBModels::save(
                    $mainAdminMenuSpec
                );

                CBModels::deleteByID(
                    SCOrdersAdminMenu::getModelCBID()
                );

                CBModels::save(
                    $ordersAdminMenuSpec
                );
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



    /* -- functions -- */



    /**
     * @return CBID
     */
    static function
    getModelCBID(
    ): string {
        return 'dfb7b34446c44bf94adafe5b6854583300466e8d';
    }
    /* getModelCBID() */



    /**
     * @deprecated 2020_03_06
     *
     *      Use SCOrdersAdminMenu::getModelCBID().
     */
    static function
    ID(
    ): string {
        return SCOrdersAdminMenu::getModelCBID();
    }
    /* ID() */

}
