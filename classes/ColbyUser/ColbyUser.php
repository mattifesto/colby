<?php

define('CBUserCookieName', 'colby-user-encrypted-data');



final class ColbyUser {

    private static $currentUser = null;

    /**
     * currentUserCBID, currentUserNumericID
     *
     * If we can authenticate the current logged in user we just store their
     * hash and ID, not the table row or anything else. The table row may be
     * changed by the site so caching it will only lead to possible stale data
     * bugs.
     */

    private static $currentUserCBID = null;
    private static $currentUserNumericID = null;
    private static $currentUserGroups = [];

    // currentUserRow
    // this is cached, see the following document for discussion
    // "Colby User Data and Permissions Caching"
    // this information will not change during a request
    // even if the database row is altered

    private static $currentUserRow = null;



    /**
     * @return bool
     */
    static function currentUserIsLoggedIn(): bool {
        return !empty(ColbyUser::$currentUserCBID);
    }



    /**
     * @deprecated 2019_11_28
     *
     *      Use CBUserGroup::currentUserIsMemberOfUserGroup().
     *
     * This function is more efficient than ColbyUser::isMemberOfGroup() because
     * it memoizes the results for the current user.
     *
     * @param string $groupName
     *
     *      This can either be a user group class name, such as
     *      "CBAdministratorsUserGroup", or a deprecated user group name, such
     *      as "Administrators".
     *
     * @return bool
     *
     * @see ColbyUser::clearCachedUserGroupsForCurrentUser()
     */
    static function currentUserIsMemberOfGroup(
        string $groupName
    ): bool {
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
    /* currentUserIsMemberOfGroup() */



    /**
     * @param int $facebookUserID
     *
     * @return object|null
     *
     *      {
     *          userCBID: CBID
     *
     *          userNumericID: int (deprecated)
     *      }
     */
    private static function facebookUserIDToCBUserIDs(
        int $facebookUserID
    ): ?stdClass {
        $SQL = <<<EOT

            SELECT  LOWER(HEX(hash)) AS userCBID,
                    id as userNumericID

            FROM    ColbyUsers

            WHERE   facebookId = {$facebookUserID}

        EOT;

        return CBDB::SQLToObjectNullable($SQL);
    }
    /* facebookUserIDToCBUserIDs() */



    /**
     * @return CBID|null
     *
     *      Returns the current user's CBID if a user is logged in; otherwise
     *      null.
     */
    static function getCurrentUserCBID(): ?string {
        return ColbyUser::$currentUserCBID;
    }
    /* getCurrentUserCBID() */



    /**
     * @deprecated use ColbyUser::getCurrentUserCBID()
     */
    static function getCurrentUserID(): ?string {
        return ColbyUser::getCurrentUserCBID();
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

            $userCBID = CBModel::valueAsID($cookie, 'userCBID');

            if ($userCBID === null) {
                ColbyUser::removeUserCookie();
                return;
            }

            $userNumericID = CBModel::valueAsInt($cookie, 'userNumericID');

            if ($userNumericID === null) {
                ColbyUser::removeUserCookie();
                return;
            }

            /* Success, the user is now logged in. */
            ColbyUser::$currentUserCBID = $userCBID;
            ColbyUser::$currentUserNumericID = $userNumericID;
        } catch (Throwable $exception) {
            CBErrorHandler::report($exception);
            ColbyUser::removeUserCookie();
        }
    }
    /* initialize() */



