<?php

final class
CB_Ajax_User_UpdateProfile
{
    // -- CBAjax interfaces



    /**
     * @param object $executorArguments
     *
     *      {
     *          CB_Ajax_User_UpdateProfile_targetUserModelCBID_argument:
     *          <CBID>,
     *
     *          CB_Ajax_User_UpdateProfile_targetUserBio_argument:
     *          <string>,
     *
     *          CB_Ajax_User_UpdateProfile_targetUserFullName_argument:
     *          <string>,
     *      }
     *
     * @param CBID|null $callingUserModelCBID
     *
     * @return void
     */
    static function
    CBAjax_execute(
        stdClass $executorArguments,
        ?string $callingUserModelCBID = null
    ): void
    {
        $targetUserModelCBID =
        CBModel::valueAsCBID(
            $executorArguments,
            'CB_Ajax_User_UpdateProfile_targetUserModelCBID_argument'
        );

        $targetUserModelUpdater =
        new CBModelUpdater(
            $targetUserModelCBID
        );

        $targetUserSpec =
        $targetUserModelUpdater->getSpec();



        // bio

        $updatedTargetUserBio =
        CBModel::valueToString(
            $executorArguments,
            'CB_Ajax_User_UpdateProfile_targetUserBio_argument'
        );

        CBUser::setBio(
            $targetUserSpec,
            $updatedTargetUserBio
        );



        // full name

        $updatedTargetUserFullName =
        CBModel::valueToString(
            $executorArguments,
            'CB_Ajax_User_UpdateProfile_targetUserFullName_argument'
        );

        CBUser::setName(
            $targetUserSpec,
            $updatedTargetUserFullName
        );



        // save

        CBDB::transaction(
            function () use (
                $targetUserModelUpdater
            ): void
            {
                $targetUserModelUpdater->save2();
            }
        );
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
        /**
         * Users who are not signed in can't update any user's profile.
         */

        if (
            $callingUserModelCBID ===
            null
        ) {
            return false;
        }



        /**
         * A user can update their own user profile.
         */

        $currentUseModelCBID =
        ColbyUser::getCurrentUserCBID();

        if (
            $callingUserModelCBID ===
            $currentUseModelCBID
        ) {
            return true;
        }



        /**
         * An administrator can update any user's profile.
         */

        $currentUserIsAnAdministrator =
        CBUserGroup::userIsMemberOfUserGroup(
            $currentUseModelCBID,
            'CBAdministratorsUserGroup'
        );

        return $currentUserIsAnAdministrator;
    }
    // CBAjax_userModelCBIDCanExecute()

}
