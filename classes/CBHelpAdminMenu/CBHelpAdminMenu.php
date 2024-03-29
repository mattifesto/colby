<?php

final class CBHelpAdminMenu {

    /* -- CBInstall interfaces -- */



    /**
     * @return void
     */
    static function CBInstall_install(
    ): void {
        $adminMenuSpec = CBModels::fetchSpecByID(
            CBAdminMenu::ID()
        );

        $adminMenuSpec->items[] = (object)[
            'className' => 'CBMenuItem',
            'name' => 'help',
            'text' => 'Documentation',
            'URL' => '/admin/?c=CBDocumentation',
        ];

        $helpAdminMenuSpec = (object)[
            'className' => 'CBMenu',
            'ID' => CBHelpAdminMenu::ID(),
            'title' => 'Documentation',
            'titleURI' => '/admin/?c=CBDocumentation',
            'items' => [],
        ];

        CBDB::transaction(
            function () use (
                $adminMenuSpec,
                $helpAdminMenuSpec
            ) {
                CBModels::save(
                    $adminMenuSpec
                );

                CBModels::deleteByID(
                    CBHelpAdminMenu::ID()
                );

                CBModels::save(
                    $helpAdminMenuSpec
                );
            }
        );
    }
    /* CBInstall_install() */



    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(
    ): array {
        return [
            'CBAdminMenu',
            'CBPagesAdminMenu',
        ];
    }
    /* CBInstall_requiredClassNames() */



    /* -- functions -- */



    /**
     * @return ID
     */
    static function ID(
    ): string {
        return '62eeeabc11366b92bf22017903bffb1fead31764';
    }
    /* ID() */

}
