<?php

final class
CB_Ajax_Update_General
{
    // -- CBAjax interfaces



    /**
     * @return void
     */
    static function
    CBAjax_execute(
    ): void
    {
        CBAdminPageForUpdate::update();
    }
    // CBAjax_execute()



    /**
     * @param CBID callingUserModelCBID
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
            'CBDevelopersUserGroup'
        );

        return $userIsAnAdministrator;
    }
    // CBAjax_userModelCBIDCanExecute()

}
