<?php

final class CBPagesAdminMenu {

    const ID = '8a2344fe85d9224f6d5f8964a7d0e350fcc6985b';

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $adminMenuSpec = CBModels::fetchSpecByID(CBAdminMenu::ID);

        $adminMenuSpec->items[] = (object)[
            'className' => 'CBMenuItem',
            'name' => 'pages',
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
                    'URL' => '/admin/pages/edit/',
                ],
                (object)[
                    'className' => 'CBMenuItem',
                    'name' => 'find',
                    'text' => 'Find',
                    'URL' => '/admin/page/?class=CBAdminPageForPagesFind',
                ],
                (object)[
                    'className' => 'CBMenuItem',
                    'name' => 'trash',
                    'text' => 'Trash',
                    'URL' => '/admin/page?class=CBAdminPageForPagesTrash',
                ],
                (object)[
                    'className' => 'CBMenuItem',
                    'name' => 'develop',
                    'text' => 'Develop',
                    'URL' => '/admin/page/?class=CBPagesDevelopmentAdminPage',
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
        return ['CBGeneralAdminMenu'];
    }
}
