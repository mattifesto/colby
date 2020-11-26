<?php

final class CBDevelopersUserGroup {

    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function
    CBInstall_install(
    ): void {
        $groupCBID = CBUserGroup::userGroupClassNameToCBID(
            __CLASS__
        );

        CBModelUpdater::update(
            (object)[
                'ID' => $groupCBID,
                'className' => 'CBUserGroup',
                'deprecatedGroupName' => 'Developers',
                'userGroupClassName' => __CLASS__,
            ]
        );
    }
    /* CBInstall_install() */



    /**
     * @return [string]
     */
    static function
    CBInstall_requiredClassNames(
    ): array {
        return [
            'CBUsers'
        ];
    }
    /* CBInstall_requiredClassNames() */



    /* -- CBUserGroup interfaces -- -- -- -- -- */



    /**
     * @param CBID $userCBID
     *
     * @return bool
     */
    static function
    CBUserGroup_userCanModifyMembership(
        string $userCBID
    ): bool {
        $isDeveloper = CBUserGroup::userIsMemberOfUserGroup(
            $userCBID,
            'CBDevelopersUserGroup'
        );

        return $isDeveloper;
    }
    /* CBUserGroup_userCanModifyMembership() */

}
