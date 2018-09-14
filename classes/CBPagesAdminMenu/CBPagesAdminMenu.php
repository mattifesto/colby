<?php

final class CBPagesAdminMenu {

    const ID = '8a2344fe85d9224f6d5f8964a7d0e350fcc6985b';

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $adminMenuSpec = CBModels::fetchSpecByID(CBAdminMenu::ID());

        $adminMenuSpec->items[] = (object)[
            'className' => 'CBMenuItem',
            'name' => 'pages',
            'submenuID' => CBPagesAdminMenu::ID,
            'text' => 'Pages',
            'URL' => '/admin/page/?class=CBAdminPageForPagesFind',
        ];

        $spec = (object)[
            'className' => 'CBMenu',
            'ID' => CBPagesAdminMenu::ID,
            'title' => 'Pages',
            'titleURI' => '/admin/page/?class=CBAdminPageForPagesFind',
            'items' => [
                (object)[
                    'className' => 'CBMenuItem',
                    'name' => 'create',
                    'text' => 'Create',
                    'URL' => '/admin/?c=CBModelsAdminTemplateSelector&modelClassName=CBViewPage',
                ],
                (object)[
                    'className' => 'CBMenuItem',
                    'name' => 'find',
                    'text' => 'Find',
                    'URL' => '/admin/page/?class=CBAdminPageForPagesFind',
                ],
            ],
        ];

        CBDB::transaction(function () use ($adminMenuSpec, $spec) {
            CBModels::save($adminMenuSpec);
            CBModels::deleteByID(CBPagesAdminMenu::ID);
            CBModels::save($spec);
        });
    }

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBAdminMenu',
            'CBGeneralAdminMenu',
        ];
    }
}
