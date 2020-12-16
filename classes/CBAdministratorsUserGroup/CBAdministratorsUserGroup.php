<?php

final class CBAdministratorsUserGroup {

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

}
