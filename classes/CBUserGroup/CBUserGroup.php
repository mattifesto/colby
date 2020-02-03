<?php

final class CBUserGroup {

    private static $cacheOfAllCBUserGroupModels = null;



    /* -- CBAjax interfaces -- -- -- -- -- */



    /**
     * @param object $args
     *
     *      {
     *          userCBID: CBID
     *          userGroupClassName: string
     *      }
     *
     * @return void
     */
    static function CBAjax_addUser(
        stdClass $args
    ): void {
        $userCBID = CBModel::valueAsCBID(
            $args,
            'userCBID'
        );

        if ($userCBID === null) {
            throw new CBExceptionWithValue(
                'The "userCBID" property of args is not valid.',
                $args,
                'ddc570db55949127f08ff40b67fae5aa6df630b8'
            );
        }

        $userGroupClassName = CBModel::valueAsName(
            $args,
            'userGroupClassName'
        );

        if ($userGroupClassName === null) {
            throw new CBExceptionWithValue(
                'The "userGroupClassName" value of args is not valid.',
                $args,
                '7147b2ea55994fde889a92b2364eabf4c83664e2'
            );
        }

        CBUserGroup::addUsers(
            $userGroupClassName,
            [
                $userCBID
            ]
        );
    }
    /* CBAjax_addUser() */



    /**
     * @return string
     */
    static function CBAjax_addUser_getUserGroupClassName(): string {
        return 'CBAdministratorsUserGroup';
    }



    /**
     * @param object $args
     *
     *      {
     *          userCBID: CBID
     *          userGroupClassName: string
     *      }
     *
     * @return void
     */
    static function CBAjax_removeUser(
        stdClass $args
    ): void {
        $userCBID = CBModel::valueAsCBID(
            $args,
            'userCBID'
        );

        if ($userCBID === null) {
            throw new CBExceptionWithValue(
                'The "userCBID" property of args is not valid.',
                $args,
                '3d073c37dafada1191cb8d2a59da5c5d3c217238'
            );
        }

        $userGroupClassName = CBModel::valueAsName(
            $args,
            'userGroupClassName'
        );

        if ($userGroupClassName === null) {
            throw new CBExceptionWithValue(
                'The "userGroupClassName" value of args is not valid.',
                $args,
                'ce42ce842f63b51dec0c39af29c42c11c4462df3'
            );
        }

        CBUserGroup::removeUsers(
            $userGroupClassName,
            [
                $userCBID
            ]
        );
    }
    /* CBAjax_removeUser() */



    /**
     * @return string
     */
    static function CBAjax_removeUser_getUserGroupClassName(): string {
        return 'CBAdministratorsUserGroup';
    }



    /**
     * @param object $args
     *
     * @return bool
     */
    static function CBAjax_userIsMemberOfUserGroup(
        stdClass $args
    ): bool {
        $userCBID = CBModel::valueAsCBID(
            $args,
            'userCBID'
        );

        if ($userCBID === null) {
            throw new CBExceptionWithValue(
                'The "userCBID" property of args is not valid.',
                $args,
                '2f23a23a512bc0e0067bb0e2c5f03aa077df01ec'
            );
        }

        $userGroupClassName = CBModel::valueAsName(
            $args,
            'userGroupClassName'
        );

        if ($userGroupClassName === null) {
            throw new CBExceptionWithValue(
                'The "userGroupClassName" value of args is not valid.',
                $args,
                '96fdde8f16bc4b95c59ada9c7a834c2cd7ec934d'
            );
        }

        return CBUserGroup::userIsMemberOfUserGroup(
            $userCBID,
            $userGroupClassName
        );
    }
    /* CBAjax_userIsMemberOfUserGroup() */



    /**
     * @return string
     */
    static function CBAjax_userIsMemberOfUserGroup_getUserGroupClassName(): string {
        return 'CBAdministratorsUserGroup';
    }




