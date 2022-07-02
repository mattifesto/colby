<?php

final class CBTasks2Admin {

    /* -- CBAdmin interfaces -- -- -- -- -- */



    /**
     * @return string
     */
    static function CBAdmin_getUserGroupClassName(): string {
        return 'CBAdministratorsUserGroup';
    }



    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return [
            'general',
            'CB_Menu_Tasks_generalMenuItem_tasks',
            'CBTasks2Admin_tasksMenuItem_status',
        ];
    }



    /**
     * @return void
     */
    static function CBAdmin_render(): void {
        CBHTMLOutput::pageInformation()->title = 'Tasks Administration';
    }



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v659.js', cbsysurl()),
        ];
    }



    /**
     * @return [[<name>, <value>]]
     */
    static function CBHTMLOutput_JavaScriptVariables(): array {
        $failedTasks = CBTasks2::fetchFailedTasks();

        return [
            [
                'CBTasks2Admin_failedTasks',
                $failedTasks,
            ],
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBAjax',
            'CBModel',
            'CBUI',
            'CBUIBooleanSwitchPart',
            'CBUIMessagePart',
            'CBUIPanel',
            'CBUISection',
            'CBUISectionItem4',
            'CBUIStringsPart',
        ];
    }



    // -- CBInstall interfaces



    /**
     * @return void
     */
    static function
    CBInstall_install(
    ): void
    {
        // tasks menu, tasks status menu item

        $tasksStatusMenuItemSpec =
        CBModel::createSpec(
            'CBMenuItem'
        );

        CBMenuItem::setName(
            $tasksStatusMenuItemSpec,
            'CBTasks2Admin_tasksMenuItem_status'
        );

        CBMenuItem::setText(
            $tasksStatusMenuItemSpec,
            'Status'
        );

        CBMenuItem::setURL(
            $tasksStatusMenuItemSpec,
            CBAdmin::getAdminPageURL(
                'CBTasks2Admin'
            )
        );



        // tasks menu

        $tasksMenuModelUpdater =
        new CBModelUpdater(
            CB_Menu_Tasks::getModelCBID()
        );

        $tasksMenuSpec =
        $tasksMenuModelUpdater->getSpec();

        $tasksMenuItems =
        CBMenu::getMenuItems(
            $tasksMenuSpec
        );

        array_push(
            $tasksMenuItems,
            $tasksStatusMenuItemSpec
        );

        CBMenu::setMenuItems(
            $tasksMenuSpec,
            $tasksMenuItems
        );



        // save

        CBDB::transaction(
            function () use (
                $tasksMenuModelUpdater
            ): void
            {
                $tasksMenuModelUpdater->save2();
            }
        );
    }
    // CBInstall_install()



    /**
     * @return [string]
     */
    static function
    CBInstall_requiredClassNames(
    ): array
    {
        $requiredClassNames =
        [
            'CB_Menu_Tasks'
        ];

        return $requiredClassNames;
    }
    // CBInstall_requiredClassNames()

}
