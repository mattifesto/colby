<?php

define('CBUserCookieName', 'colby-user-encrypted-data');

final class ColbyUser {
    private $hash = null;
    private $id = null;
    private $groups = array();

    private static $currentUser = null;

    /**
     * currentUserHash, currentUserId
     *
     * If we can authenticate the current logged in user we just store their
     * hash and ID, not the table row or anything else. The table row may be
     * changed by the site so caching it will only lead to possible stale data
     * bugs.
     */

    private static $currentUserHash = null;
    private static $currentUserId = null;
    private static $currentUserGroups = [];

    // currentUserRow
    // this is cached, see the following document for discussion
    // "Colby User Data and Permissions Caching"
    // this information will not change during a request
    // even if the database row is altered

    private static $currentUserRow = null;

    /**
     * @return ColbyUser
     */
    private function __construct() {
    }

    /**
     * @deprecated use ColbyUser::updateGroupMembership()
     *
     * This function adds a user to a group. It does no error handling and will
     * throw an exception if the user is already in the group or if the group
     * doesn't exist.
     *
     * @param int $userID
     * @param string $groupName
     *
     * @return null
     */
    static function addUserToGroup($userID, $groupName) {
        $groupName = CBDB::escapeString($groupName);
        $groupTableName = "ColbyUsersWhoAre{$groupName}";
        $userID = intval($userID);

        Colby::query("INSERT INTO `{$groupTableName}` VALUES ({$userID}, NOW())");
    }

    /**
     * @return ColbyUser
     */
    static function current() {
        if (!ColbyUser::$currentUser) {
            $user = new ColbyUser();
            $user->hash = ColbyUser::$currentUserHash;
            $user->id = ColbyUser::$currentUserId;
            ColbyUser::$currentUser = $user;
        }

        return ColbyUser::$currentUser;
    }

    /**
     * Returns the current user hash if a user is logged in; otherwise null.
     *
     * @return hex160|null
     */
    static function currentUserHash() {
        return ColbyUser::$currentUserHash;
    }

    /**
     * Returns the current user ID if a user is logged in; otherwise null.
     *
     * @return int|null
     */
    static function currentUserId() {
        return ColbyUser::$currentUserId;
    }

    /**
     * @return bool
     */
    static function currentUserIsLoggedIn(): bool {
        return !empty(ColbyUser::$currentUserHash);
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
                    ColbyUser::$currentUserId,
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
            if (preg_match('/^ColbyUsersWhoAre(.+)$/', $tableName, $matches)) {
                $groupNames[] = $matches[1];
            }
        }

