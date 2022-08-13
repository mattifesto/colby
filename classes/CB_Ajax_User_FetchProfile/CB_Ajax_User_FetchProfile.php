<?php

/**
 * This Ajax function pulls profile information from a user model that is
 * editable by either the user or an administrator.
 */
final class
CB_Ajax_User_FetchProfile
{
    // -- CBAjax interfaces



    /**
     * @param object $executorArguments
     *
     *      {
     *          CB_Ajax_User_FetchProfile_targetUserModelCBID_argument:
     *          <CBID>,
     *      }
     *
     * @param CBID|null $callingUserModelCBID
     *
     * @return object
     *
     *      {
     *          CB_Ajax_User_FetchProfile_bio:
     *          <string>,
     *
     *          CB_Ajax_User_FetchProfile_fullName:
     *          <string>,
     *      }
     */
    static function
    CBAjax_execute(
        stdClass $executorArguments,
        ?string $callingUserModelCBID = null
    ): stdClass
    {
        $targetUserModelCBID =
        CBModel::valueAsCBID(
            $executorArguments,
            'CB_Ajax_User_FetchProfile_targetUserModelCBID_argument'
        );

        $targetUserModel =
        CBModelCache::fetchModelByID(
            $targetUserModelCBID
        );

        $targetUserFullName =
        CBUser::getName(
            $targetUserModel
        );

        $targetUserBio =
        CBUser::getBio(
            $targetUserModel
        );

        $profile =
        (object)
        [
            'CB_Ajax_User_FetchProfile_bio' =>
            $targetUserBio,

            'CB_Ajax_User_FetchProfile_fullName' =>
            $targetUserFullName,
        ];

        return $profile;
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
         * Users who are not signed in can't fetch any user's profile.
         */

        if (
            $callingUserModelCBID ===
            null
        ) {
            return false;
        }



        /**
         * A user can fetch their own user profile.
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
         * An administrator can fetch any user's profile.
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
