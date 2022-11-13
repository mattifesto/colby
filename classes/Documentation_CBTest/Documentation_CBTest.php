<?php

final class
Documentation_CBTest
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



    /**
     * @return [string]
     */
    static function
    CBAdmin_menuNamePath(
    ): array
    {
        return [
            'help',
        ];
    }



    /**
     * @return void
     */
    static function
    CBAdmin_render(
    ): void
    {
        CBHTMLOutput::pageInformation()->title =
        'CBTest Documentation';

        CBView::renderSpec(
            (object)
            [
                'className' =>
                'CBPageTitleAndDescriptionView',
            ]
        );

        CBView::renderSpec(
            (object)
            [
                'className' =>
                'CBMessageView',

                'markup' =>
                file_get_contents(
                    Colby::flexpath(
                        __CLASS__,
                        'mmk',
                        cbsysdir()
                    )
                ),
            ]
        );
    }
    // CBAdmin_render()



    // -- CBInstall interfaces



    /**
     * @return void
     */
    static function
    CBInstall_install(
    ): void
    {
        $updater =
        CBModelUpdater::fetch(
            (object)
            [
                'ID' =>
                CBHelpAdminMenu::ID(),
            ]
        );

        $menuSpec =
        $updater->working;

        CBMenu::addOrReplaceItem(
            $menuSpec,
            (object)
            [
                'className' =>
                'CBMenuItem',

                'name' =>
                'CBTest',

                'text' =>
                'CBTest',

                'URL' =>
                '/admin/?c=Documentation_CBTest',
            ]
        );

        CBModelUpdater::save(
            $updater
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
        return [
            'CBHelpAdminMenu'
        ];
    }
    // CBInstall_requiredClassNames()

}
