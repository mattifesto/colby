<?php

final class CBGitStatusAdmin {

    /**
     * @return string
     */
    static function CBAdmin_group(): string {
        return 'Developers';
    }

    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return ['develop', 'git', 'status'];
    }

    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        CBHTMLOutput::pageInformation()->title = 'Git Status';
    }

    /**
     * @param mixed $args
     *
     * @return mixed
     */
    static function CBAjax_fetchStatus($args) {
        return CBHex160::random();
    }

    /**
     * @return string
     */
    static function CBAjax_fetchStatus_group(): string {
        return 'Developers';
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [Colby::flexpath(__CLASS__, 'v397.js', cbsysurl())];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return ['CBUI'];
    }

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $spec = CBModels::fetchSpecByID(CBGitAdminMenu::ID());

        $spec->items[] = (object)[
            'className' => 'CBMenuItem',
            'name' => 'status',
            'text' => 'Status',
            'URL' => '/admin/?c=CBGitStatusAdmin',
        ];

        CBDB::transaction(function () use ($spec) {
            CBModels::save($spec);
        });
    }

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return ['CBGitAdminMenu', 'CBGitAdmin'];
    }
}
