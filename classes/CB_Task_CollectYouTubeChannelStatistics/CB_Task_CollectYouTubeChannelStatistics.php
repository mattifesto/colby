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

        foreach(
            $youtubeChannelModelCBIDs as
            $youtubeChannelModelCBID
        ) {
            CB_Task_CollectYouTubeChannelStatistics::restartNextTaskForYouTubeChannel(
                $youtubeChannelModelCBID
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

        $previouslyRunCBTimestamp =
        CB_YouTubeStatistics::getCBTimestamp(
            $youtubeStatisticsSpec
        );

        $previouslyRunUnixTimestamp =
        CB_Timestamp::getUnixTimestamp(
            $previouslyRunCBTimestamp
        );

        $nextRunUnixTimestamp =
        CB_Task_CollectYouTubeChannelStatistics::calculateTheScheduledUnixTimestampOfTheNextTaskRun(
            $previouslyRunUnixTimestamp
        );

        $returnValue =
        (object)
        [
            'scheduled' =>
            $nextRunUnixTimestamp,
        ];

        return $returnValue;
    }
    // CBTasks2_run()



    // -- functions



    /**
     * This function returns the next time the task should be run according to
     * the settings of the current website.
     *
     * @param object|null $unixTimestampOfMostRecentTaskRun
     * @param int|null $providedUnixTimestampForNow
     *
     *      This parameter exists to make this function testable. In production
     *      it will usually not be provided and the return value of time() will
     *      be used.
     *
     * @return int
     */
    static function
    calculateTheScheduledUnixTimestampOfTheNextTaskRun(
        ?int $unixTimestampOfTheMostRecentTaskRun,
        ?int $providedUnixTimestampForNow = null
    ): int
    {
        if (
            $providedUnixTimestampForNow ===
            null
        ) {
            $unixTimestampForNow =
            time();
        }

        else
        {
            if (
                $providedUnixTimestampForNow <
                $unixTimestampOfTheMostRecentTaskRun
            ) {
                throw new CBException(
                    CBConvert::stringToCleanLine(<<<EOT

                        The providedUnixTimestampForNow argument must be greater
                        than the unixTimestampOfTheMostRecentTaskRun argument.

                    EOT),
                    '',
                    '72a0aece1a286b237d1a16920f72efd375920a27'
                );
            }

            $unixTimestampForNow =
            $providedUnixTimestampForNow;
        }

        if (
            $unixTimestampOfTheMostRecentTaskRun ===
            null
        ) {
            return $unixTimestampForNow;
        }

        $secondsInOneHour =
        60 * 60;

        $unixTimestampAtTheBeginningOfTheCurrentHour =
        (
            intdiv(
                $unixTimestampForNow,
                $secondsInOneHour
            ) *
            $secondsInOneHour
        ) +
        1;

        if (
            $unixTimestampAtTheBeginningOfTheCurrentHour >
            $unixTimestampOfTheMostRecentTaskRun
        ) {
            return $unixTimestampAtTheBeginningOfTheCurrentHour;
        }

        $scheduledUnixTimestampOfTheNextTaskRun =
        $unixTimestampAtTheBeginningOfTheCurrentHour +
        $secondsInOneHour;

        return $scheduledUnixTimestampOfTheNextTaskRun;
    }
    // calculateTheScheduledUnixTimestampOfTheNextTaskRun();



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



    /**
     * This function should be the only place where this task is scheduled. It
     * schedules the task for a YouTube channel at the next appropriate time
     * after having looked at when the task was last run for the channel.
     */
    static function
    restartNextTaskForYouTubeChannel(
        string $youtubeChannelModelCBID
    ): void
    {
        $youtubeStatisticsModel =
        CB_YouTubeStatistics::fetchMostRecentYouTubeStatisticsModel(
            $youtubeChannelModelCBID
        );



        if (
            $youtubeStatisticsModel ===
            null
        ) {
            $previouslyRunUnixTimestamp =
            null;
        }
        // if



        else
        {
            $previouslyRunCBTimestamp =
            CB_YouTubeStatistics::getCBTimestamp(
                $youtubeStatisticsModel
            );

            $previouslyRunUnixTimestamp =
            CB_Timestamp::getUnixTimestamp(
                $previouslyRunCBTimestamp
            );

        }
        //else


        $nextRunUnixTimestamp =
        CB_Task_CollectYouTubeChannelStatistics::calculateTheScheduledUnixTimestampOfTheNextTaskRun(
            $previouslyRunUnixTimestamp
        );

        CBTasks2::updateTask(
            'CB_Task_CollectYouTubeChannelStatistics',
            $youtubeChannelModelCBID,
            null, /* proccess ID */
            null, /* priority */
            $nextRunUnixTimestamp
        );
    }
    // restartNextTaskForYouTubeChannel()

}