    /**
     * @deprecated 2019_11_28
     *
     *      Use CBUserGroup::userIsMemberOfUserGroup().
     *
     * ColbyUser::currentUserIsMemberOfGroup() is a more efficient alternative
     * for the current user.
     *
     * @param int $userNumericID
     * @param string $userGroupName
     *
     *      This can either be a user group class name, such as
     *      "CBAdministratorsUserGroup", or a deprecated user group name, such
     *      as "Administrators".
     *
     * @return bool
     */
    static function isMemberOfGroup(
        $userNumericID,
        string $userGroupName
    ): bool {
        if ($userGroupName === 'Public') {
            return true;
        }

        if (empty($userNumericID)) {

            /**
             * @NOTE 2019_11_28
             *
             *      This situation should actually throw an exception. I'm not
             *      making that change today because so much work is already
             *      being done in this area.
             */

            return false;
        }

        $userNumericIDAsSQL = intval($userNumericID);

        $tableName = ColbyUser::groupNameToTableName(
            $userGroupName
        );

        if ($tableName !== false) {
            $SQL = <<<EOT

                SELECT  COUNT(*)

                FROM    {$tableName}

                WHERE   userID = {$userNumericIDAsSQL}

            EOT;

            try {
                $isMember = CBDB::SQLToValue($SQL) > 0;

                return $isMember;
            } catch (Throwable $throwable) {
                // CBErrorHandler::report($throwable);
            }
        }

        /**
         * If the table has been removed check to see if a user is a member
         * of the group the new way here.
         */

        $userGroupModels = CBUserGroup::fetchCBUserGroupModels();

        $userGroupModel = cb_array_find(
            $userGroupModels,
            function ($userGroupModel) use ($userGroupName) {
                return (
                    $userGroupModel->userGroupClassName === $userGroupName ||
                    $userGroupModel->deprecatedGroupName === $userGroupName
                );
            }
        );

        if ($userGroupModel !== null) {
            $userCBIDs = CBUsers::userNumericIDsToUserCBIDs(
                [
                    $userNumericID,
                ]
            );

            if (empty($userCBIDs)) {
                return false;
            }

            $userCBID = $userCBIDs[0];

            $associations = CBModelAssociations::fetch(
                $userGroupModel->ID,
                'CBUserGroup_CBUser',
                $userCBID
            );

            if (count($associations) > 0) {
                return true;
            }
        }

        return false;
    }
    /* isMemberOfGroup() */



