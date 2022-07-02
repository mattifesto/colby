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
            throw
            new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    The value of the
                    CB_Ajax_YouTube_GetStatistics_youtubeChannelModelCBID_parameter
                    is not a valid CBID.

                EOT),
                $executorArguments,
                '3186ed4802ae8bfc4f4935570e81fe42e90fd9d1'
            );
        }

        $associations =
        CBModelAssociations::fetchModelAssociationsByFirstCBIDAndAssociationKey(
            $youtubeChannelModelCBID,
            'CB_YouTubeStatistics_association',
            'descending',
            100
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