    /* -- CBModel interfaces -- -- -- -- -- */



    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_build(
        stdClass $spec
    ): stdClass {
        $CBID = CBModel::valueToString(
            $spec,
            'ID'
        );

        $userGroupClassName = CBModel::valueToString(
            $spec,
            'userGroupClassName'
        );

        $expectedCBID = CBUserGroup::userGroupClassNameToCBID(
            $userGroupClassName
        );

        if ($CBID !== $expectedCBID) {
            throw CBException::createWithValue(
                'The CBID of the spec does not have the expected CBID.',
                $spec,
                '2353080672a490970ceef66dd42e4ce9240cef96'
            );
        }

        return (object)[
            'deprecatedGroupName' => CBModel::valueAsName(
                $spec,
                'deprecatedGroupName'
            ),

            'userGroupClassName' => $userGroupClassName,

            'title' => $userGroupClassName,
        ];
    }
    /* CBModel_build() */



    /* -- CBModels interfaces -- -- -- -- -- */



    /**
     * When any CBUserGroup models are deleted, the cache of all user group
     * models is cleared. This prevents bugs in the same request when a user
     * group is deleted.
     *
     * @return void
     */
    static function CBModels_willDelete(
        array $userGroupModelCBIDs
    ): void {
        CBUserGroup::clearCachedUserGroupModels();

        $userGroupModels = CBModels::fetchModelsByID2(
            $userGroupModelCBIDs
        );

        foreach ($userGroupModels as $userGroupModel) {
            $userGroupModelCBID = $userGroupModel->ID;

            CBModelAssociations::delete(
                $userGroupModelCBID,
                'CBUserGroup_CBUser'
            );

            CBModelAssociations::delete(
                null,
                'CBUser_CBUserGroup',
                $userGroupModelCBID
            );
        }
    }
    /* CBModels_willDelete() */



    /**
     * When any CBUserGroup models are saved the cache of all user group models
     * is cleared. This prevents bugs in the same request when a new user group
     * is added.
     *
     * @return void
     */
    static function CBModels_willSave(
        array $userGroupModels
    ): void {
        CBUserGroup::clearCachedUserGroupModels();
    }



    /* -- functions -- -- -- -- -- */



    /**
     * @TODO 2019_11_15
     *
     *      Consider confirming that the users are actual users.
     *
     * @return void
     */
    static function addUsers(
        string $userGroupClassName,
        $userCBIDs
    ): void {
        if (!is_array($userCBIDs)) {
            $userCBIDs = [$userCBIDs];
        }

        $userGroupCBID = CBUserGroup::userGroupClassNameToCBID(
            $userGroupClassName
        );

        $userGroupModel = CBModelCache::fetchModelByID($userGroupCBID);

        if (
            $userGroupModel === null ||
            $userGroupModel->className !== 'CBUserGroup'
        ) {
            throw CBExceptionWithValue(
                (
                    'The user group class name is not associated ' .
                    'with any user group'
                ),
                $userGroupClassName,
                'df16aa741c27d2eb8ceb6394ff2993cbf6e3b89f'
            );
        }

        $canModifyMembership = CBUserGroup::userCanModifyMembership(
            ColbyUser::getCurrentUserCBID(),
            $userGroupClassName
        );

        if ($canModifyMembership !== true) {
            throw new CBExceptionWithValue(
                (
                    'You do not have pemission to modify the ' .
                    'members of this group.'
                ),
                $userGroupClassName,
                '314f8b7a1ee1aba2eb1a640593d4c544e2c8a3f4'
            );
        }

        CBDB::transaction(
            function () use ($userGroupCBID, $userCBIDs) {
                $associations = array_map(
                    function ($userCBID) use ($userGroupCBID) {
                        return [
                            $userGroupCBID,
                            'CBUserGroup_CBUser',
                            $userCBID,
                        ];
                    },
                    $userCBIDs
                );

                CBModelAssociations::addMultiple($associations);

                $associations = array_map(
                    function ($userCBID) use ($userGroupCBID) {
                        return [
                            $userCBID,
                            'CBUser_CBUserGroup',
                            $userGroupCBID,
                        ];
                    },
                    $userCBIDs
                );

                CBModelAssociations::addMultiple($associations);
            }
        );

        $currentUserWasChanged = in_array(
            ColbyUser::getCurrentUserCBID(),
            $userCBIDs
        );
    }
    /* addUsers() */



