<?php

final class CBUserGroup {

    /* -- CBModel interfaces -- -- -- -- -- */



    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_build(stdClass $spec): stdClass {
        $CBID = CBModel::valueToString(
            $spec,
            'ID'
        );

        $userGroupClassName = CBModel::valueToString(
            $spec,
            'userGroupClassName'
        );

        $expectedCBID = CBUserGroup::userGroupClassNameToCBID($userGroupClassName);

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
            throw CBException::createWithValue(
                (
                    'The user group class name is not associated ' .
                    'with any user group'
                ),
                $userGroupClassName,
                'df16aa741c27d2eb8ceb6394ff2993cbf6e3b89f'
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
    }
    /* addUsers() */



    /**
     * @return [object]
     */
    static function fetchCBUserGroupModels(): array {
        static $models = null;

        if ($models === null) {
            $models = CBModels::fetchModelsByClassName2(
                'CBUserGroup'
            );
        }

        return $models;
    }



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
    }
    /* removeUsers() */



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

}
