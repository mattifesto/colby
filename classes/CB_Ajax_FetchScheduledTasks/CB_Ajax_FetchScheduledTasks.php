<?php

final class
CB_Ajax_FetchScheduledTasks
{
    // -- CBAjax interfaces



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
    ): stdClass
    {
        $returnValue =
        (object)
        [
            'CB_Ajax_FetchScheduledTasks_scheduledTasks' =>
            CBTasks2::fetchScheduledTasks(),
        ];

        return $returnValue;
    }
    // CBAjax_execute()



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

        $userIsADeveloper =
        CBUserGroup::userIsMemberOfUserGroup(
            $callingUserModelCBID,
            'CBDevelopersUserGroup'
        );

        return $userIsADeveloper;
    }
    // CBAjax_userModelCBIDCanExecute()

}
