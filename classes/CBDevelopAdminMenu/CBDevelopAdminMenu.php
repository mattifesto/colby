<?php

final class CBDevelopAdminMenu {

    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $adminMenuSpec = CBModels::fetchSpecByID(
            CBAdminMenu::getModelCBID()
        );

        $adminMenuSpec->items[] = (object)[
            'className' => 'CBMenuItem',

            'name' => 'develop',

            'submenuID' => CBDevelopAdminMenu::getModelCBID(),

            'text' => 'Develop',

            'URL' => CBAdmin::getAdminPageURL(
                'CBAdminPageForUpdate'
            ),
        ];

        $developAdminMenuSpec = (object)[
            'className' => 'CBMenu',

            'ID' => CBDevelopAdminMenu::getModelCBID(),

            'title' => 'Develop',

            'titleURI' => CBAdmin::getAdminPageURL(
                'CBAdminPageForUpdate'
            ),
        ];

        CBDB::transaction(
            function () use ($adminMenuSpec, $developAdminMenuSpec) {
                CBModels::save($adminMenuSpec);

                CBModels::deleteByID(
                    CBDevelopAdminMenu::getModelCBID()
                );

                CBModels::save($developAdminMenuSpec);
            }
        );
    }
    /* CBInstall_install() */



    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBAdminMenu',
            'CBHelpAdminMenu',
        ];
    }



    /* -- functions -- -- -- -- -- */



    /**
     * @return CBID
     */
    static function getModelCBID(): string {
        return '8ba9210a82d3ab8f181dd7ac9619e06be65f930d';
    }



    /**
     * @deprecated use getModelCBID()
     */
    static function ID(): string {
        return CBDevelopAdminMenu::getModelCBID();
    }

}