    /**
     * @return void
     */
    static function clearCachedUserGroupModels(): void {
        CBUserGroup::$cacheOfAllCBUserGroupModels = null;
    }



    /**
     * @TODO 2019_11_28
     *
     *      This function may eventually support caching.
     *
     * @param string $userGroupClassName
     *
     *      Deprecated group names are not and will never be supported by this
     *      function.
     *
     * @return bool
     */
    static function currentUserIsMemberOfUserGroup(
        string $userGroupClassName
    ): bool {
        return CBUserGroup::userIsMemberOfUserGroup(
            ColbyUser::getCurrentUserCBID(),
            $userGroupClassName
        );
    }
    /* currentUserIsMemberOfUserGroup() */



    /**
     * @param string $deprecatedGroupName
     *
     * @return string|null
     */
    static function deprecatedGroupNameToUserGroupClassName(
        string $deprecatedGroupName
    ): ?string {
        if ($deprecatedGroupName === 'Public') {
            return 'CBPublicUserGroup';
        }

        $userGroupClassName = null;
        $userGroupModels = CBUserGroup::fetchAllUserGroupModels();

        foreach ($userGroupModels as $userGroupModel) {
            $userGroupDeprecatedGroupName = CBModel::valueAsName(
                $userGroupModel,
                'deprecatedGroupName'
            );

            if (
                $deprecatedGroupName ===
                $userGroupDeprecatedGroupName
            ) {
                $userGroupClassName = CBModel::valueAsName(
                    $userGroupModel,
                    'userGroupClassName'
                );
            }
        }

        return $userGroupClassName;
    }
    /* deprecatedGroupNameToUserGroupClassName() */



    /**
     * @deprecated use CBUserGroup::fetchAllUserGroupModels()
     */
    static function fetchCBUserGroupModels(): array {
        return CBUserGroup::fetchAllUserGroupModels();
    }



    /**
     * @return [object]
     */
    static function fetchAllUserGroupModels(): array {
        if (
            CBUserGroup::$cacheOfAllCBUserGroupModels === null
        ) {
            $userGroupModels = CBModels::fetchModelsByClassName2(
                'CBUserGroup'
            );

            CBUserGroup::$cacheOfAllCBUserGroupModels = $userGroupModels;
        }

        return CBUserGroup::$cacheOfAllCBUserGroupModels;
    }
    /* fetchAllUserGroupModels() */



    /**
     * @TODO 2019_11_15
     *
     *      Consider confirming that the users are actual users.
     *
     * @return void
     */
    static function removeUsers(
        string $userGroupClassName,
        $userCBIDs
    ): void {
        if (!is_array($userCBIDs)) {
            $userCBIDs = [$userCBIDs];
        }

        $userGroupCBID = CBUserGroup::userGroupClassNameToCBID(
            $userGroupClassName
        );

        $userGroupModel = CBModelCache::fetchModelByID($userGroupCBID);

        if (
            $userGroupModel === null ||
            $userGroupModel->className !== 'CBUserGroup'
        ) {
            throw CBException::createWithValue(
                (
                    'The user group class name is not associated ' .
                    'with any user group'
                ),
                $userGroupCBID,
                'df16aa741c27d2eb8ceb6394ff2993cbf6e3b89f'
            );
        }

        $canModifyMembership = CBUserGroup::userCanModifyMembership(
            ColbyUser::getCurrentUserCBID(),
            $userGroupClassName
        );

        if ($canModifyMembership !== true) {
            throw new CBExceptionWithValue(
                (
                    'You do not have pemission to modify the ' .
                    'members of this group.'
                ),
                $userGroupClassName,
                '314f8b7a1ee1aba2eb1a640593d4c544e2c8a3f4'
            );
        }

        CBDB::transaction(
            function () use ($userGroupCBID, $userCBIDs) {
                $associations = array_map(
                    function ($userCBID) use ($userGroupCBID) {
                        return [
                            $userGroupCBID,
                            'CBUserGroup_CBUser',
                            $userCBID,
                        ];
                    },
                    $userCBIDs
                );

                CBModelAssociations::deleteMultiple($associations);

                $associations = array_map(
                    function ($userCBID) use ($userGroupCBID) {
                        return [
                            $userCBID,
                            'CBUser_CBUserGroup',
                            $userGroupCBID,
                        ];
                    },
                    $userCBIDs
                );

                CBModelAssociations::deleteMultiple($associations);
            }
        );

        $currentUserWasChanged = in_array(
            ColbyUser::getCurrentUserCBID(),
            $userCBIDs
        );
    }
    /* removeUsers() */



