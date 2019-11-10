<?php

define('CBUserCookieName', 'colby-user-encrypted-data');



final class ColbyUser {

    private static $currentUser = null;

    /**
     * currentUserID, currentUserNumericID
     *
     * If we can authenticate the current logged in user we just store their
     * hash and ID, not the table row or anything else. The table row may be
     * changed by the site so caching it will only lead to possible stale data
     * bugs.
     */

    private static $currentUserID = null;
    private static $currentUserNumericID = null;
    private static $currentUserGroups = [];

    // currentUserRow
    // this is cached, see the following document for discussion
    // "Colby User Data and Permissions Caching"
    // this information will not change during a request
    // even if the database row is altered

    private static $currentUserRow = null;



    /**
     * @deprecated 2019_07_16
     *
     *      Use the hexadecimal user ID instead of the numeric user ID. The
     *      hexadecimal user ID is returned by the function
     *      ColbyUser::getCurrentUserID()
     *
     * @return int|null
     *
     *      Returns the current user numeric ID if a user is logged in;
     *      otherwise null.
     */
    static function currentUserId(): ?int {
        return ColbyUser::$currentUserNumericID;
    }



    /**
     * @return bool
     */
    static function currentUserIsLoggedIn(): bool {
        return !empty(ColbyUser::$currentUserID);
    }



    /**
     * This function is more efficient than ColbyUser::isMemberOfGroup() because
     * it memoizes the results for the current user.
     *
     * @param string $groupName
     *
     * @return bool
     */
    static function currentUserIsMemberOfGroup($groupName) {
        if (isset(ColbyUser::$currentUserGroups[$groupName])) {
            return ColbyUser::$currentUserGroups[$groupName];
        } else {
            if (CBAdminPageForUpdate::installationIsRequired()) {
                error_log(
                    'Permissions granted from '
                    . __METHOD__
                    . '() because of first installation.'
                );

                $isMember = true;
            } else {
                $isMember = ColbyUser::isMemberOfGroup(
                    ColbyUser::$currentUserNumericID,
                    $groupName
                );
            }

            ColbyUser::$currentUserGroups[$groupName] = $isMember;

            return $isMember;
        }
    }



    /**
     * @param int $facebookUserID
     *
     * @return {id: int, hash: hex160}|false
     */
    private static function facebookUserIDtoUserIdentity($facebookUserID) {
        $facebookUserID = intval($facebookUserID);
        $SQL = <<<EOT

            SELECT  `ID`, LOWER(HEX(`hash`)) AS `hash`
            FROM    `ColbyUsers`
            WHERE   `facebookId` = {$facebookUserID}

        EOT;

        return CBDB::SQLToObject($SQL);
    }



    /**
     * @return [string]
     */
    static function fetchGroupNames() {
        $SQL = <<<EOT

            SELECT  `table_name`
            FROM    `information_schema`.`tables`
            WHERE   `table_schema` = DATABASE()

        EOT;

        $tableNames = CBDB::SQLToArray($SQL);
        $groupNames = [];

        foreach ($tableNames as $tableName) {
            if (
                preg_match(
                    '/^ColbyUsersWhoAre(.+)$/',
                    $tableName,
                    $matches
                )
            ) {
                $groupNames[] = $matches[1];
            }
        }

        return $groupNames;
    }



    /**
     * @return ID|null
     *
     * Returns the current user ID if a user is logged in; otherwise null.
     */
    static function getCurrentUserID(): ?string {
        return ColbyUser::$currentUserID;
    }
    /* getCurrentUserID() */



    /**
     * @param string $groupName
     *
     * @return string|false
     */
    static function groupNameToTableName($groupName) {
        if (
            !preg_match(
                '/^[a-zA-Z0-9]+$/',
                $groupName
            )
        ) {
            return false;
        }

        return "ColbyUsersWhoAre{$groupName}";
    }



