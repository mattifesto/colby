<?php

final class
CB_Admin_ScheduledTasks
{

    // -- CBAdmin interfaces



    /**
     * @return string
     */
    static function
    CBAdmin_getUserGroupClassName(
    ): string
    {
        return 'CBDevelopersUserGroup';
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
            'general',
            'CB_Menu_Tasks_generalMenuItem_tasks',
            'CB_Admin_ScheduledTasks_tasksMenuItem_scheduled',
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
        'Scheduled Tasks';
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
        $javascriptURLs =
        [
            CBLibrary::buildLibraryClassFilePath(
                'CB_Admin_ScheduledTasks',
                '2022_06_30_1656605068',
                'js',
                cbsysurl()
            ),
        ];

        return $javascriptURLs;
    }
    // CBHTMLOutput_JavaScriptURLs()



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array
    {
        $requiredClassNames =
        [
            'CBAjax',
            'CBModel',
            'Colby',
        ];

        return $requiredClassNames;
    }
    // CBHTMLOutput_requiredClassNames()



    // -- CBInstall interfaces



    /**
     * @return void
     */
    static function
    CBInstall_install(
    ): void
    {
        // tasks menu, scheduled tasks menu item

        $scheduledTasksMenuItemSpec =
        CBModel::createSpec(
            'CBMenuItem'
        );

        CBMenuItem::setName(
            $scheduledTasksMenuItemSpec,
            'CB_Admin_ScheduledTasks_tasksMenuItem_scheduled'
        );

        CBMenuItem::setText(
            $scheduledTasksMenuItemSpec,
            'Scheduled'
        );

        CBMenuItem::setURL(
            $scheduledTasksMenuItemSpec,
            CBAdmin::getAdminPageURL(
                'CB_Admin_ScheduledTasks'
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
            $scheduledTasksMenuItemSpec
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