    /**
     * @param CBID $userCBID
     * @param string $userGroupClassName
     *
     * @return bool
     *
     *      Returns true if the user is allowed to add and remove other users
     *      from the group.
     */
    static function userCanModifyMembership(
        string $userCBID,
        string $userGroupClassName
    ): bool {
        $function = (
            $userGroupClassName .
            '::CBUserGroup_userCanModifyMembership'
        );

        if (is_callable($function)) {
            $canModify = call_user_func($function, $userCBID);
        } else {
            $canModify = CBUserGroup::userIsMemberOfUserGroup(
                $userCBID,
                'CBAdministratorsUserGroup'
            );
        }

        /**
         * There are two situations that provide permission to modify group
         * membership outside the normal rules.
         *
         *      1. Developers can always modify group membership.
         *
         *      2. The user of a website with only one user can always modify
         *      group membership.
         */
        if ($canModify !== true) {
            $canModify = CBUserGroup::userIsMemberOfUserGroup(
                $userCBID,
                'CBDevelopersUserGroup'
            );
        }

        if ($canModify !== true) {
            $canModify = CBUsers::countOfUsers() < 2;
        }

        return $canModify;
    }
    /* userCanModifyMembership() */



    /**
     * @param string $userGroupClassName
     *
     * @return CBID
     */
    static function userGroupClassNameToCBID(
        string $userGroupClassName
    ): string {
        if (!CBConvert::valueIsName($userGroupClassName)) {
            throw CBException::createWithValue(
                'The userGroupClassName parameter is not a valid name.',
                $userGroupClassName,
                'f59516b5e0b2456f909779b31bf031a0b3a42edc'
            );
        }

        return sha1(
            "2b86df1bdd90b6e45c051709fcab4f6697dbdfe3 {$userGroupClassName}"
        );
    }
    /* userGroupClassNameToCBID() */



    /**
     * @param CBID|null $userCBID
     *
     *      Pass null if a user is not currently logged in. Often callers will
     *      use the result of ColbyUser::getCurrentUserCBID() as the paramter
     *      value.
     *
     * @param string $userGroupClassName
     *
     *      Deprecated group names are not and will never be supported by this
     *      function.
     *
     * @return bool
     */
    static function userIsMemberOfUserGroup(
        ?string $userCBID,
        string $userGroupClassName
    ): bool {
        if ($userGroupClassName === 'CBPublicUserGroup') {
            return true;
        }

        if ($userCBID === null) {
            return false;
        }

        $userGroupCBID = CBUserGroup::userGroupClassNameToCBID(
            $userGroupClassName
        );

        $associations = CBModelAssociations::fetch(
            $userGroupCBID,
            'CBUserGroup_CBUser',
            $userCBID
        );

        if (count($associations) > 0) {
            return true;
        } else {
            return false;
        }
    }
    /* userIsMemberOfUserGroup() */

}
