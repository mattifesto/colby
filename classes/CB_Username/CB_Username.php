<?php

/**
 * @deprecated 2022_10_25_1666740439
 *
 *      This class is deprecated because the username is stored in the CBUser
 *      model now.
 *
 *      Some username related functions are still used but should eventually be
 *      moved to CBUser.
 */
final class
CB_Username
{
    /* -- CBAjax interfaces -- */



    /**
     * @deprecated 2022_10_25_1666740328
     *
     *      This function no longer works but I don't have time to officially
     *      deprecate it right now. User
     *      CB_Ajax_User_PrettyUsernameToUserModelCBID instead.
     *
     * @param object $args
     *
     *      {
     *          prettyUsername: string
     *      }
     *
     * @return CBID|null
     */
    static function
    CBAjax_CB_Username_ajax_fetchUserModelCBIDByPrettyUsername(
        stdClass $args
    ): ?string {
        $prettyUsername = CBModel::valueToString(
            $args,
            'prettyUsername'
        );

        if (
            !CB_Username::isPrettyUsernameValid(
                $prettyUsername
            )
        ) {
            return null;
        }

        return CB_Username::fetchUserCBIDByUsernameCBID(
            CB_Username::prettyUsernameToUsernameModelCBID(
                $prettyUsername
            )
        );
    }
    /* CBAjax_CB_Username_ajax_fetchUserModelCBIDByPrettyUsername() */



    /**
     * @return string
     */
    static function
    CBAjax_CB_Username_ajax_fetchUserModelCBIDByPrettyUsername_getUserGroupClassName(
    ): string {
        return 'CBPublicUserGroup';
    }
    /* CBAjax_CB_Username_ajax_fetchUserModelCBIDByPrettyUsername() */



    /**
     * @param object $args
     *
     *  {
     *      CB_Username_ajax_setUsername_targetUserCBID: CBID
     *      CB_Username_ajax_setUsername_requestedUsername: string
     *  }
     *
     * @return object
     *
     *  {
     *      CB_Username_ajax_setUsername_succeeded: bool
     *      CB_Username_ajax_setUsername_message: string
     *  }
     */
    static function
    CBAjax_CB_Username_ajax_setUsername(
        stdClass $args
    ): stdClass {

        /* verify target user CBID */

        $targetUserModelCBID = CBModel::valueAsCBID(
            $args,
            'CB_Username_ajax_setUsername_targetUserCBID'
        );

        if (
            !CBID::valueIsCBID(
                $targetUserModelCBID
            )
        ) {
            throw new CBExceptionWithValue(
                'The target user model CBID argument is not valid.',
                $args,
                '0489d7de780341e68fea7f6ee5a107465693101e'
            );
        }


        /* verify requested pretty username */

        $requestedPrettyUsername = CBModel::valueToString(
            $args,
            'CB_Username_ajax_setUsername_requestedUsername'
        );

        if (
            !CB_Username::isPrettyUsernameValid(
                $requestedPrettyUsername
            )
        ) {
            return (object)[
                'CB_Username_ajax_setUsername_message' => (
                    "\"${requestedPrettyUsername}\" is not a valid username"
                ),
            ];
        }

        /* verify target user spec */

        $targetUserSpec = CBModels::fetchSpecByCBID(
            $targetUserModelCBID
        );

        $targetUserSpecClassName = CBModel::getClassName(
            $targetUserSpec
        );

        if (
            $targetUserSpecClassName !== 'CBUser'
        ) {
            return (object)[
                'CB_Username_ajax_setUsername_message' => (
                    CBConvert::stringToCleanLine(<<<EOT

                        The target user model CBID is not the CBID of a user
                        model.

                    EOT)
                ),
            ];
        }

        /* verify the request pretty username is different */

        $currentPrettyUsername = CBUser::getPrettyUsername(
            $targetUserSpec
        );

        if (
            $requestedPrettyUsername === $currentPrettyUsername
        ) {
            return (object)[
                'CB_Username_ajax_setUsername_succeeded' => true,
            ];
        }

        /* verify requested pretty username is available */

        $userModelCBID = CBUser::prettyUsernameToUserModelCBID(
            $requestedPrettyUsername
        );

        if (
            $userModelCBID !== null &&
            $userModelCBID !== $targetUserModelCBID
        ) {
            return (object)[
                'CB_Username_ajax_setUsername_message' => (
                    'This username is not available.'
                ),
            ];
        }


        /* set and save the requested pretty username */

        CBUser::setPrettyUsername(
            $targetUserSpec,
            $requestedPrettyUsername
        );

        CBDB::transaction(
            function (
            ) use (
                $targetUserSpec
            ) {
                CBModels::save(
                    $targetUserSpec
                );
            }
        );

        return (object)[
            'CB_Username_ajax_setUsername_succeeded' => true,
        ];
    }
    /* CBAjax_CB_Username_ajax_setUsername() */



    /**
     * @return string
     */
    static function
    CBAjax_CB_Username_ajax_setUsername_getUserGroupClassName(
    ): string {
        return 'CBPublicUserGroup';
    }
    /* CBAjax_CB_Username_ajax_setUsername_getUserGroupClassName() */



    /* -- CBInstall interfaces -- */



    /**
     * CB_Username models have been deprecated because the username is now
     * stored inside the CBUser model. This function removes all CB_Username
     * models and related assocations from the system.
     *
     * @return void
     */
    static function
    CBInstall_configure(
    ): void
    {
        $usernameCBIDs =
        CBModels::fetchCBIDsByClassName(
            'CB_Username'
        );

        CBDB::transaction(
            function () use (
                $usernameCBIDs
            ): void
            {
                CBModels::deleteByID(
                    $usernameCBIDs
                );

                CBModelAssociations::delete(
                    null,
                    'CBUser_to_CB_Username_association'
                );
            }
        );
    }
    // CBInstall_configure()



    /* -- CBModel interfaces -- */



    /**
     * @NOTE 2022_01_16
     *
     *      This function only exists so that if an existing CB_Username model
     *      is altered during install before we delete all the CB_Username
     *      mdoels there won't be a crash.
     *
     *      This function can be removed in version 676.
     *
     * @param object $usernameSpec
     *
     * @return object
     */
    static function
    CBModel_build(
        stdClass $usernameSpec
    ): stdClass {
        $usernameModel = (object)[];

        return $usernameModel;
    }
    /* CBModel_build() */



    /* -- functions -- -- -- -- -- */





    /**
     * This function is the fastest way to get the currently logged in user's
     * pretty username.
     *
     * @return string\null
     */
    static function
    fetchCurrentUserPrettyUsername(
    ): ?string {
        static $currentUserPrettyUsername = false;

        if (
            $currentUserPrettyUsername === false
        ) {
            $currentUserModelCBID = ColbyUser::getCurrentUserCBID();

            if (
                $currentUserModelCBID === null
            ) {
                $currentUserPrettyUsername = null;
            }

            else {
                $currentUserModel = CBModels::fetchModelByCBID(
                    $currentUserModelCBID
                );

                $currentUserPrettyUsername = CBUser::getPrettyUsername(
                    $currentUserModel
                );
            }
        }

        return $currentUserPrettyUsername;
    }
    /* fetchCurrentUserPrettyUsername() */



    /**
     * @param CBID $usernameModelCBID
     *
     * @return CBID|null
     */
    static function
    fetchUserCBIDByUsernameCBID(
        $usernameModelCBID
    ): ?string {
        return CBModelAssociations::fetchSingularFirstCBID(
            'CBUser_to_CB_Username_association',
            $usernameModelCBID
        );
    }
    /* fetchUserCBIDByUsernameCBID() */



    /**
     * @param CBID $userModelCBID
     *
     * @return CBID|null
     *
     *      Returns null if no username has been associated with the user.
     */
    static function
    fetchUsernameCBIDByUserCBID(
        string $userModelCBID
    ): ?string {
        return CBModelAssociations::fetchSingularSecondCBID(
            $userModelCBID,
            'CBUser_to_CB_Username_association',
        );
    }
    /* fetchUsernameCBIDByUserCBID() */






    /**
     * @param string $prettyUsername
     *
     * @return bool
     */
    static function
    isPrettyUsernameValid(
        string $prettyUsername
    ): bool {
        return !!preg_match(
            '/^[a-zA-Z0-9_]{4,30}$/',
            $prettyUsername
        );
    }
    /* isPrettyUsernameValid() */



    /**
     * @param string $prettyUsername
     *
     * @return string
     */
    private static function
    prettyUsernameToCanonicalUsername(
        string $prettyUsername
    ): string {
        $prettyUsernameIsValid = CB_Username::isPrettyUsernameValid(
            $prettyUsername
        );

        if (
            $prettyUsernameIsValid !== true
        ) {
            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    CB_Username::prettyUsernameToCanonicalUsername() can't be
                    called with an invalid pretty username.

                EOT),
                $prettyUsername,
                'b2188e6ae5eaf2897f5d2a19d1c1a2e7da17aa48'
            );
        }

        return mb_strtolower(
            $prettyUsername
        );
    }
    /* prettyUsernameToCanonicalUsername() */



    /**
     * @param string $prettyUsername
     *
     * @return CBID
     */
    static function
    prettyUsernameToUsernameModelCBID(
        string $prettyUsername
    ): string {
        $canonicalUsername = CB_Username::prettyUsernameToCanonicalUsername(
            $prettyUsername
        );

        return sha1(
            'b82abcdda73fb9644defb36b3c4e792d68e9bda5 ' .
            $canonicalUsername
        );
    }
    /* prettyUsernameToUsernameModelCBID() */

}