    /**
     * This function should be run only once. It is run when this class is first
     * loaded.
     *
     * @return void
     */
    static function initialize(): void {
        if (!isset($_COOKIE[CBUserCookieName])) {
            return;
        }

        $cookieCipherData = $_COOKIE[CBUserCookieName];

        try {
            $cookie = CBConvert::valueAsObject(
                Colby::decrypt($cookieCipherData)
            );

            if ($cookie === null) {
                ColbyUser::removeUserCookie();
                return;
            }

            $expirationTimestamp = CBModel::valueAsInt(
                $cookie,
                'expirationTimestamp'
            ) ?? PHP_INT_MIN;

            if (time() > $expirationTimestamp) {
                ColbyUser::removeUserCookie();
                return;
            }

            $userID = CBModel::valueAsID($cookie, 'userID');

            if ($userID === null) {
                ColbyUser::removeUserCookie();
                return;
            }

            $userNumericID = CBModel::valueAsInt($cookie, 'userNumericID');

            if ($userNumericID === null) {
                ColbyUser::removeUserCookie();
                return;
            }

            /* Success, the user is now logged in. */
            ColbyUser::$currentUserID = $userID;
            ColbyUser::$currentUserNumericID = $userNumericID;
        } catch (Throwable $exception) {
            CBErrorHandler::report($exception);
            ColbyUser::removeUserCookie();
        }
    }
    /* initialize() */



    /**
     * ColbyUser::currentUserIsMemberOfGroup() is a more efficient alternative
     * for the current user.
     *
     * @param int $userID
     * @param string $groupName
     *
     * @return bool
     */
    static function isMemberOfGroup($userID, $groupName) {
        if ($groupName === 'Public') {
            return true;
        }

        if (empty($userID)) {
            return false;
        }

        $userIDAsSQL = intval($userID);
        $tableName = ColbyUser::groupNameToTableName($groupName);

        if ($tableName === false) {
            return false;
        }

        $SQL = <<<EOT

            SELECT COUNT(*)
            FROM `{$tableName}`
            WHERE `userId` = {$userIDAsSQL}

        EOT;

        try {
            $isMember = CBDB::SQLToValue($SQL) > 0;
        } catch (Throwable $exception) {
            return false;
        }

        return $isMember;
    }



    /**
     * This function is called at after Facebook authenticates a user that wants
     * to log in. It updates the database and sets a cookie in the user's
     * browser confirms their identity and that they are logged in for future
     * page requests.
     *
     * Since it sets a cookie it must be called before any HTML is ouput.
     *
     * @param int $facebookAccessExpirationTime
     *
     *  This is a unix timestamp representing the time in the future that
     *  the user's access expires. It's the current unix timestamp plus the
     *  duration of the user's access.
     *
     * @return void
     */
    static function loginCurrentUser(
        $facebookAccessToken,
        $facebookAccessExpirationTime,
        $facebookProperties
    ): void {
        $mysqli = Colby::mysqli();
        $facebookUserID = intval($facebookProperties->id);

        if ($facebookUserID <= 0) {
            throw new RuntimeException('The Facebook user ID is invalid.');
        }

        $userIdentity = ColbyUser::facebookUserIDtoUserIdentity(
            $facebookUserID
        );

        $sqlFacebookAccessToken = $mysqli->escape_string($facebookAccessToken);
        $sqlFacebookAccessToken = "'{$sqlFacebookAccessToken}'";

        $sqlFacebookAccessExpirationTime = "'{$facebookAccessExpirationTime}'";

        $sqlFacebookName = cbhtml($facebookProperties->name);
        $sqlFacebookName = $mysqli->escape_string($sqlFacebookName);
        $sqlFacebookName = "'{$sqlFacebookName}'";

        /**
         * @TODO
         *
         *      The ColbyUsers table is deprecated and will be removed. First
         *      name, last name, and time zone are deprecated.
         */

        Colby::query('START TRANSACTION');

        if ($userIdentity) {
            $sql = <<<EOT

                UPDATE
                    `ColbyUsers`
                SET
                    `facebookAccessToken` = {$sqlFacebookAccessToken},
                    `facebookAccessExpirationTime` =
                    {$sqlFacebookAccessExpirationTime},
                    `facebookName` = {$sqlFacebookName},
                    `facebookFirstName` = '',
                    `facebookLastName` = '',
                    `facebookTimeZone` = 0
                WHERE
                    `id` = {$userIdentity->ID}

            EOT;

            Colby::query($sql);
        }
        /* if */

        else {
            $userIdentity = (object)[
                'hash' => CBHex160::random(),
            ];

            $userHashAsSQL = CBHex160::toSQL($userIdentity->hash);

            $sql = <<<EOT

                INSERT INTO `ColbyUsers` (
                    `hash`,
                    `facebookId`,
                    `facebookAccessToken`,
                    `facebookAccessExpirationTime`,
                    `facebookName`,
                    `facebookFirstName`,
                    `facebookLastName`,
                    `facebookTimeZone`
                ) VALUES (
                    {$userHashAsSQL},
                    {$facebookUserID},
                    {$sqlFacebookAccessToken},
                    {$sqlFacebookAccessExpirationTime},
                    {$sqlFacebookName},
                    '',
                    '',
                    0
                )

            EOT;

            Colby::query($sql);

            $userIdentity->ID = intval($mysqli->insert_id);

            /* Detect first user */

            $count = CBDB::SQLToValue(
                'SELECT COUNT(*) FROM `ColbyUsers`'
            );

            if ($count === '1') {
                Colby::query(
                    "INSERT INTO `ColbyUsersWhoAreAdministrators` " .
                    "VALUES ({$userIdentity->ID}, NOW())"
                );

                Colby::query(
                    "INSERT INTO `ColbyUsersWhoAreDevelopers` " .
                    "VALUES ({$userIdentity->ID}, NOW())"
                );
            }
        }
        /* else */


        /**
         * Update the user model
         *
         * This update is forced because we update the full object each time so
         * enforcing sequential updates is not important.
         */

        $spec = (object)[
            'className' => 'CBUser',
            'ID' => $userIdentity->hash,
            'facebook'=> $facebookProperties,
            'lastLoggedIn' => time(),
            'title' => CBModel::value($facebookProperties, 'name', '', 'trim'),
            'userID' => $userIdentity->ID,
        ];

        CBModels::save([$spec], /* force */ true);

        /* All database updates are complete */

        Colby::query('COMMIT');

        /**
         * Set the Colby user cookie data.
         *
         * The only realistic way to best prevent cookie hijacking is to use
         * HTTPS. As soon as a site becomes relatively popular or makes
         * enough money to cover the cost, switch. This is what Facebook and
         * Twitter did. It doesn't prevent physical access attacks, but that's
         * pretty tough to do.
         */

        $cookie = (object)[
            'userID' => $userIdentity->hash,

            /* deprecated */
            'userNumericID' => $userIdentity->ID,

            /* 24 hours from now */
            'expirationTimestamp' => time() + (60 * 60 * 24),
        ];

        $encryptedCookie = Colby::encrypt($cookie);

        /**
         * TODO: If site uses HTTPS set parameter that only allows cookies to
         * be transmitted over secure connections.
         */

        setcookie(
            CBUserCookieName,
            $encryptedCookie,
            time() + (60 * 60 * 24 * 30),
            '/'
        );
    }
    /* loginCurrentUser() */



