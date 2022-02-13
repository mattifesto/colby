<?php

final class
CB_CBAdmin_Social {

    /* -- CBAdmin interfaces -- -- -- -- -- */



    /**
     * @return string
     */
    static function
    CBAdmin_getUserGroupClassName(
    ): string {
        return 'CBAdministratorsUserGroup';
    }
    /* CBAdmin_getUserGroupClassName() */



    /**
     * @return [string]
     */
    static function
    CBAdmin_menuNamePath(
    ) {
        return [
            'general',
            'social',
        ];
    }
    /* CBAdmin_menuNamePath() */

    /**
     * @return void
     */
    static function
    CBAdmin_render(
    ): void {
        ?>

        <div class="CB_CBAdmin_Social_element"></div>

        <?php
    }
    /* CBAdmin_render() */



    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.56.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array {
        return [
            'CB_UI_KeyValue',
            'CBAjax',
            'CBErrorHandler',
            'CBModel',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBInstall interfaces -- */



    /**
     * @return void
     */
    static function
    CBInstall_install(
    ): void {
        $generalMenuUpdater = new CBModelUpdater(
            CBGeneralAdminMenu::getModelCBID()
        );

        $generalMenuSpec = $generalMenuUpdater->getSpec();

        $generalMenuItemSpecs = CBMenu::getMenuItems(
            $generalMenuSpec
        );

        $socialMenuItemSpec = CBModel::createSpec(
            'CBMenuItem'
        );

        $socialMenuItemSpec->name = 'social';
        $socialMenuItemSpec->text = 'Social';

        $socialMenuItemSpec->URL = CBAdmin::getAdminPageURL(
            'CB_CBAdmin_Social'
        );

        array_push(
            $generalMenuItemSpecs,
            $socialMenuItemSpec
        );

        CBMenu::setMenuItems(
            $generalMenuSpec,
            $generalMenuItemSpecs
        );

        CBDB::transaction(
            function () use (
                $generalMenuUpdater
            ) {
                $generalMenuUpdater->save2();
            }
        );
    }
    /* CBInstall_install() */



    /**
     * @return [string]
     */
    static function
    CBInstall_requiredClassNames(
    ): array {
        return [
            'CBGeneralAdminMenu',
            'CBLogAdminPage',
        ];
    }
    /* CBInstall_requiredClassNames() */

}
