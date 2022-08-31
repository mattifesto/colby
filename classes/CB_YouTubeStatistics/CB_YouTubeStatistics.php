<?php

final class
CB_YouTubeStatistics
{
    // -- CBModel interfaces



    /**
     * @param object $youtubeChannelSpec
     *
     * @return object
     */
    static function
    CBModel_build(
        stdClass $youtubeStatisticsSpec
    ): stdClass
    {
        $youtubeStatisticsModel =
        (object)[];

        CB_YouTubeStatistics::setCBTimestamp(
            $youtubeStatisticsModel,
            CB_YouTubeStatistics::getCBTimestamp(
                $youtubeStatisticsSpec
            )
        );

        CB_YouTubeStatistics::setStatistics(
            $youtubeStatisticsModel,
            CB_YouTubeStatistics::getStatistics(
                $youtubeStatisticsSpec
            )
        );

        CB_YouTubeStatistics::setYouTubeChannelModelCBID(
            $youtubeStatisticsModel,
            CB_YouTubeStatistics::getYouTubeChannelModelCBID(
                $youtubeStatisticsSpec
            )
        );

        return $youtubeStatisticsModel;
    }
    // CBModel_build()



    /**
     * @param object $youtubeStatisticsModel
     *
     * @return [<CB_Timestamp>]
     */
    static function
    CBModel_getCBTimestamps(
        stdClass $youtubeStatisticsModel
    ): array
    {
        $cbtimestampModel =
        CB_YouTubeStatistics::getCBTimestamp(
            $youtubeStatisticsModel
        );

        if (
            $cbtimestampModel === null
        ) {
            $cbtimestamps =
            [];
        }

        else
        {
            $cbtimestamps =
            [
                $cbtimestampModel,
            ];
        }

        return $cbtimestamps;
    }
    /* CBModel_getCBTimestamps() */



    /**
     * @param object $youtubeStatisticsModel
     *
     * @return string
     */
    static function
    CBModel_getTitle(
        stdClass $youtubeStatisticsModel
    ): string
    {
        $youtubeChannelModelCBID =
        CB_YouTubeStatistics::getYouTubeChannelModelCBID(
            $youtubeStatisticsModel
        );

        $youtubeChannelModel =
        CBModelCache::fetchModelByID(
            $youtubeChannelModelCBID
        );

        if (
            $youtubeChannelModel ===
            null
        ) {
            $youtubeChannelTitle =
            "unknown channel";
        }

        else
        {
            $youtubeChannelTitle =
            CB_YouTubeChannel::getTitle(
                $youtubeChannelModel
            );
        }

        $youtubeStatisticsCBTimestamp =
        CB_YouTubeStatistics::getCBTimestamp(
            $youtubeStatisticsModel
        );

        $date =
        gmdate(
            'Y_m_d h:i:s a',
            CB_Timestamp::getUnixTimestamp(
                $youtubeStatisticsCBTimestamp
            )
        );

        $youtubeStatisticsTitle =
        "${youtubeChannelTitle} ${date}";


        return $youtubeStatisticsTitle;
    }
    // CBModel_getTitle()



    // -- CBModels interfaces



    /**
     * @param [object] $momentModelCBIDs
     *
     * @return void
     */
    static function
    CBModels_willDelete(
        array $youtubeStatisticsModelCBIDs
    ): void
    {
        $youtubeStatisticsModels =
        CBModels::fetchModelsByID2(
            $momentModelCBIDs
        );

        foreach (
            $youtubeStatisticsModels as
            $youtubeStatisticsModel
        ) {
            $firstCBID =
            CB_YouTubeStatistics::getYouTubeChannelModelCBID(
                $youtubeStatisticsModel
            );

            $associationKey =
            'CB_YouTubeStatistics_association';

            $secondCBID =
            CBModel::getCBID(
                $youtubeStatisticsModel
            );

            CBModelAssociations::delete(
                $firstCBID,
                $associationKey,
                $secondCBID
            );
        }
    }
    // CBModels_willDelete()



    /**
     * @param [object] $youtubeStatisticsModels
     *
     * @return void
     */
    static function
    CBModels_willSave(
        array $youtubeStatisticsModels
    ): void
    {
        $modelAssociations = [];

        foreach (
            $youtubeStatisticsModels as
            $youtubeStatisticsModel
        ) {
            $modelAssociation =
            CBModel::createSpec(
                'CB_ModelAssociation'
            );

            CB_ModelAssociation::setFirstCBID(
                $modelAssociation,
                CB_YouTubeStatistics::getYouTubeChannelModelCBID(
                    $youtubeStatisticsModel
                )
            );

            CB_ModelAssociation::setAssociationKey(
                $modelAssociation,
                'CB_YouTubeStatistics_association'
            );

            $cbtimestampModel =
            CB_YouTubeStatistics::getCBTimestamp(
                $youtubeStatisticsModel
            );

            CB_ModelAssociation::setSortingValue(
                $modelAssociation,
                CB_Timestamp::getUnixTimestamp(
                    $cbtimestampModel
                )
            );

            CB_ModelAssociation::setSortingValueDifferentiator(
                $modelAssociation,
                CB_Timestamp::getFemtoseconds(
                    $cbtimestampModel
                )
            );

            CB_ModelAssociation::setSecondCBID(
                $modelAssociation,
                CBModel::getCBID(
                    $youtubeStatisticsModel
                )
            );

            array_push(
                $modelAssociations,
                $modelAssociation
            );
        }

        CBModelAssociations::insertOrUpdate(
            $modelAssociations
        );
    }
    // CBModels_willSave()



    // -- accessors



    /**
     * @param object $youtubeStatisticsModel
     *
     * @return object
     */
    static function
    getCBTimestamp(
        stdClass $youtubeStatisticsModel
    ): ?stdClass
    {
        $cbtimestamp =
        CBModel::valueAsModel(
            $youtubeStatisticsModel,
            'CB_YouTubeStatistics_cbtimestamp_property',
            'CB_Timestamp'
        );

        return $cbtimestamp;
    }
    /* getCBTimestamp() */



    /**
     * @param object $youtubeStatisticsModel
     * @param object $newCBTimestampModel
     *
     * @return void
     */
    static function
    setCBTimestamp(
        stdClass $youtubeStatisticsModel,
        stdClass $newCBTimestampModel
    ): void
    {
        $verifiedCBTimestampModel =
        CBConvert::valueAsModel(
            $newCBTimestampModel,
            'CB_Timestamp'
        );

        $youtubeStatisticsModel->CB_YouTubeStatistics_cbtimestamp_property =
        $verifiedCBTimestampModel;
    }
    /* setCBTimestamp() */



    /**
     * @param object $youtubeStatisticsModel
     *
     * @return CBID|null
     */
    static function
    getYouTubeChannelModelCBID(
        stdClass $youtubeStatisticsModel
    ): ?string
    {
        $youtubeChannelModelCBID =
        CBModel::valueAsCBID(
            $youtubeStatisticsModel,
            'CB_YouTubeStatistics_youtubeChannelModelCBID_property'
        );

        return $youtubeChannelModelCBID;
    }
    /* getYouTubeChannelModelCBID() */



    /**
     * @param object $youtubeStatisticsModel
     * @param string $newYouTubeChannelModelCBID
     *
     * @return void
     */
    static function
    setYouTubeChannelModelCBID(
        stdClass $youtubeStatisticsModel,
        string $newYouTubeChannelModelCBID
    ): void
    {
        $youtubeStatisticsModel->
        CB_YouTubeStatistics_youtubeChannelModelCBID_property =
        $newYouTubeChannelModelCBID;
    }
    /* setYouTubeChannelModelCBID() */



    /**
     * @param object $youtubeStatisticsModel
     *
     * @return object|null
     */
    static function
    getStatistics(
        stdClass $youtubeStatisticsModel,
    ): ?stdClass
    {
        $statistics =
        CBModel::valueAsObject(
            $youtubeStatisticsModel,
            'CB_YouTubeStatistics_statistics_property'
        );

        return $statistics;
    }
    // getStatistics()



    /**
     * @param object $youtubeStatisticsModel
     * @param object $newStatistics
     *
     *      This object should be the raw object returned by the YouTube API
     *      call to get a channel's statistics.
     *
     * @return void
     */
    static function
    setStatistics(
        stdClass $youtubeStatisticsModel,
        ?stdClass $newStatistics
    ): void
    {
        $youtubeStatisticsModel->
        CB_YouTubeStatistics_statistics_property =
        $newStatistics;
    }
    // setStatistics()



    // -- functions



    /**
     * This function gets the current statistics from YouTube for a YouTube
     * channel and returns an unsaved CB_YouTubeStatistics spec.
     */
    static function
    fetch(
        stdClass $youtubeChannelModel
    ): stdClass
    {
        $youtubeAPIKey =
        CB_YouTubeChannel::getAPIKey(
            $youtubeChannelModel
        );

        $youtubeChannelID =
        CB_YouTubeChannel::getChannelID(
            $youtubeChannelModel
        );

        $youtubeStatisticsModelCBID =
        CBID::generateRandomCBID();

        $cbtimestamp =
        CB_Timestamp::reserveNow(
            $youtubeStatisticsModelCBID
        );

        $statistics =
        CB_YouTube::fetchStatistics(
            $youtubeAPIKey,
            $youtubeChannelID
        );

        $youtubeStatisticsSpec =
        CBModel::createSpec(
            'CB_YouTubeStatistics',
            $youtubeStatisticsModelCBID
        );

        CB_YouTubeStatistics::setCBTimestamp(
            $youtubeStatisticsSpec,
            $cbtimestamp
        );

        CB_YouTubeStatistics::setStatistics(
            $youtubeStatisticsSpec,
            $statistics
        );

        CB_YouTubeStatistics::setYouTubeChannelModelCBID(
            $youtubeStatisticsSpec,
            CBModel::getCBID(
                $youtubeChannelModel
            )
        );

        return $youtubeStatisticsSpec;
    }
    // fetch()



    /**
     * This function fetches the most recently saved CB_YouTubeStatistics models
     * for a channel.
     *
     * @param CBID $youtubeChannelModelCBID
     * @param int $maximumResultCount
     *
     * @return [<CB_YouTubeStatistics model>]
     */
    static function
    fetchRecentStatistics(
        string $youtubeChannelModelCBID,
        int $maximumResultCount = 10
    ): array
    {
        $associations =
        CBModelAssociations::fetchModelAssociationsByFirstCBIDAndAssociationKey(
            $youtubeChannelModelCBID,
            'CB_YouTubeStatistics_association',
            'descending',
            $maximumResultCount
        );

        $youtubeStatisticsModelCBIDs =
        array_map(
            function (
                $association
            ): string
            {
                $youtubeStatisticsModelCBID =
                CB_ModelAssociation::getSecondCBID(
                    $association
                );

                return $youtubeStatisticsModelCBID;
            },
            $associations
        );

        $returnValue =
        CBModels::fetchModelsByID2(
            $youtubeStatisticsModelCBIDs,
            true /* maintain positions */
        );

        return $returnValue;
    }
    // fetchRecentStatistics()

}
