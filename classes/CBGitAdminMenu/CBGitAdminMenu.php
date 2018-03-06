<?php

final class CBGitAdminMenu {

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $developAdminMenuSpec = CBModels::fetchSpecByID(CBDevelopAdminMenu::ID());

        $developAdminMenuSpec->items[] = (object)[
            'className' => 'CBMenuItem',
            'name' => 'git',
            'submenuID' => CBGitAdminMenu::ID(),
            'text' => 'Git',
            'URL' => '/admin/?c=CBGitHistoryAdmin',
        ];

        $spec = (object)[
            'className' => 'CBMenu',
            'ID' => CBGitAdminMenu::ID(),
            'title' => 'Git',
            'titleURI' => '/admin/?c=CBGitHistoryAdmin',
        ];

        CBDB::transaction(function () use ($developAdminMenuSpec, $spec) {
            CBModels::save($developAdminMenuSpec);
            CBModels::deleteByID(CBGitAdminMenu::ID());
            CBModels::save($spec);
        });
    }

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return ['CBDevelopAdminMenu'];
    }

    /**
     * @return hex160
     */
    static function ID(): string {
        return '72ca15bae2ff97a62e841ad72233aa5ffdab4bec';
    }
}