    /**
     * This function must be called before any output is generated because it
     * sets a cookie.
     */
    static function logoutCurrentUser() {
        ColbyUser::removeUserCookie();
    }



    /**
     * @param string $redirect
     *  The URL to go to after logging out.
     *
     *  This URL should not be escaped for use in HTML.
     *
     *  The URL can be URL encoded or not.
     *      (If a case is found where it needs to be one or the other,
     *       update this documentation.)
     *
     *  If no URL is provided, $_SERVER['REQUEST_URI'] will be used.
     *
     * @return string
     *  The URL to visit to log out.
     *
     *  This URL will be properly URL encoded.
     *
     *  This URL will not be escaped for use in HTML.
     */
    static function logoutURL($redirectURL = null) {
        if (!$redirectURL) {
            $redirectURL = $_SERVER['REQUEST_URI'];
        }

        $state = new stdClass();
        $state->colby_redirect_uri = $redirectURL;

        $URL =
        cbsiteurl() .
        '/colby/logout/?state=' .
        urlencode(
            json_encode($state)
        );

        return $URL;
    }



    /**
     * This function must be called before any output is generated because it
     * sets a cookie.
     */
    private static function removeUserCookie() {
        // time = now - 1 day
        // sure to be in the past in all time zones

        $time = time() - (60 * 60 * 24);

        setcookie(CBUserCookieName, '', $time, '/');
    }



    /**
     * @param int $userNumericID
     * @param string $group
     * @param bool $isMember
     *
     * @return void
     */
    static function updateGroupMembership(
        int $userNumericID,
        string $groupName,
        bool $isMember
    ): void {
        $tableName = ColbyUser::groupNameToTableName($groupName);

        if ($isMember) {
            $SQL = <<<EOT

                INSERT IGNORE INTO {$tableName}
                VALUES (
                    {$userNumericID},
                    NOW()
                )

            EOT;
        } else {
            $SQL = <<<EOT

                DELETE FROM {$tableName}
                WHERE userId = {$userNumericID}

            EOT;
        }

        Colby::query($SQL);
    }
    /* updateGroupMembership() */

}

ColbyUser::initialize();
