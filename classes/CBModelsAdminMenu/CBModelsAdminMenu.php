<?php

final class CBModelsAdminMenu {

    const ID = 'f6a893489fb3ea4bfbc4af9f8cb3f052f8add349';

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $adminMenuSpec = CBModels::fetchSpecByID(CBAdminMenu::ID);

        $adminMenuSpec->items[] = (object)[
            'className' => 'CBMenuItem',
            'name' => 'models',
            'text' => 'Models',
            'URL' => '/admin/?c=CBModelsAdmin',
        ];

        $spec = (object)[
            'className' => 'CBMenu',
            'ID' => CBModelsAdminMenu::ID,
            'title' => 'Models',
            'titleURI' => '/admin/?c=CBModelsAdmin',
            'items' => [
                (object)[
                    'className' => 'CBMenuItem',
                    'name' => 'directory',
                    'text' => 'Directory',
                    'URL' => '/admin/?c=CBModelsAdmin',
                ],
                (object)[
                    'className' => 'CBMenuItem',
                    'name' => 'import',
                    'text' => 'Import',
                    'URL' => '/admin/page/?class=CBAdminPageForModelImport',
                ],
                (object)[
                    'className' => 'CBMenuItem',
                    'name' => 'inspector',
                    'text' => 'Inspector',
                    'URL' => '/admin/?c=CBModelInspector',
                ],
            ],
        ];

        CBDB::transaction(function () use ($adminMenuSpec, $spec) {
            CBModels::save($adminMenuSpec);
            CBModels::deleteByID(CBModelsAdminMenu::ID);
            CBModels::save($spec);
        });
    }

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return ['CBDevelopAdminMenu'];
    }
}
