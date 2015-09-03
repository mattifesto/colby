<?php

define('CBUserCookieName', 'colby-user-encrypted-data');

class ColbyUser
{
    private $id = null;
    private $groups = array();
    private $row = null;

    private static $currentUser = null;

    // currentUserId
    // if we can authenticate the current logged in user
    // we just store their id, not the table row or anything else
    // table row may be changed by the site
    // caching it will only lead to possible stale data bugs

    private static $currentUserId = null;

    // currentUserRow
    // this is cached, see the following document for discussion
    // "Colby User Data and Permissions Caching"
    // this information will not change during a request
    // even if the database row is altered

    private static $currentUserRow = null;

    /**
     * @return ColbyUser
     */
    private function __construct()
    {
    }

    /**
     * @return ColbyUser
     */
    public static function current()
    {
        if (!self::$currentUser)
        {
            self::$currentUser = new ColbyUser();
            self::$currentUser->id = self::$currentUserId;
        }

        return self::$currentUser;
    }

    ///
    /// will return the user id or null if a user is not logged in
    ///
    public static function currentUserId()
    {
        return self::$currentUserId;
    }

    ///
    /// this function should be run only once
    /// it is run automatically when ColbyUser is first included
    ///
    public static function initialize()
    {
        self::initializeCurrentUser();
    }

    /**
     * @return void
     */
    private static function initializeCurrentUser()
    {
        if (!isset($_COOKIE[CBUserCookieName]))
        {
            return;
        }

        $cookieCipherData = $_COOKIE[CBUserCookieName];

        try
        {
            $cookie = Colby::decrypt($cookieCipherData);

            if (time() > $cookie->expirationTimestamp)
            {
                self::removeUserCookie();

                return;
            }

            /**
             * Success, the user is now logged in.
             */

            self::$currentUserId = $cookie->userId;
        }
        catch (Exception $exception)
        {
            Colby::reportException($exception);

            self::removeUserCookie();

            return;
        }
    }

    /**
     * @return bool
     */
    public function isLoggedIn()
    {
        return !!$this->id;
    }