        return $groupNames;
    }

    /**
     * @param hex160 $userHash
     *
     * @return object|false
     *
     *      Returns an object is the user is logged in; otherwise false.
     */
    static function fetchUserDataByHash($userHash) {
        if (empty($userHash)) {
            return false;
        }

        $userHashAsSQL = CBHex160::toSQL($userHash);
        $SQL = <<<EOT

            SELECT  `id`,
                    LOWER(HEX(`hash`)) as `hash`,
                    `facebookAccessToken`,
                    `facebookId`,
                    `facebookName`,
                    `facebookFirstName`,
                    `facebookLastName`
            FROM    `ColbyUsers`
            WHERE   `hash` = {$userHashAsSQL}

EOT;

        return CBDB::SQLToObject($SQL);
    }

    /**
     * @param string $groupName
     *
     * @return string|false
     */
    static function groupNameToTableName($groupName) {
        if (!preg_match('/^[a-zA-Z0-9]+$/', $groupName)) {
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
    static function initialize() {
        if (!isset($_COOKIE[CBUserCookieName])) {
            return;
        }

        $cookieCipherData = $_COOKIE[CBUserCookieName];

        try {
            $cookie = Colby::decrypt($cookieCipherData);

            if (empty($cookie)) {
                ColbyUser::removeUserCookie();
                return;
            }

            if (time() > $cookie->expirationTimestamp) {
                ColbyUser::removeUserCookie();
                return;
            }

            /* Success, the user is now logged in. */
            ColbyUser::$currentUserHash = $cookie->userHash;
            ColbyUser::$currentUserId = $cookie->userId;
        } catch (Throwable $exception) {
            CBErrorHandler::report($exception);
            ColbyUser::removeUserCookie();
        }
    }


    /**
     * @deprecated 2018_04_17
     *
     *      Use ColbyUser::currentUserIsLoggedIn())
     *
     * @return bool
     */
    public function isLoggedIn() {
        return !!$this->id;
    }

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
     * @deprecated use ColbyUser::isMemberOfGroup()
     *
     * @return bool
     */
    function isOneOfThe($group) {
        if ($group === 'Public') {
            return true;
        }

        if (!preg_match('/^[a-zA-Z0-9]+$/', $group)) {
            throw new InvalidArgumentException('group');
        }

        if (!$this->id) {
            return false;
        }

        if (isset($this->groups[$group])) {
            return $this->groups[$group];
        }

        $sql = <<<EOT

            SELECT COUNT(*) AS `isOneOfTheGroup`
            FROM `ColbyUsersWhoAre{$group}`
            WHERE `userId` = '{$this->id}'

EOT;

        $result = Colby::mysqli()->query($sql);

        /**
         * An error will generally mean that the table doesn't exist in which
         * case the user is not considered to belong to the group.
         *
         * Errors produced for other reasons will be very rare and if they
         * represent a bad database state they will be caught by other queries.
         */

        if (Colby::mysqli()->error) {
            $isOneOfTheGroup = false;
        } else {
            $isOneOfTheGroup = $result->fetch_object()->isOneOfTheGroup;

            $result->free();
        }

        $this->groups[$group] = !!$isOneOfTheGroup;

        return $this->groups[$group];
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
     * @return null
     */
    static function loginCurrentUser(
        $facebookAccessToken,
        $facebookAccessExpirationTime,
        $facebookProperties
    ) {
        $mysqli = Colby::mysqli();
        $facebookUserID = intval($facebookProperties->id);

        if ($facebookUserID <= 0) {
            throw new RuntimeException('The Facebook user ID is invalid.');
        }

        $userIdentity = ColbyUser::facebookUserIDtoUserIdentity($facebookUserID);

        $sqlFacebookAccessToken = $mysqli->escape_string($facebookAccessToken);
        $sqlFacebookAccessToken = "'{$sqlFacebookAccessToken}'";

        $sqlFacebookAccessExpirationTime = "'{$facebookAccessExpirationTime}'";

        $sqlFacebookName = ColbyConvert::textToHTML($facebookProperties->name);
        $sqlFacebookName = $mysqli->escape_string($sqlFacebookName);
        $sqlFacebookName = "'{$sqlFacebookName}'";

        /**
         * First name, last name, and time zone are deprecated.
         * TODO: Remove them from the table, they are not used anyway. This
         * table needs to updated to store JSON or use a model.
         */

        Colby::query('START TRANSACTION');

        if ($userIdentity) {
            $sql = <<<EOT

                UPDATE
                    `ColbyUsers`
                SET
                    `facebookAccessToken` = {$sqlFacebookAccessToken},
                    `facebookAccessExpirationTime` = {$sqlFacebookAccessExpirationTime},
                    `facebookName` = {$sqlFacebookName},
                    `facebookFirstName` = '',
                    `facebookLastName` = '',
                    `facebookTimeZone` = 0
                WHERE
                    `id` = {$userIdentity->ID}

EOT;

            Colby::query($sql);
        } else {
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

            $count = CBDB::SQLToValue('SELECT COUNT(*) FROM `ColbyUsers`');

            if ($count === '1') {
                Colby::query(
                    "INSERT INTO `ColbyUsersWhoAreAdministrators` VALUES ({$userIdentity->ID}, NOW())"
                );

                Colby::query(
                    "INSERT INTO `ColbyUsersWhoAreDevelopers` VALUES ({$userIdentity->ID}, NOW())"
                );
            }
        }

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
            'userHash' => $userIdentity->hash,
            'userId' => $userIdentity->ID,
            'expirationTimestamp' => time() + (60 * 60 * 24), /* 24 hours from now */
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

    /**
     * This function must be called before any output is generated because it
     * sets a cookie.
     */
    static function logoutCurrentUser() {
        self::removeUserCookie();
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
     * @param int $userID
     * @param string $group
     * @param bool $isMember
     *
     * @return null
     */
    static function updateGroupMembership($userID, $groupName, $isMember) {
        $tableName = ColbyUser::groupNameToTableName($groupName);
        $userIDAsSQL = intval($userID);

        if ($isMember) {
            $SQL = <<<EOT

                INSERT IGNORE INTO `{$tableName}`
                VALUES ({$userIDAsSQL}, NOW())

EOT;
        } else {
            $SQL = <<<EOT

                DELETE FROM `{$tableName}`
                WHERE `userId` = {$userIDAsSQL}

EOT;
        }

        Colby::query($SQL);
    }

    /**
     * @deprecated use ColbyUser::fetchUserDataByHash()
     *
     * @param int $userId
     *
     *  If $userId is null the method returns the user row for the currently
     *  logged in user or null of nobody is logged in.
     *
     * @return stdClass | null
     *
     *  Returns the user row for a given user id. If the userId doesn't exist
     *  null is returned.
     */
    static function userRow($userId = null) {
        if (null === $userId) {
            if (null === self::$currentUserId) {
                return null;
            }

            $userId = self::$currentUserId;
        } else {
            // intval confirmed 64-bit capable (signed though)
            $userId = intval($userId);
        }

        if ($userId == self::$currentUserId && self::$currentUserRow) {
            return self::$currentUserRow;
        }

        $sqlUserId = "'{$userId}'";

        $sql = <<<EOT

            SELECT *
            FROM `ColbyUsers`
            WHERE `id` = {$sqlUserId}

EOT;

        $result = Colby::query($sql);

        if (1 === $result->num_rows) {
            $userRow = $result->fetch_object();
        } else {
            $userRow = null;
        }

        $result->free();

        if ($userId == self::$currentUserId) {
            // cache current user row
            // user data shouldn't change significantly during a request
            // if it does, that will be the main task of the request
            // so the request will be aware of the changes

            self::$currentUserRow = $userRow;
        }

        return $userRow;
    }
}

ColbyUser::initialize();
