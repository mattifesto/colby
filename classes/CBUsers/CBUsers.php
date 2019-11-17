<?php

final class CBUsers {

    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $SQL = <<<EOT

            CREATE TABLE IF NOT EXISTS ColbyUsers (
                id                              BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                hash                            BINARY(20) NOT NULL,
                facebookId                      BIGINT UNSIGNED NOT NULL,
                facebookName                    VARCHAR(100) NOT NULL,

                PRIMARY KEY (id),

                UNIQUE KEY facebookId (
                    facebookId
                ),

                UNIQUE KEY hash (
                    hash
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
     * @param string $name
     *
     *      The name of the group such as 'Administrators' or
     *      'LEWholesaleCustomers'.
     *
     * @return void
     */
    static function installUserGroup(string $userGroupName): void {
        if (CBConvert::valueAsName($userGroupName) === null) {
            throw new CBException(
                "The \$userGroupName parameter value \"{$userGroupName}\" " .
                "is not valid.",
                '',
                'ed62068612672b934db76b46103f037a281cb252'
            );
        }

        $SQL = <<<EOT

            CREATE TABLE IF NOT EXISTS ColbyUsersWhoAre{$userGroupName} (
                userId    BIGINT UNSIGNED NOT NULL,
                added     DATETIME NOT NULL,

                PRIMARY KEY (userId),

                CONSTRAINT ColbyUsersWhoAre{$userGroupName}_userId
                    FOREIGN KEY (userId)
                    REFERENCES ColbyUsers (id)
                    ON DELETE CASCADE
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_520_ci

        EOT;

        Colby::query($SQL);
    }
    /* installUserGroup() */



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
