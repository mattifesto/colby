<?php

final class CBModelsAdminMenu {

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
            'name' => 'models',
            'submenuID' => CBModelsAdminMenu::ID(),
            'text' => 'Models',
            'URL' => CBAdmin::getAdminPageURL(
                'Admin_CBModelClassList'
            ),
        ];

        $modelsMenuSpec = (object)[
            'className' => 'CBMenu',
            'ID' => CBModelsAdminMenu::ID(),
            'title' => 'Models',
            'titleURI' => CBAdmin::getAdminPageURL(
                'Admin_CBModelClassList'
            ),
            'items' => [
                (object)[
                    'className' => 'CBMenuItem',
                    'name' => 'inspector',
                    'text' => 'Inspector',
                    'URL' => '/admin/?c=CBModelInspector',
                ],
            ],
        ];

        CBDB::transaction(
            function () use ($adminMenuSpec, $modelsMenuSpec) {
                CBModels::save($adminMenuSpec);

                CBModels::deleteByID(
                    CBModelsAdminMenu::ID()
                );

                CBModels::save($modelsMenuSpec);
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
            'CBDevelopAdminMenu',
        ];
    }
    /* CBInstall_requiredClassNames() */



    /* -- functions -- -- -- -- -- */



    /**
     * @return CBID
     */
    static function getModelCBID(): string {
        return 'f6a893489fb3ea4bfbc4af9f8cb3f052f8add349';
    }



    /**
     * @deprecated use CBModelsAdminMenu::getModelCBID()
     */
    static function ID(): string {
        return CBModelsAdminMenu::getModelCBID();
    }

}
