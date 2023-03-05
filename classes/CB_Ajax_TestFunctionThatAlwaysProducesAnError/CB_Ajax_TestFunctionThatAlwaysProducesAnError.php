<?php

final class
CB_Ajax_TestFunctionThatAlwaysProducesAnError
{
    // -- CBAjax interfaces



    /**
     * @param object $executorArguments
     * @param string|null $callingUserModelCBID
     *
     * @return void
     */
    static function
    CBAjax_execute(
        stdClass $executorArguments,
        ?string $callingUserModelCBID = null
    ): void
    {
        CBSlack::disable();

        throw new CBException(
            'text exception message',
            '(cbmessage (b)) exception message',
            '9d62abdd0f759759ec994242b9d1d748e62f702a'
        );
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

        $userIsAnAdministrator =
        CBUserGroup::userIsMemberOfUserGroup(
            $callingUserModelCBID,
            'CBAdministratorsUserGroup'
        );

        return $userIsAnAdministrator;
    }
    // CBAjax_userModelCBIDCanExecute()

}