    /**
     * This function is called at after Facebook authenticates a user that wants
     * to log in. It updates the database and sets a cookie in the user's
     * browser confirms their identity and that they are logged in for future
     * page requests.
     *
     * Since it sets a cookie it must be called before any HTML is ouput.
     *
     * @param int $facebookUserID
     * @param string $facebookAccessToken
     * @param object $facebookName
     *
     * @return void
     */
    static function loginFacebookUser(
        int $facebookUserID,
        string $facebookAccessToken,
        string $facebookName
    ): void {
        $userSpec = ColbyUser::updateFacebookUser(
            $facebookUserID,
            $facebookAccessToken,
            $facebookName
        );

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
            'userCBID' => $userSpec->ID,

            /* deprecated */
            'userNumericID' => $userSpec->userNumericID,

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
     * @return void
     */
    static function clearCachedUserGroupsForCurrentUser(): void {
        ColbyUser::$currentUserGroups = [];
    }



    /**
     * The expiration timestamp is set to
     *
     *      current time - 24 hours
     *
     * because this value is guaranteed to be in the past in all time zones.
     *
     * This function must be called before any output is generated because it
     * sets a cookie.
     */
    private static function removeUserCookie() {
        $value = '';
        $expirationTimestamp = time() - (60 * 60 * 24);
        $path = '/';

        setcookie(
            CBUserCookieName,
            $value,
            $expirationTimestamp,
            $path
        );
    }



    /**
     * @param int $facebookUserID
     * @param string $facebookAccessToken
     * @param object $facebookName
     *
     * @return object
     *
     *      Returns the updated CBUser model for the user.
     */
    static function updateFacebookUser(
        int $facebookUserID,
        string $facebookAccessToken,
        string $facebookName
    ): stdClass {
        $isFirstUser = false;

        if ($facebookUserID <= 0) {
            throw CBException::createWithValue(
                'The Facebook user ID is invalid.',
                $facebookUserID,
                '2417ee1efc46f05e3ae75cd6050ea4c52c719a65'
            );
        }

        $CBUserIDs = ColbyUser::facebookUserIDToCBUserIDs(
            $facebookUserID
        );

        $facebookNameAsSQL = CBDB::stringToSQL($facebookName);

        Colby::query('START TRANSACTION');

        if ($CBUserIDs !== null) {
            $SQL = <<<EOT

                UPDATE  ColbyUsers

                SET     facebookName = {$facebookNameAsSQL}

                WHERE   id = {$CBUserIDs->userNumericID}

            EOT;

            Colby::query($SQL);
        } else {
            $CBUserIDs = (object)[
                'userCBID' => CBID::generateRandomCBID(),
            ];

            $userCBIDAsSQL = CBID::toSQL($CBUserIDs->userCBID);

            $SQL = <<<EOT

                INSERT INTO ColbyUsers (
                    hash,
                    facebookId,
                    facebookName
                ) VALUES (
                    {$userCBIDAsSQL},
                    {$facebookUserID},
                    {$facebookNameAsSQL}
                )

            EOT;

            Colby::query($SQL);

            $CBUserIDs->userNumericID = intval(Colby::mysqli()->insert_id);

            /* Detect first user */

            $SQL = <<<EOT

                SELECT  COUNT(*)
                FROM    ColbyUsers

            EOT;

            $count = CBDB::SQLToValue($SQL);

            if ($count === '1') {
                $isFirstUser = true;
            }
        }

        $updater = CBModelUpdater::fetch(
            (object)[
                'className' => 'CBUser',
                'ID' => $CBUserIDs->userCBID,
                'facebookAccessToken' => $facebookAccessToken,
                'facebookName' => $facebookName,
                'facebookUserID' => $facebookUserID,
                'lastLoggedIn' => time(),
                'title' => $facebookName,
                'userNumericID' => $CBUserIDs->userNumericID,
            ]
        );

        if ($updater->working != $updater->original) {
            CBModels::save($updater->working);
        }

        if ($isFirstUser) {
            CBUserGroup::addUsers(
                'CBAdministratorsUserGroup',
                $CBUserIDs->userCBID
            );

            CBUserGroup::addUsers(
                'CBDevelopersUserGroup',
                $CBUserIDs->userCBID
            );
        }

        /* All database updates are complete */

        Colby::query('COMMIT');

        return $updater->working;
    }
    /* updateFacebookUser() */



    /**
     * @param int $userNumericID
     * @param string $targetUserGroupName
     *
     *      This can either be a user group class names, such as
     *      "CBAdministratorsUserGroup", or a deprecated user group name, such
     *      as "Administrators".
     *
     * @param bool $isMember
     *
     * @return void
     */
    static function updateGroupMembership(
        int $targetUserNumericID,
        string $targetUserGroupName,
        bool $isMember
    ): void {
        $userGroupModels = CBUserGroup::fetchCBUserGroupModels();

        $userGroupModel = cb_array_find(
            $userGroupModels,
            function ($userGroupModel) use ($targetUserGroupName): bool {
                return (
                    $userGroupModel->userGroupClassName === $targetUserGroupName ||
                    $userGroupModel->deprecatedGroupName === $targetUserGroupName
                );
            }
        );

        if ($userGroupModel === null) {
            throw new CBExceptionWithValue(
                (
                    'The target user group name is not associated ' .
                    'with any user group.'
                ),
                $targetUserGroupName,
                'c9dc8a6a0d805cfb70e9fb51b1739df2481ffe53'
            );
        }

        $targetUserGroupClassName = $userGroupModel->userGroupClassName;

        $userCBIDs = CBUsers::userNumericIDsToUserCBIDs(
            [
                $targetUserNumericID,
            ]
        );

        if (count($userCBIDs) === 0) {
            throw CBException::createWithValue(
                'The target user numeric ID is not associated with any user.',
                $targetUserNumericID,
                '59ca6ae5fda96e84d0fa94374ff37ee0b2b07004'
            );
        }

        $targetUserCBID = $userCBIDs[0];

        if ($isMember) {
            CBUserGroup::addUsers(
                $targetUserGroupClassName,
                $targetUserCBID
            );
        } else {
            CBUserGroup::removeUsers(
                $targetUserGroupClassName,
                $targetUserCBID
            );
        }
    }
    /* updateGroupMembership() */

}



ColbyUser::initialize();
