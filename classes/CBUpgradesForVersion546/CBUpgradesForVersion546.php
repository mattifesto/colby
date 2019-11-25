<?php

final class CBUpgradesForVersion546 {

    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        CBDBA::dropTableKey(
            'ColbyUsers',
            'hasBeenVerified_facebookLastName'
        );

        CBDBA::dropTableColumn(
            'ColbyUsers',
            'facebookAccessToken'
        );

        CBDBA::dropTableColumn(
            'ColbyUsers',
            'facebookAccessExpirationTime'
        );

        CBDBA::dropTableColumn(
            'ColbyUsers',
            'facebookFirstName'
        );

        CBDBA::dropTableColumn(
            'ColbyUsers',
            'facebookLastName'
        );

        CBDBA::dropTableColumn(
            'ColbyUsers',
            'facebookTimeZone'
        );

        CBDBA::dropTableColumn(
            'ColbyUsers',
            'hasBeenVerified'
        );
    }
    /* CBInstall_install() */



    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBUsers',
        ];
    }
    /* CBInstall_requiredClassNames() */

}
