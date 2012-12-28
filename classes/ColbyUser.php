<?php

define('COLBY_USER_COOKIE', 'colby-user');

class ColbyUser
{
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

    ///
    /// this function must be called before any html output to be effective
    /// it sets a cookie
    ///
    /// facebookAccessExpirationTime
    ///     this is an absolute unix time representing a point in the future
    ///     basically: current unix time + facebook relative expiration time
    ///
    public static function loginCurrentUser(
        $facebookAccessToken,
        $facebookAccessExpirationTime,
        $facebookProperties)
    {
        $mysqli = Colby::mysqli();

        $accessToken = $mysqli->escape_string($facebookAccessToken);

        $name = ColbyConvert::textToHTML($facebookProperties->name);
        $name = $mysqli->escape_string($name);

        $firstName = ColbyConvert::textToHTML($facebookProperties->first_name);
        $firstName = $mysqli->escape_string($firstName);

        $lastName = ColbyConvert::textToHTML($facebookProperties->last_name);
        $lastName = $mysqli->escape_string($lastName);

        $sql = <<<EOT
SELECT ColbyLoginFacebookUser(
    '{$facebookProperties->id}',
    '{$accessToken}',
    '{$facebookAccessExpirationTime}',
    '{$name}',
    '{$firstName}',
    '{$lastName}',
    '{$facebookProperties->timezone}')
AS `id`
EOT;

        $result = $mysqli->query($sql);

        if ($mysqli->error)
        {
            throw new RuntimeException($mysqli->error);
        }

        $userId = $result->fetch_object()->id;

        $result->free();

        $hashedValue = $userId .
            $facebookAccessToken .
            $facebookAccessExpirationTime;

        $hash = hash('sha512', $hashedValue);

        $cookieValue = $userId . '-' . $hash;

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

            return "<a href=\"{$url}\">log out</a>";
        }
        else
        {
            $url = ColbyUser::loginURL($redirectURL);
            $url = ColbyConvert::textToHTML($url);

            return "<a href=\"{$url}\">log in</a>";
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
