<?php

final class
CB_Username {

    /* -- CBAjax interfaces -- */



    /**
     * @param object $args
     *
     *      {
     *          targetUserCBID: CBID
     *      }
     *
     * @return string
     */
    static function
    CBAjax_CB_Username_ajax_fetchUsernameByUserCBID(
        stdClass $args
    ): string {
        $targetUserCBID = CBModel::valueAsCBID(
            $args,
            'targetUserCBID'
        );

        if ($targetUserCBID === null) {
            if ($targetUserCBID === null) {
                throw new CBExceptionWithValue(
                    'The "targetUserCBID" argument is not valid.',
                    $args,
                    '067baca0eb194029dccd3bc41b35ef4a8f10e70f'
                );
            }
        }

        $currentUserCBID = ColbyUser::getCurrentUserCBID();

        if (
            $targetUserCBID !== $currentUserCBID &&
            !CBUserGroup::userIsMemberOfUserGroup(
                $currentUserCBID,
                'CBAdministratorsUserGroup'
            )
        ) {
            throw new CBException(
                CBConvert::stringToCleanLine(<<<EOT

                    The current user does not have permission to call this ajax
                    function.

                EOT),
                '',
                '170dfc9a3a8e2824ef69137b9fa971e9472306cb'
            );
        }

        $usernameModelCBID = CB_Username::fetchUsernameCBIDByUserCBID(
            $targetUserCBID
        );

        if (
            $usernameModelCBID === null
        ) {
            return '';
        }

        $usernameModel = CBModels::fetchModelByCBID(
            $usernameModelCBID
        );

        if (
            $usernameModel === null
        ) {
            return '';
        }

        return CB_Username::getPrettyUsername(
            $usernameModel
        );
    }
    /* CBAjax_CB_Username_ajax_fetchUsernameByUserCBID() */



    /**
     * @return string
     */
    static function
    CBAjax_CB_Username_ajax_fetchUsernameByUserCBID_getUserGroupClassName(
    ): string {
        return 'CBPublicUserGroup';
    }
    /* CBAjax_CB_Username_ajax_fetchUsernameByUserCBID_getUserGroupClassName() */



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
        $targetUserCBID = CBModel::valueAsCBID(
            $args,
            'CB_Username_ajax_setUsername_targetUserCBID'
        );

        if (
            !CBID::valueIsCBID($targetUserCBID)
        ) {
            throw new CBExceptionWithValue(
                'The "targetUserCBID" argument is not valid.',
                $args,
                '0489d7de780341e68fea7f6ee5a107465693101e'
            );
        }

        $requestedUsername = CBModel::valueToString(
            $args,
            'CB_Username_ajax_setUsername_requestedUsername'
        );

        if (
            !CB_Username::isPrettyUsernameValid(
                $requestedUsername
            )
        ) {
            return (object)[
                'CB_Username_ajax_setUsername_message' => (
                    "\"${requestedUsername}\" is not a valid username"
                ),
            ];
        }

        $newUsernameSpec = CB_Username::createSpec(
            $requestedUsername,
            $targetUserCBID
        );

        $newUsernameModelCBID = CBModel::getCBID(
            $newUsernameSpec
        );

        $currentUsernameModelCBID = CB_Username::fetchUsernameCBIDByUserCBID(
            $targetUserCBID
        );

        if (
            $newUsernameModelCBID === $currentUsernameModelCBID
        ) {
            return (object)[
                'CB_Username_ajax_setUsername_succeeded' => true,
            ];
        }

        $newUsernameModel = CBModels::fetchModelByCBID(
            CBModel::getCBID(
                $newUsernameSpec
            )
        );

        if ($newUsernameModel !== null) {
            return (object)[
                'CB_Username_ajax_setUsername_message' => (
                    'This username is not available.'
                ),
            ];
        }

        CBDB::transaction(
            function (
            ) use (
                $newUsernameSpec
            ) {
                CBModels::save(
                    $newUsernameSpec
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



    /* -- CBModel interfaces -- */



    /**
     * @param object $usernameSpec
     *
     * @return object
     */
    static function
    CBModel_build(
        stdClass $usernameSpec
    ): stdClass {
        $usernameModel = (object)[];

        $usernameSpecActualCBID = CBModel::getCBID(
            $usernameSpec
        );

        $usernameSpecPrettyUsername = CB_Username::getPrettyUsername(
            $usernameSpec
        );

        $usernameSpecExpectedCBID = (
            CB_Username::prettyUsernameToUsernameModelCBID(
                $usernameSpecPrettyUsername
            )
        );

        if (
            $usernameSpecActualCBID !== $usernameSpecExpectedCBID
        ) {
            throw new CBExceptionWithValue(
                'This CB_Username spec does not have a valid CBID',
                $usernameSpec,
                '7cb2fd61a8962760c88f6446e1eb3cad8d1f9bd1'
            );
        }

        CB_Username::setPrettyUsername(
            $usernameModel,
            $usernameSpecPrettyUsername
        );

        CB_Username::setUserCBID(
            $usernameModel,
            CB_Username::getUserCBID(
                $usernameSpec
            )
        );

        return $usernameModel;
    }
    /* CBModel_build() */



    /* -- CBModels interfaces -- */



    /**
     * @param [object] $usernameModels
     *
     * @return void
     */
    static function
    CBModels_willSave(
        array $usernameModels
    ): void {
        foreach (
            $usernameModels as $usernameModel
        ) {
            $userCBID = CB_Username::getUserCBID(
                $usernameModel
            );

            $userModel = CBModels::fetchModelByCBID(
                $userCBID
            );

            $userModelClassName = CBModel::getClassName(
                $userModel
            );

            if (
                $userModelClassName !== 'CBUser'
            ) {
                throw new CBExceptionWithValue(
                    CBConvert::stringToCleanLine(<<<EOT

                        The user CBID for this CB_Username model does is not the
                        CBID of a CBUser model.

                    EOT),
                    $usernameModel,
                    'e878f10b66ed483cf03f74551d101e01866fbfae'
                );
            }

            $associations = CBModelAssociations::fetch(
                $userCBID,
                'CBUser_to_CB_Username_association'
            );

            /**
             * Unless some sort of error has occurred, there will be at most one
             * associated username for a user, but if there are multiple, they
             * should all be deleted.
             */
            foreach (
                $associations as $association
            ) {
                $associatedUsernameModelCBID = $association->associatedID;

                CBModels::deleteByID(
                    $associatedUsernameModelCBID
                );
            }

            CBModelAssociations::add(
                $userCBID,
                'CBUser_to_CB_Username_association',
                CBModel::getCBID(
                    $usernameModel
                )
            );
        }
    }
    /* CBModels_willSave() */



    /**
     * @param [CBID] $CBIDs
     *
     * @return void
     */
    static function
    CBModels_willDelete(
        array $usernameModelCBIDs
    ): void {
        foreach (
            $usernameModelCBIDs as $usernameModelCBID
        ) {
            CBModelAssociations::delete(
                null,
                'CBUser_to_CB_Username_association',
                $usernameModelCBID
            );

        }
    }
    /* CBModels_willDelete() */



    /* -- accessors -- */



    /**
     * @param object $usernameModel
     *
     * @return string
     */
    static function
    getPrettyUsername(
        stdClass $usernameModel
    ): string {
        return CBModel::valueToString(
            $usernameModel,
            'CB_Username_prettyUsername'
        );
    }
    /* getUsername() */



    /**
     * @param object $usernameModel
     * @param string $passwordHash
     *
     * @return void
     */
    static function
    setPrettyUsername(
        stdClass $usernameModel,
        string $prettyUsername
    ): void {
        $prettyUsernameIsValid = CB_Username::isPrettyUsernameValid(
            $prettyUsername
        );

        if (
            $prettyUsernameIsValid !== true
        ) {
            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    CB_Username::setPrettyUsername() can't be called with an
                    invalid pretty username.

                EOT),
                $prettyUsername,
                'bd58123f8ee2e8c45ac8014cdf30dd6a472b8813'
            );
        }

        $canonicalUsername = CB_Username::prettyUsernameToCanonicalUsername(
            $prettyUsername
        );

        $usernameModel->CB_Username_prettyUsername = $prettyUsername;
        $usernameModel->CB_Username_canonicalUsername = $canonicalUsername;
    }
    /* setPrettyUsername() */



    /**
     * @param object $usernameModel
     *
     * @return string|null
     */
    static function
    getUserCBID(
        stdClass $usernameModel
    ): ?string {
        return CBModel::valueAsCBID(
            $usernameModel,
            'CB_Username_userCBID'
        );
    }
    /* getUserCBID() */



    /**
     * @param object $usernameModel
     *
     * @return void
     */
    static function
    setUserCBID(
        stdClass $usernameModel,
        string $userCBID
    ): void {
        $valueIsCBID = CBID::valueIsCBID(
            $userCBID
        );

        if ($valueIsCBID !== true) {
            throw new CBExceptionWithValue(
                'The $userCBID argument is not a CBID.',
                $userCBID,
                'eb69683965e18e5c8f578463931ae355bb7fcb46'
            );
        }

        $usernameModel->CB_Username_userCBID = $userCBID;
    }
    /* setUserCBID() */



    /* -- functions -- -- -- -- -- */



    /**
     * This function will return a spec with its CBID set to the appropriate
     * CBID for the username.
     *
     * @param string $prettyUsername
     * @param CBID $userCBID
     *
     * @return object
     */
    static function
    createSpec(
        string $prettyUsername,
        string $userCBID
    ): object {
        $usernameModelCBID = CB_Username::prettyUsernameToUsernameModelCBID(
            $prettyUsername
        );

        $usernameSpec = CBModel::createSpec(
            'CB_Username',
            $usernameModelCBID
        );

        CB_Username::setPrettyUsername(
            $usernameSpec,
            $prettyUsername
        );

        CB_Username::setUserCBID(
            $usernameSpec,
            $userCBID
        );

        return $usernameSpec;
    }
    /* createSpec() */



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
            $currentUserCBID = ColbyUser::getCurrentUserCBID();

            if (
                $currentUserCBID === null
            ) {
                $currentUserPrettyUsername = null;
            } else {
                $currentUserUsernameModelCBID = (
                    CB_Username::fetchUsernameCBIDByUserCBID(
                        $currentUserCBID
                    )
                );

                if (
                    $currentUserUsernameModelCBID === null
                ) {
                    $currentUserPrettyUsername = null;
                } else {
                    $currentUserUsernameModel = CBModels::fetchModelByCBID(
                        $currentUserUsernameModelCBID
                    );

                    $currentUserPrettyUsername = CB_Username::getPrettyUsername(
                        $currentUserUsernameModel
                    );
                }
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
     * @return stdClass
     *
     *      The username in the returned spec been confirmed to be available.
     */
    static function
    generateRandomUsernameSpec(
        string $userCBID
    ): stdClass {
        $randomCBID = CBID::generateRandomCBID();

        while (true) {
            $randomPrettyUsername = (
                'user_' .
                mb_substr(
                    $randomCBID,
                    0,
                    25
                )
            );

            $randomUsernameSpec = CB_Username::createSpec(
                $randomPrettyUsername,
                $userCBID
            );

            $usernameModelCBID = CBModel::getCBID(
                $randomUsernameSpec
            );

            $existingUsernameModel = CBModels::fetchModelByCBID(
                $usernameModelCBID
            );

            if ($existingUsernameModel === null) {
                break;
            }
        }

        return $randomUsernameSpec;
    }
    /* generateRandomUsername() */



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
            '/^[a-zA-Z0-9_]{5,30}$/',
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
