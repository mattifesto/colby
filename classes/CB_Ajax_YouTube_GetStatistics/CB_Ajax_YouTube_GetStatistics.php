<?php

final class
CB_Ajax_YouTube_GetStatistics
{
    /* -- CBAjax interfaces -- */



    /**
     * @param object $executorArguments
     * @param string|null $callingUserModelCBID
     *
     * @return object|null
     */
    static function
    CBAjax_execute(
        stdClass $executorArguments,
        ?string $callingUserModelCBID = null
    ): array
    {
        $youtubeChannelModelCBID =
        CBModel::valueAsCBID(
            $executorArguments,
            'CB_Ajax_YouTube_GetStatistics_youtubeChannelModelCBID_parameter'
        );

        if (
            $youtubeChannelModelCBID ===
            null
        ) {
            $message =
            CBConvert::stringToCleanLine(<<<EOT

                The value of the
                CB_Ajax_YouTube_GetStatistics_youtubeChannelModelCBID_parameter
                is not a valid CBID.

            EOT);

            throw
            new CBExceptionWithValue(
                $message,
                $executorArguments,
                '3186ed4802ae8bfc4f4935570e81fe42e90fd9d1'
            );
        }

        $youtubeStatisticsModels =
        CB_YouTubeStatistics::fetchRecentStatistics(
            $youtubeChannelModelCBID,
            7
        );

        return $youtubeStatisticsModels;
    }
    /* CBAjax_execute() */



    /**
     * @param CBID $callingUserModelCBID
     *
     * @return bool
     */
    static function
    CBAjax_userModelCBIDCanExecute(
        ?string $callingUserModelCBID = null
    ): bool
    {
        if (
            $callingUserModelCBID ===
            null
        ) {
            return false;
        }

        $userIsAnAdministrator =
        CBUserGroup::userIsMemberOfUserGroup(
            $callingUserModelCBID,
            'CBAdministratorsUserGroup'
        );

        return $userIsAnAdministrator;
    }
    /* CBAjax_userModelCBIDCanExecute() */

}
