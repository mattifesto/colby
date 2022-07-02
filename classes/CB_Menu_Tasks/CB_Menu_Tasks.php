<?php

final class
CB_Menu_Tasks
{
    // -- CBInstall interfaces



    /**
     * @return void
     */
    static function
    CBInstall_install(
    ): void
    {
        // tasks menu

        $tasksMenuSpec =
        CBModel::createSpec(
            'CBMenu',
            CB_Menu_Tasks::getModelCBID()
        );

        CBMenu::setTitle(
            $tasksMenuSpec,
            'Tasks'
        );

        CBMenu::setTitleURL(
            $tasksMenuSpec,
            CBAdmin::getAdminPageURL(
                'CBTasks2Admin'
            )
        );



        // general admin menu, tasks menu item

        $generalAdminMenuTaskMenuItemSpec =
        CBModel::createSpec(
            'CBMenuItem'
        );

        CBMenuItem::setName(
            $generalAdminMenuTaskMenuItemSpec,
            'CB_Menu_Tasks_generalMenuItem_tasks'
        );

        CBMenuItem::setSubmenuCBID(
            $generalAdminMenuTaskMenuItemSpec,
            CB_Menu_Tasks::getModelCBID()
        );

        CBMenuItem::setText(
            $generalAdminMenuTaskMenuItemSpec,
            'Tasks'
        );

        CBMenuItem::setURL(
            $generalAdminMenuTaskMenuItemSpec,
            CBAdmin::getAdminPageURL(
                'CBTasks2Admin'
            )
        );



        // general admin menu

        $generalAdminMenuModelUpdater =
        new CBModelUpdater(
            CBGeneralAdminMenu::getModelCBID()
        );

        $generalAdminMenuSpec =
        $generalAdminMenuModelUpdater->getSpec();

        $generalAdminMenuItems =
        CBMenu::getMenuItems(
            $generalAdminMenuSpec
        );

        array_push(
            $generalAdminMenuItems,
            $generalAdminMenuTaskMenuItemSpec
        );

        CBMenu::setMenuItems(
            $generalAdminMenuSpec,
            $generalAdminMenuItems
        );



        // save

        CBDB::transaction(
            function () use (
                $tasksMenuSpec,
                $generalAdminMenuModelUpdater
            ): void
            {
                CBModels::deleteByID(
                    CB_Menu_Tasks::getModelCBID()
                );

                CBModels::save(
                    $tasksMenuSpec
                );

                $generalAdminMenuModelUpdater->save2();
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
            'CBGeneralAdminMenu',
        ];

        return $requiredClassNames;
    }
    // CBInstall_requiredClassNames()



    // -- functions



    /**
     * @return CBID
     */
    static function
    getModelCBID(
    ): string
    {
        return '2cfc9d027e6ce061e99cff861cce911ef4c8438f';
    }
    // getModelCBID()

}
