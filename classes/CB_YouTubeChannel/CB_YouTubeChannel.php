<?php

final class
CB_YouTubeChannel
{
    // -- CBModel interfaces



    /**
     * @param object $youtubeChannelSpec
     *
     * @return object
     */
    static function
    CBModel_build(
        stdClass $youtubeChannelSpec
    ): stdClass
    {
        $youtubeChannelModel =
        (object)[];

        CB_YouTubeChannel::setAPIKey(
            $youtubeChannelModel,
            CB_YouTubeChannel::getAPIKey(
                $youtubeChannelSpec
            )
        );

        CB_YouTubeChannel::setChannelID(
            $youtubeChannelModel,
            CB_YouTubeChannel::getChannelID(
                $youtubeChannelSpec
            )
        );

        CB_YouTubeChannel::setTitle(
            $youtubeChannelModel,
            CB_YouTubeChannel::getTitle(
                $youtubeChannelSpec
            )
        );

        return $youtubeChannelModel;
    }
    // CBModel_build()



    /**
     * @param object $youtubeChannelModel
     *
     * @return string
     */
    static function
    CBModel_getTitle(
        stdClass $youtubeChannelModel
    ): string
    {
        $title =
        CB_YouTubeChannel::getTitle(
            $youtubeChannelModel
        );

        return $title;
    }
    // CBModel_getTitle()



    /**
     * @param [object] $youtubeChannelModels
     *
     * @return void
     */
    static function
    CBModels_willSave(
        array $youtubeChannelModels
    ): void
    {
        $youtubeChannelModelCBIDs =
        array_map(
            function (
                stdClass $youtubeChannelModel
            ): string
            {
                $CBID =
                CBModel::getCBID(
                    $youtubeChannelModel
                );

                return $CBID;
            },
            $youtubeChannelModels
        );

        CBTasks2::restart(
            'CB_Task_CollectYouTubeChannelStatistics',
            $youtubeChannelModelCBIDs,
            null, // default priority
            15 // run task in 15 seconds
        );
    }
    /* CBModels_willSave() */



    // -- accessors



    /**
     * @param object $youtubeChannelModel
     *
     * @return string
     *
     *      Returns an empty string if the property value has not been set.
     */
    static function
    getAPIKey(
        stdClass $youtubeChannelModel
    ): string
    {
        $apiKey =
        trim(
            CBModel::valueToString(
                $youtubeChannelModel,
                'CB_YouTubeChannel_apiKey_property'
            )
        );

        return $apiKey;
    }
    /* getAPIKey() */



    /**
     * @param object $youtubeChannelModel
     * @param string $newAPIKey
     *
     * @return void
     */
    static function
    setAPIKey(
        stdClass $youtubeChannelModel,
        string $newAPIKey
    ): void
    {
        $youtubeChannelModel->
        CB_YouTubeChannel_apiKey_property =
        $newAPIKey;
    }
    /* setAPIKey() */



    /**
     * @param object $youtubeChannelModel
     *
     * @return string
     *
     *      Returns an empty string if the property value has not been set.
     */
    static function
    getChannelID(
        stdClass $youtubeChannelModel
    ): string
    {
        $channelID =
        trim(
            CBModel::valueToString(
                $youtubeChannelModel,
                'CB_YouTubeChannel_channelID_property'
            )
        );

        return $channelID;
    }
    /* getChannelID() */



    /**
     * @param object $youtubeChannelModel
     * @param string $newChannelID
     *
     * @return void
     */
    static function
    setChannelID(
        stdClass $youtubeChannelModel,
        string $newChannelID
    ): void
    {
        $youtubeChannelModel->
        CB_YouTubeChannel_channelID_property =
        $newChannelID;
    }
    /* setChannelID() */



    /**
     * @param object $youtubeChannelModel
     *
     * @return string
     *
     *      Returns an empty string if the property value has not been set.
     */
    static function
    getTitle(
        stdClass $youtubeChannelModel
    ): string
    {
        $title =
        CBModel::valueToString(
            $youtubeChannelModel,
            'CB_YouTubeChannel_title_property'
        );

        return $title;
    }
    /* getTitle() */



    /**
     * @param object $youtubeChannelModel
     * @param string $newTitle
     *
     * @return void
     */
    static function
    setTitle(
        stdClass $youtubeChannelModel,
        string $newTitle
    ): void
    {
        $youtubeChannelModel->CB_YouTubeChannel_title_property =
        $newTitle;
    }
    /* setTitle() */

}
