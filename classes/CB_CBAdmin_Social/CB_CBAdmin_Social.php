<?php

final class
CB_CBAdmin_Social
{
    // -- CBAdmin interfaces



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
    ): void
    {
        CBHTMLOutput::pageInformation()->title =
        'Social';

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
    ): array
    {
        return
        [
            CBLibrary::buildLibraryClassFilePath(
                __CLASS__,
                '2022_07_02_1656798521',
                'js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [[<key>, <value>]]
     */
    static function
    CBHTMLOutput_JavaScriptVariables(
    ): array
    {
        $youtubeChannelModels =
        CBModels::fetchModelsByClassName(
            'CB_YouTubeChannel'
        );

        $youtubeChannels =
        array_map(
            function (
                $youtubeChannelModel
            ): stdClass
            {
                $title =
                CBModel::getTitle(
                    $youtubeChannelModel
                );

                $cbid =
                CBModel::getCBID(
                    $youtubeChannelModel
                );

                $returnValue =
                (object)
                [
                    'CB_CBAdmin_Social_YouTubeChannel_title_property' =>
                    $title,

                    'CB_CBAdmin_Social_YouTubeChannel_cbid_property' =>
                    $cbid,
                ];

                return $returnValue;
            },
            $youtubeChannelModels
        );

        $youtubeChannels =
        array_values(
            $youtubeChannels
        );

        return
        [
            [
                'CB_CBAdmin_Social_youtubeChannels_jsvariable',
                $youtubeChannels,
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
            'CBAjax',
            'CBErrorHandler',
            'CBModel',
            'Colby',
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
