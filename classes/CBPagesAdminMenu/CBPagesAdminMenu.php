<?php

final class CBPagesAdminMenu {

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $adminMenuSpec = CBModels::fetchSpecByID(CBAdminMenu::ID());

        $adminMenuSpec->items[] = (object)[
            'className' => 'CBMenuItem',
            'name' => 'pages',
            'submenuID' => CBPagesAdminMenu::ID(),
            'text' => 'Pages',
            'URL' => '/admin/page/?class=CBAdminPageForPagesFind',
        ];

        $pagesAdminMenuSpec = (object)[
            'className' => 'CBMenu',
            'ID' => CBPagesAdminMenu::ID(),
            'title' => 'Pages',
            'titleURI' => '/admin/page/?class=CBAdminPageForPagesFind',
            'items' => [
                (object)[
                    'className' => 'CBMenuItem',
                    'name' => 'create',
                    'text' => 'Create',
                    'URL' => '/admin/?c=CBModelsAdminTemplateSelector&modelClassName=CBViewPage',
                ],
            ],
        ];

        CBDB::transaction(function () use ($adminMenuSpec, $pagesAdminMenuSpec) {
            CBModels::save($adminMenuSpec);
            CBModels::deleteByID(CBPagesAdminMenu::ID());
            CBModels::save($pagesAdminMenuSpec);
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

    /**
     * @return ID
     */
    static function ID(): string {
        return '8a2344fe85d9224f6d5f8964a7d0e350fcc6985b';
    }
}
