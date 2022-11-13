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

        <div class="CB_CBAdmin_Social_element">

            <?php

            CB_CBAdmin_Social::renderYouTubeChannelGraphs();

            ?>

        </div>

        <?php
    }
    /* CBAdmin_render() */



    // -- CBHTMLOutput interfaces



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_CSSURLs(
    ): array
    {
        $cssURLs =
        [
            CBLibrary::buildLibraryClassFilePath(
                __CLASS__,
                '2022_09_13_1663107077',
                'css',
                cbsysurl()
            ),
        ];

        return $cssURLs;
    }
    // CBHTMLOutput_CSSURLs()



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array
    {
        $javaScriptURLs =
        [
            CBLibrary::buildLibraryClassFilePath(
                __CLASS__,
                '2022_09_12_1662999979',
                'js',
                cbsysurl()
            ),
        ];

        return $javaScriptURLs;
    }
    // CBHTMLOutput_JavaScriptURLs()



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
        $arrayOfRequiredClassNames =
        [
            'CBAjax',
            'CBDevelopersUserGroup',
            'CBErrorHandler',
            'CBModel',
            'Colby',
        ];

        return $arrayOfRequiredClassNames;
    }
    // CBHTMLOutput_requiredClassNames()



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



    // -- functions



    /**
     * @param object $youtubeChannelModel
     *
     * @return void
     */
    private static function
    renderGraphForYouTubeChannel(
        stdClass $youtubeChannelModel
    ): void
    {
        $youtubeChannelModelCBID =
        CBModel::getCBID(
            $youtubeChannelModel
        );

        $dateInformation =
        getdate();

        $youtubeStatisticsModels =
        CB_YouTubeStatistics::fetchDailyStatistics(
            $youtubeChannelModelCBID,
            $dateInformation['mon'],
            $dateInformation['mday'],
            $dateInformation['year'],
            28
        );

        $titles =
        [];

        $values =
        [];

        foreach (
            $youtubeStatisticsModels as
            $youtubeStatisticsModel
        ) {
            if (
                $youtubeStatisticsModel !==
                null
            ) {
                $subscriberCount =
                CB_YouTubeStatistics::getStatistics_SubscriberCount(
                    $youtubeStatisticsModel
                );

                array_push(
                    $values,
                    $subscriberCount
                );

                $cbtimestamp =
                CB_YouTubeStatistics::getCBTimestamp(
                    $youtubeStatisticsModel
                );

                $timestamp =
                CB_Timestamp::getUnixTimestamp(
                    $cbtimestamp
                );

                $dateString =
                gmdate(
                    'Y/m/d',
                    $timestamp
                );

                array_push(
                    $titles,
                    "$dateString $subscriberCount"
                );
            }
            // if

            else
            {
                array_push(
                    $values,
                    null
                );

                array_push(
                    $titles,
                    "No Data"
                );
            }
            // else
        }
        // foreach

        /**
         * Put the most recent values at the end of the array.
         */

        $values =
        array_reverse(
            $values
        );

        $titles =
        array_reverse(
            $titles
        );

        $viewSpec =
        CBModel::createSpec(
            'CB_View_SVGBarChart1'
        );

        CB_View_SVGBarChart1::setTitles(
            $viewSpec,
            $titles
        );

        CB_View_SVGBarChart1::setValues(
            $viewSpec,
            $values
        );

        CBView::renderSpec(
            $viewSpec
        );
    }
    // renderGraphForYouTubeChannel()



    /**
     * @return void
     */
    private static function
    renderYouTubeChannelGraphs(
    ): void
    {
        $youtubeChannelModels =
        CBModels::fetchModelsByClassName(
            'CB_YouTubeChannel'
        );

        foreach (
            $youtubeChannelModels as
            $youtubeChannelModel
        ) {
            $title =
            CB_YouTubeChannel::getTitle(
                $youtubeChannelModel
            );

            echo
            '<div class="CB_CBAdmin_Social_channel_element">',
            '<div class="CB_CBAdmin_Social_channelContent_element">',
            '<h3>',
            cbhtml($title),
            '</h3>';

            CB_CBAdmin_Social::renderGraphForYouTubeChannel(
                $youtubeChannelModel
            );

            echo
            '</div>',
            '</div>';
        }
    }
    // renderYouTubeChannelGraphs()

}
