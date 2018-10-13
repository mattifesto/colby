<?php

final class CBTasks2Admin {

    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return [
            'general',
            'tasks'
        ];
    }

    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        CBHTMLOutput::pageInformation()->title = 'Tasks Administration';
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v460.js', cbsysurl()),
        ];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBUI',
            'CBUIMessagePart',
            'CBUISection',
            'CBUISectionItem4',
            'CBUIStringsPart',
        ];
    }

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $spec = CBModels::fetchSpecByID(CBGeneralAdminMenu::ID);

        $spec->items[] = (object)[
            'className' => 'CBMenuItem',
            'name' => 'tasks',
            'text' => 'Tasks',
            'URL' => '/admin/?c=CBTasks2Admin',
        ];

        CBDB::transaction(function () use ($spec) {
            CBModels::save($spec);
        });
    }

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBGeneralAdminMenu',
        ];
    }
}
