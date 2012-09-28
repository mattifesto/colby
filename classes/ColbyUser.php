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
                // that's very odd
                // nevertheless, cookie is not authentic, remove it

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

            if ($cookieHash === $generatedHash)
            {
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

        $sql = 'SELECT LoginFacebookUser(' .
            "'{$facebookProperties->id}'," .
            "'{$accessToken}'," .
            "'{$facebookAccessExpirationTime}'," .
            "'{$name}'," .
            "'{$firstName}'," .
            "'{$lastName}'," .
            "'{$facebookProperties->timezone}'" .
            ') AS `id`';

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

            return "<a href=\"{$url}\">log out</a>";
        }
        else
        {
            $url = ColbyUser::loginURL($redirectURL);

            return "<a href=\"{$url}\">log in</a>";
        }
    }

    ///
    ///
    ///
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
            '&redirect_uri=' .
                urlencode(COLBY_SITE_URL
                    . '/colby/facebook-oauth-handler/') .
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

    ///
    ///
    ///
    public static function logoutURL($redirectURL = null)
    {
        if (!$redirectURL)
        {
            $redirectURL = $_SERVER['REQUEST_URI'];
        }

        $state = new stdClass();
        $state->colby_redirect_uri = $redirectURL;

        $url = COLBY_SITE_URL . '/colby/logout/' .
            '?state=' . urlencode(json_encode($state));

        return $url;
    }

    ///
    /// returns the ColbyUser table row for a given user id
    /// if userId is null
    ///     returns the row for the current logged in user
    ///     or null if nobody's logged in
    /// if userId doesn't exist
    ///     returns null
    ///
    /// userId
    ///     type: unsigned 64-bit integer
    ///
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

        if ($userId === self::$currentUserId && self::$currentUserRow)
        {
            return self::$currentUserRow;
        }

        $userRow = include(COLBY_SITE_DIRECTORY .
            '/colby/snippets/query-user-row-for-user-id.php');

        if ($userId === self::$currentUserId)
        {
            self::$currentUserRow = $userRow;
        }

        return $userRow;
    }
}

ColbyUser::initialize();
