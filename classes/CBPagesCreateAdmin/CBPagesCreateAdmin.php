<?php

final class CBPagesCreateAdmin {

    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return ['pages', 'create'];
    }

    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        CBHTMLOutput::pageInformation()->title = 'Create a New Page';
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [Colby::flexpath(__CLASS__, 'v411.js', cbsysurl())];
    }

    /**
     * @return [[<name>, <value>]]
     */
    static function CBHTMLOutput_JavaScriptVariables(): array {
        $templateclassNames = CBPageTemplates::templateClassNames();

        $templates = array_map(function ($className) {
            if (is_callable($function = "{$className}::CBModelTemplate_title")) {
                $title = call_user_func($function);
            } else {
                $title = $className;
            }

            return (object)[
                'className' => $className,
                'title' => $title,
            ];
        }, $templateclassNames);

        return [
            ['CBPagesCreateAdmin_templates', $templates],
        ];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return ['CBUI', 'CBUINavigationArrowPart', 'CBUISectionItem4',
                'CBUIStringsPart'];
    }

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $spec = CBModels::fetchSpecByID(CBPagesAdminMenu::ID);
        $spec->items[] = (object)[
            'className' => 'CBMenuItem',
            'name' => 'create',
            'text' => 'Create',
            'URL' => '/admin/?c=CBPagesCreateAdmin',
        ];

        CBDB::transaction(function () use ($spec) {
            CBModels::save($spec);
        });
    }

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return ['CBPagesAdminMenu'];
    }
}
