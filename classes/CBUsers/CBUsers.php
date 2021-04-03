<?php

/**
 * @deprecated 2021_04_03
 *
 *      This class has been replaced by ColbyUsersTable. The remaining functions
 *      provided by this class should be moved and this class will eventually be
 *      deleted.
 */
final class CBUsers {

    /* -- CBInstall interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBInstall_requiredClassNames(
    ): array {
        return [
            'ColbyUsersTable',
        ];
    }
    /* CBInstall_requiredClassNames() */
    


    /* -- functions -- -- -- -- -- */



    /**
     * Returns the number of user accounts for the website. This function mainly
     * exists to return zero before the first user is logged in to detect that
     * they are the first user and add them to CBAdministratorsUserGroup and
     * CBDevelopersUserGroup.
     *
     * @return int
     */
    static function countOfUsers(): int {
        $SQL = <<<EOT

            SELECT  COUNT(*)
            FROM    CBModels
            WHERE   className = 'CBUser';

        EOT;

        return CBConvert::valueAsInt(
            CBDB::SQLToValue($SQL)
        );
    }
    /* countOfUsers() */



    /**
     * @deprecated use CBUserGroup models
     *
     * @return void
     */
    static function installUserGroup(string $userGroupName): void {
    }



    /**
     * @param string $userGroupName
     *
     * @return void
     */
    static function uninstallUserGroup(string $userGroupName): void {
        if (CBConvert::valueAsName($userGroupName) === null) {
            throw new CBException(
                "The \$userGroupName parameter value \"{$userGroupName}\" " .
                "is not valid.",
                '',
                'fc5c564cedc9fc1a692a6a359756bb82dcf89245'
            );
        }

        Colby::query(
            "DROP TABLE IF EXISTS ColbyUsersWhoAre{$userGroupName}"
        );
    }
    /* uninstallUserGroup() */

}
