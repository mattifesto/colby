<?php

define('COLBY_USER_COOKIE', 'colby-user');

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
        if (isset($_COOKIE[COLBY_USER_COOKIE]))
        {
            $cookie = $_COOKIE[COLBY_USER_COOKIE];

            // get '-' delimited data out of cookie

            $cookieData = explode('-', $cookie);

            $cookieUserId = $cookieData[0];
            $cookieHash = $cookieData[1];

            // get the user row for the user indicated by the cookie

            // NOTE: 2012.09.28
            // Sometimes when the configuration file has been modified
            // the user can be logged in and later the database is unavailable.
            // This is a rare occurrence but we handle it
            // with a check to make sure COLBY_MYSQL_HOST is set
            // before attempting to get the user row for the user.

            if (COLBY_MYSQL_HOST)
            {
                $userRow = self::userRow($cookieUserId);
            }
            else
            {
                $userRow = null;
            }

            if (null === $userRow)
            {
                // if no user row was found for the user in the cookie
                // the cookie is not authentic, remove it

                self::logoutCurrentUser();

                return;
            }

            // regenerate the hash
            // compare it against the hash in the cookie
            // if they match the cookie is authentic
            // if they don't the cookie is not authentic (or it's stale)

            $hashedValue = $userRow->id .
                $userRow->facebookAccessToken .
                $userRow->facebookAccessExpirationTime;

            $generatedHash = hash('sha512', $hashedValue);

            if ($cookieHash == $generatedHash)
            {
                // NOTE: This is the only place the current user id is set. This is because when the user logs in that request sets the cookie and that's all it does. So the only way a user can be logged in is if the cookie is already set. The user is never logged in any other way or in the middle of a request.

                self::$currentUserId = $cookieUserId;
            }
            else
            {
                // if the hashed don't match
                // user is effectively logged out
                // we have to assume for security purposes the cookie was faked
                // even if really the user just has an old cookie

                self::logoutCurrentUser();
            }
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

        $result = Colby::query($sql);

        $isOneOfTheGroup = $result->fetch_object()->isOneOfTheGroup;

        $result->free();

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
SELECT
    `id`
FROM
    `ColbyUsers`
WHERE
    `facebookId` = {$sqlFacebookId}
EOT;

        $result = Colby::query($sql);

        if ($row = $result->fetch_object())
        {
            $id = $row->id;
            $sqlId = "'{$id}'";
        }
        else
        {
            $id = null;
            $sqlId = null;
        }

        $result->free();

        $sqlFacebookAccessToken = $mysqli->escape_string($facebookAccessToken);
        $sqlFacebookAccessToken = "'{$sqlFacebookAccessToken}'";

        $sqlFacebookAccessExpirationTime = "'{$facebookAccessExpirationTime}'";

        $sqlFacebookName = ColbyConvert::textToHTML($facebookProperties->name);
        $sqlFacebookName = $mysqli->escape_string($sqlFacebookName);
        $sqlFacebookName = "'{$sqlFacebookName}'";

        $sqlFacebookFirstName = ColbyConvert::textToHTML($facebookProperties->first_name);
        $sqlFacebookFirstName = $mysqli->escape_string($sqlFacebookFirstName);
        $sqlFacebookFirstName = "'{$sqlFacebookFirstName}'";

        $sqlFacebookLastName = ColbyConvert::textToHTML($facebookProperties->last_name);
        $sqlFacebookLastName = $mysqli->escape_string($sqlFacebookLastName);
        $sqlFacebookLastName = "'{$sqlFacebookLastName}'";

        $sqlFacebookTimeZone = "'{$facebookProperties->timezone}'";

        if ($sqlId)
        {
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

            $id = $mysqli->insert_id;
        }

        $hashedValue = $id . $facebookAccessToken . $facebookAccessExpirationTime;

        $hash = hash('sha512', $hashedValue);

        $cookieValue = $id . '-' . $hash;

        setcookie(COLBY_USER_COOKIE, $cookieValue, 0, '/');
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

    ///
    /// this function must be called before any html output to be effective
    /// it sets a cookie
    ///
    public static function logoutCurrentUser()
    {
        // time = now - 1 day
        // sure to be in the past in all time zones

        $time = time() - (60 * 60 * 24);

        setcookie(COLBY_USER_COOKIE, '', $time, '/');
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
