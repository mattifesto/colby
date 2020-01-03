<?php

final class CBUsers {

    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $SQL = <<<EOT

            CREATE TABLE IF NOT EXISTS ColbyUsers (
                id                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                hash                BINARY(20) NOT NULL,
                email               VARCHAR(254),
                facebookId          BIGINT UNSIGNED,
                facebookName        VARCHAR(100) NOT NULL,

                PRIMARY KEY (id),

                UNIQUE KEY facebookId (
                    facebookId
                ),

                UNIQUE KEY hash (
                    hash
                ),

                UNIQUE KEY email (
                    email
                )
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_520_ci

        EOT;

        Colby::query($SQL);
    }
    /* CBInstall_install() */



    /* -- functions -- -- -- -- -- */



    /**
     * @deprecated 2019_08_29
     *
     *      This function was created as deprecated. It exists to support
     *      testing of upgrades from user numeric IDs to user CBIDs. When user
     *      numeric IDs go away completely, this function can be removed.
     *
     * @param CBID $userCBID
     *
     * @return int
     *
     *      This function will throw an exception if the $userCBID parameter
     *      does not represent an actual user.
     */
    static function forTesting_userCBIDtoUserNumericID(
        string $userCBID
    ): int {
        $userCBIDAsSQL = CBID::toSQL($userCBID);

        $SQL = <<<EOT

            SELECT      id

            FROM        ColbyUsers

            WHERE       hash = {$userCBIDAsSQL}

        EOT;

        $userNumericID = CBConvert::valueAsInt(
            CBDB::SQLToValue($SQL)
        );

        if ($userNumericID === null) {
            throw new CBExceptionWithValue(
                'The userCBID parameter does not represent an actual user.',
                $userCBID,
                '0c2a5d93283b730cf3baa4919a561123a4a33183'
            );
        }

        return $userNumericID;
    }
    /* forTesting_userCBIDtoUserNumericID() */



    /**
     * @deprecated use CBUserGroup models
     *
     * @return void
     */
    static function installUserGroup(string $userGroupName): void {
    }



    /**
     * @param [int]
     *
     * @return [CBID]
     *
     *      If a user numeric ID is not valid, this function will return a
     *      smaller array than the array passed in. If you care, check the
     *      length of the returned array.
     */
    static function userNumericIDsToUserCBIDs(array $userNumericIDs): array {
        $values = array_map(
            function ($userNumericID) {
                $value = CBConvert::valueAsInt($userNumericID);

                if ($value === null) {
                    throw CBException::createWithValue(
                        'This value is not a valid integer.',
                        $userNumericID,
                        'd733d6d67e5dcb5e65ef1acc2f5f0cec1d5b260f'
                    );
                }

                return $value;
            },
            $userNumericIDs
        );

        $values = implode(
            ',',
            $values
        );

        $SQL = <<<EOT

            SELECT      LOWER(HEX(hash))

            FROM        ColbyUsers

            WHERE       ID IN ({$values})

        EOT;

        $userCBIDs = CBDB::SQLToArrayOfNullableStrings($SQL);

        return $userCBIDs;
    }
    /* userNumericIDsToUserCBIDs() */



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
