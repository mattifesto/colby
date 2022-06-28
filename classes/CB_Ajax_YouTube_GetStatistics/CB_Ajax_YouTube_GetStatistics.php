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
    ): ?stdClass
    {
        $youtubeChannelID =
        CBSitePreferences::getYouTubeChannelID(
            CBSitePreferences::model()
        );

        if (
            $youtubeChannelID ===
            ''
        ) {
            return null;
        }

        $youtubeAPIKey =
        CBSitePreferences::getYouTubeAPIKey(
            CBSitePreferences::model()
        );

        if (
            $youtubeAPIKey ===
            ''
        ) {
            return null;
        }

        $returnValue =
        CB_YouTube::call(
            'channels',
            (object)
            [
                'id' =>
                $youtubeChannelID,

                'key' =>
                $youtubeAPIKey,

                'part' =>
                'statistics'
            ]
        );

        return $returnValue;
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
