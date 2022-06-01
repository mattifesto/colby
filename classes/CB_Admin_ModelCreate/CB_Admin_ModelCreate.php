<?php

final class
CB_Admin_ModelCreate
{
    // -- CBAdmin interfaces



    /**
     * @return string
     */
    static function
    CBAdmin_getUserGroupClassName(
    ): string
    {
        return
        'CBAdministratorsUserGroup';
    }
    // CBAdmin_getUserGroupClassName()



    /**
     * @return [string]
     */
    static function
    CBAdmin_menuNamePath(
    ): array {
        return
        [
            'models',
            'create',
        ];
    }
    // CBAdmin_menuNamePath()



    /**
     * @return void
     */
    static function
    CBAdmin_render(
    ): void
    {
        CBHTMLOutput::pageInformation()->title =
        'Search Models';
    }
    // CBAdmin_render()



    // -- CBHTMLOutput interfaces



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array
    {
        return
        [
            Colby::flexpath(
                __CLASS__,
                'v2022.06.01.1654054851.js',
                cbsysurl()
            ),
        ];
    }
    // CBHTMLOutput_JavaScriptURLs()



    /**
     * @return [[<name>,<value>]]
     */
    static function
    CBHTMLOutput_JavaScriptVariables(
    ): array
    {
        return
        [
            [
                'CB_Admin_ModelCreate_modelTemplates',
                CBModelTemplateCatalog::fetchAllTemplates(),
            ],
        ];
    }
    // CBHTMLOutput_JavaScriptVariables()




    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array
    {
        return
        [
            'CBUINavigationView',
        ];
    }
    // CBHTMLOutput_requiredClassNames()



    // -- CBInstall interfaces



    /**
     * @return void
     */
    static function
    CBInstall_configure(
    ): void
    {
        $modelsAdminMenuSpec =
        CBModels::fetchSpecByID(
            CBModelsAdminMenu::ID()
        );

        $modelsAdminMenuItems =
        CBMenu::getMenuItems(
            $modelsAdminMenuSpec
        );

        $createMenuItem =
        CBModel::createSpec(
            'CBMenuItem'
        );

        CBMenuItem::setName(
            $createMenuItem,
            'create'
        );

        CBMenuItem::setText(
            $createMenuItem,
            'Create'
        );

        CBMenuItem::setURL(
            $createMenuItem,
            CBAdmin::getAdminPageURL(
                'CB_Admin_ModelCreate'
            )
        );

        array_push(
            $modelsAdminMenuItems,
            $createMenuItem
        );

        CBMenu::setMenuItems(
            $modelsAdminMenuSpec,
            $modelsAdminMenuItems
        );

        CBDB::transaction(
            function () use (
                $modelsAdminMenuSpec
            ): void
            {
                CBModels::save(
                    $modelsAdminMenuSpec
                );
            }
        );
    }
    // CBInstall_configure()

}