    /**
     * @return bool
     */
    public function isOneOfThe($group)
    {
        if (!preg_match('/^[a-zA-Z0-9]+$/', $group))
        {
            throw new InvalidArgumentException('group');
        }

        if (!$this->id)
        {
            return false;
        }

        /**
         * TODO: COLBY_FACEBOOK_FIRST_VERIFIED_USER_ID should be renamed to COLBY_FACEBOOK_SUPERUSER_USERNAME
         */

        if (COLBY_FACEBOOK_FIRST_VERIFIED_USER_ID == $this->row()->facebookId)
        {
            return true;
        }

        if (isset($this->groups[$group]))
        {
            return $this->groups[$group];
        }

        $sql = <<<EOT
SELECT
    COUNT(*) AS `isOneOfTheGroup`
FROM
    `ColbyUsersWhoAre{$group}`
WHERE
    `userId` = '{$this->id}'
EOT;

        $result = Colby::mysqli()->query($sql);

        /**
         * An error will generally mean that the table doesn't exist in which
         * case the user is not considered to belong to the group.
         *
         * Errors produced for other reasons will be very rare and if they
         * represent a bad database state they will be caught by other queries.
         */

        if (Colby::mysqli()->error)
        {
            $isOneOfTheGroup = false;
        }
        else
        {
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
     * Note:
     *
     *  This function uses MySQL 'INSERT ... ON DUPLICATE KEY UPDATE ...'
     *  which will increment the AUTO_INCREMENT id every time a user logs in,
     *  not every time a new user is added. This is due to some optimizations
     *  in the way InnoDB deals with AUTO_INCREMENT. It's a good thing.
     *
     *  This means that big gaps between user id's should be expected. The
     *  idea that the max AUTO_INCREMENT id will be reached is not a concern.
     *  If a trillion users logged in every day it would take over 50,000 years
     *  for the maximum AUTO_INCREMENT id to be reached. So it's not a problem.
     *
     * @return void
     */
    public static function loginCurrentUser(
        $facebookAccessToken,
        $facebookAccessExpirationTime,
        $facebookProperties)
    {
        $mysqli = Colby::mysqli();

        $sqlFacebookId = "'{$facebookProperties->id}'";

        $sql = <<<EOT

            SELECT  `id`
            FROM    `ColbyUsers`
            WHERE   `facebookId` = {$sqlFacebookId}

EOT;

        $id = CBDB::SQLToValue($sql);

        if ($id !== false) {
            $sqlId = "'{$id}'";
        } else {
            $sqlId = null;
        }


        $sqlFacebookAccessToken = $mysqli->escape_string($facebookAccessToken);
        $sqlFacebookAccessToken = "'{$sqlFacebookAccessToken}'";

        $sqlFacebookAccessExpirationTime = "'{$facebookAccessExpirationTime}'";

        $sqlFacebookName = ColbyConvert::textToHTML($facebookProperties->name);
        $sqlFacebookName = $mysqli->escape_string($sqlFacebookName);
        $sqlFacebookName = "'{$sqlFacebookName}'";

        if (isset($facebookProperties->first_name)) {
            $sqlFacebookFirstName = ColbyConvert::textToHTML($facebookProperties->first_name);
            $sqlFacebookFirstName = $mysqli->escape_string($sqlFacebookFirstName);
            $sqlFacebookFirstName = "'{$sqlFacebookFirstName}'";

            $sqlFacebookLastName = ColbyConvert::textToHTML($facebookProperties->last_name);
            $sqlFacebookLastName = $mysqli->escape_string($sqlFacebookLastName);
            $sqlFacebookLastName = "'{$sqlFacebookLastName}'";

            $sqlFacebookTimeZone = "'{$facebookProperties->timezone}'";
        } else {
            /**
             * 2015.09.03 Facebook did not return an of these properies for a
             * new app so they may be deprecated.
             * TODO: Remove them, they are not used anyway.
             */
            $sqlFacebookFirstName = "''";
            $sqlFacebookLastName = "''";
            $sqlFacebookTimeZone = '0';
        }

        if ($sqlId) {
            $sql = <<<EOT

                UPDATE
                    `ColbyUsers`
                SET
                    `facebookAccessToken` = {$sqlFacebookAccessToken},
                    `facebookAccessExpirationTime` = {$sqlFacebookAccessExpirationTime},
                    `facebookName` = {$sqlFacebookName},
                    `facebookFirstName` = {$sqlFacebookFirstName},
                    `facebookLastName` = {$sqlFacebookLastName},
                    `facebookTimeZone` = {$sqlFacebookTimeZone}
                WHERE
                    `id` = {$sqlId}

EOT;

            Colby::query($sql);
        }
        else
        {
            $sqlHash = sha1(microtime() . rand());
            $sqlHash = "'{$sqlHash}'";

        $sql = <<<EOT

            INSERT INTO
                `ColbyUsers`
            (
                `hash`,
                `facebookId`,
                `facebookAccessToken`,
                `facebookAccessExpirationTime`,
                `facebookName`,
                `facebookFirstName`,
                `facebookLastName`,
                `facebookTimeZone`
            )
            VALUES
            (
                UNHEX({$sqlHash}),
                {$sqlFacebookId},
                {$sqlFacebookAccessToken},
                {$sqlFacebookAccessExpirationTime},
                {$sqlFacebookName},
                {$sqlFacebookFirstName},
                {$sqlFacebookLastName},
                {$sqlFacebookTimeZone}
            )

EOT;

            Colby::query($sql);

            $id = (int)$mysqli->insert_id;

            /* Detect first user */

            $count = CBDB::SQLToValue('SELECT COUNT(*) FROM `ColbyUsers`');

            if ($count === '1') {
                Colby::query("INSERT INTO `ColbyUsersWhoAreAdministrators` VALUES ({$id}, NOW())");
                Colby::query("INSERT INTO `ColbyUsersWhoAreDevelopers` VALUES ({$id}, NOW())");
            }
        }

        /**
         * Set the Colby user cookie data.
         */

        $cookie = new stdClass();

        /**
         * The only realistic way to best prevent cookie hijacking is to use
         * HTTPS. As soon as a site becomes relatively popular or makes
         * enough money to cover the cost, switch. This is what Facebook and
         * Twitter did. It doesn't prevent physical access attacks, but that's
         * pretty tough to do.
         */

        $cookie->userId = $id;
        $cookie->expirationTimestamp = time() + (60 * 60 * 4); /* 4 hours from now */

        $encryptedCookie = Colby::encrypt($cookie);

        /**
         * TODO: If site uses HTTPS set parameter that only allows cookies to
         * be transmitted over secure connections.
         */

        setcookie(CBUserCookieName, $encryptedCookie, time() + (60 * 60 * 24 * 30), '/');
    }

    /**
     * Creates a hyperlink for either logging in or out depending on whether
     * the user is currently logged in or out.
     *
     * @param string $redirectURL
     *
     * @return string
     *  a string containg an HTML anchor element
     *  <a href="...">log in</a>
     */
    public static function loginHyperlink($redirectURL = null)
    {
        if (!COLBY_FACEBOOK_APP_ID)
        {
            return '<span style="color: red;">this site is not configured to support users</span>';
        }
        else if (ColbyUser::currentUserId())
        {
            $url = ColbyUser::logoutURL($redirectURL);
            $url = ColbyConvert::textToHTML($url);

            return "<a href=\"{$url}\">Log Out</a>";
        }
        else
        {
            $url = ColbyUser::loginURL($redirectURL);
            $url = ColbyConvert::textToHTML($url);

            return "<a href=\"{$url}\">Log In</a>";
        }
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
    public static function loginURL($redirectURL = null)
    {
        if (!$redirectURL)
        {
            $redirectURL = $_SERVER['REQUEST_URI'];
        }

        $state = new stdClass();
        $state->colby_redirect_uri = $redirectURL;

        $url = 'https://www.facebook.com/dialog/oauth' .
            '?client_id=' . COLBY_FACEBOOK_APP_ID .
            '&redirect_uri=' . urlencode(COLBY_SITE_URL . '/colby/facebook-oauth-handler/') .
            '&state=' . urlencode(json_encode($state));

        return $url;
    }

    /**
     * This function must be called before any output is generated because it
     * sets a cookie.
     */
    public static function logoutCurrentUser()
    {
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
    public static function logoutURL($redirectURL = null)
    {
        if (!$redirectURL)
        {
            $redirectURL = $_SERVER['REQUEST_URI'];
        }

        $state = new stdClass();
        $state->colby_redirect_uri = $redirectURL;

        $url = COLBY_SITE_URL . '/colby/logout/?state=' . urlencode(json_encode($state));

        return $url;
    }

    /**
     * This function must be called before any output is generated because it
     * sets a cookie.
     */
    private static function removeUserCookie()
    {
        // time = now - 1 day
        // sure to be in the past in all time zones

        $time = time() - (60 * 60 * 24);

        setcookie(CBUserCookieName, '', $time, '/');
    }

    /**
     * @return stdClass | null
     */
    public function row()
    {
        if (!$this->id)
        {
            return null;
        }

        if ($this->row)
        {
            return $this->row;
        }

        $sql = <<<EOT
SELECT
    *
FROM
    `ColbyUsers`
WHERE
    `id` = '{$this->id}'
EOT;

        $result = Colby::query($sql);

        $this->row = $result->fetch_object();

        $result->free();

        return $this->row;
    }

    /**
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
    public static function userRow($userId = null)
    {
        if (null === $userId)
        {
            if (null === self::$currentUserId)
            {
                return null;
            }

            $userId = self::$currentUserId;
        }
        else
        {
            $userId = intval($userId); // intval confirmed 64-bit capable (signed though)
        }

        if (   $userId == self::$currentUserId
            && self::$currentUserRow)
        {
            return self::$currentUserRow;
        }

        $sqlUserId = "'{$userId}'";

        $sql = <<<EOT
SELECT
    *
FROM
    `ColbyUsers`
WHERE
    `id` = {$sqlUserId}
EOT;

        $result = Colby::query($sql);

        if (1 === $result->num_rows)
        {
            $userRow = $result->fetch_object();
        }
        else
        {
            $userRow = null;
        }

        $result->free();

        if ($userId == self::$currentUserId)
        {
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
