<?php

/**
 * This task will be scheduled when a CB_YouTubeChannel model is saved and
 * appears to have a channel ID and an API key.
 */
final class
CB_Task_CollectYouTubeChannelStatistics
{
    private static
    $sessionRunCount =
    0;



    private static
    $sessionYouTubeStatisticsSpecs =
    [];



    // -- CBInstall interfaces



    /**
     * @return void
     */
    static function
    CBInstall_configure(
    ): void
    {
        $youtubeChannelModelCBIDs =
        CBModels::fetchCBIDsByClassName(
            'CB_YouTubeChannel'
        );

        $timestampForNextMidnightUTC =
        strtotime(
            '24:00'
        );

        foreach(
            $youtubeChannelModelCBIDs as
            $youtubeChannelModelCBID
        ) {
            CBTasks2::updateTask(
                'CB_Task_CollectYouTubeChannelStatistics',
                $youtubeChannelModelCBID,
                null, /* proccess ID */
                null, /* priority */
                $timestampForNextMidnightUTC /* scheduled timestamp */
            );
        }
    }
    // CBInstall_configure()



    // -- CBTasks2 interfaces



    /**
     * @param CBID $userModelCBID
     *
     * @return object|null
     */
    static function
    CBTasks2_run(
        string $youtubeChannelModelCBID
    ): ?stdClass
    {
        CB_Task_CollectYouTubeChannelStatistics::$sessionRunCount +=
        1;

        $youtubeChannelModel =
        CBModels::fetchModelByCBID(
            $youtubeChannelModelCBID
        );

        if (
            $youtubeChannelModel === null
        ) {
            array_push(
                CB_Task_CollectYouTubeChannelStatistics::$sessionYouTubeStatisticsSpecs,
                null
            );

            return null;
        }

        $youtubeStatisticsSpec =
        CB_YouTubeStatistics::fetch(
            $youtubeChannelModel
        );

        CBDB::transaction(
            function () use (
                $youtubeStatisticsSpec
            ): void
            {
                CBModels::save(
                    $youtubeStatisticsSpec
                );
            }
        );

        array_push(
            CB_Task_CollectYouTubeChannelStatistics::$sessionYouTubeStatisticsSpecs,
            $youtubeStatisticsSpec
        );

        $timestampForNextMidnightUTC =
        strtotime("24:00");

        $returnValue =
        (object)
        [
            'scheduled' =>
            $timestampForNextMidnightUTC,
        ];

        return $returnValue;
    }
    // CBTasks2_run()



    // -- functions



    /**
     *  @return int
     */
    static function
    getSessionRunCount(
    ): int
    {
        $sessionRunCount =
        CB_Task_CollectYouTubeChannelStatistics::$sessionRunCount;

        return $sessionRunCount;
    }
    // getSessionRunCount()



    /**
     * @return array
     */
    static function
    getSessionYouTubeStatisticsSpecs(
    ): array
    {
        $sessionYouTubeStatisticsSpecs =
        CB_Task_CollectYouTubeChannelStatistics::$sessionYouTubeStatisticsSpecs;

        return $sessionYouTubeStatisticsSpecs;
    }
    // getSessionYouTubeStatisticsSpecs()



    /**
     * @return void
     */
    static function
    resetSessionStaticVariables(
    ): void
    {
        CB_Task_CollectYouTubeChannelStatistics::$sessionRunCount =
        0;

        CB_Task_CollectYouTubeChannelStatistics::$sessionYouTubeStatisticsSpecs =
        [];
    }
    /* resetSession() */

}
