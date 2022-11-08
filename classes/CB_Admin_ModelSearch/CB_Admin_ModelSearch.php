<?php

final class
CB_Admin_ModelSearch
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
            'search',
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
        $arrayOfJavaScriptURLs =
        [
            CBLibrary::buildLibraryClassFilePath(
                __CLASS__,
                '2022_11_06_1667761008',
                'js',
                cbsysurl()
            ),
        ];

        return $arrayOfJavaScriptURLs;
    }
    // CBHTMLOutput_JavaScriptURLs()



    /**
     * @return [[<name>,<value>]]
     */
    static function
    CBHTMLOutput_JavaScriptVariables(
    ): array
    {
        $modelClassNameOptions =
        [
            (object)
            [
                'title' =>
                'All',

                'value' =>
                '',
            ]
        ];

        $modelClassNames =
        CBModels::fetchAllModelClassNames();

        foreach (
            $modelClassNames as $modelClassName
        ) {
            $option =
            (object)
            [
                'title' =>
                $modelClassName,

                'value' =>
                $modelClassName
            ];

            array_push(
                $modelClassNameOptions,
                $option
            );
        }

        return
        [
            [
                'CB_Admin_ModelSearch_modelClassNameOptions',
                $modelClassNameOptions,
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
            'CB_UI_ListItem',
            'CB_UI_StringEditor',
            'CBAjax',
            'CBUINavigationView',
            'CBUIPanel',
            'CB_UI_Selector',
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

        $searchMenuItem =
        CBModel::createSpec(
            'CBMenuItem'
        );

        $searchMenuItem->name =
        'search';

        $searchMenuItem->text =
        'Search';

        $searchMenuItem->URL =
        CBAdmin::getAdminPageURL(
            'CB_Admin_ModelSearch'
        );

        array_push(
            $modelsAdminMenuItems,
            $searchMenuItem
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
