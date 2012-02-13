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
    ///
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

            $userRow = self::userRow($cookieUserId);

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

            $hashedValue = $userRow->facebookId .
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

        $name = Colby::textToHTML($facebookProperties->name);
        $name = $mysqli->escape_string($name);

        $firstName = Colby::textToHTML($facebookProperties->first_name);
        $firstName = $mysqli->escape_string($firstName);

        $lastName = Colby::textToHTML($facebookProperties->last_name);
        $lastName = $mysqli->escape_string($lastName);

        $sql = <<< END
INSERT INTO `ColbyUsers`
(
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
    '$facebookProperties->id',
    '$accessToken',
    '$facebookAccessExpirationTime',
    '$name',
    '$firstName',
    '$lastName',
    '$facebookProperties->timezone'
)
ON DUPLICATE KEY UPDATE
    `facebookAccessToken` = VALUES(`facebookAccessToken`),
    `facebookAccessExpirationTime` = VALUES(`facebookAccessExpirationTime`),
    `facebookName` = VALUES(`facebookName`),
    `facebookFirstName` = VALUES(`facebookFirstName`),
    `facebookLastName` = VALUES(`facebookLastName`),
    `facebookTimeZone` = VALUES(`facebookTimeZone`)
END;

        $mysqli->query($sql);

        if ($mysqli->error)
        {
            throw new RuntimeException($mysqli->error);
        }

        $hashedValue = $facebookProperties->id .
            $facebookAccessToken .
            $facebookAccessExpirationTime;

        $hash = hash('sha512', $hashedValue);

        $cookieValue = $facebookProperties->id . '-' . $hash;

        setcookie(COLBY_USER_COOKIE, $cookieValue, 0, '/');
    }

    ///
    ///
    ///
    public static function loginURL($redirectURL = '/')
    {
        $state = new stdClass();
        $state->colby_redirect_uri = $redirectURL;

        $url = 'https://www.facebook.com/dialog/oauth' .
            '?client_id=' . COLBY_FACEBOOK_APP_ID .
            '&redirect_uri=' .
                urlencode(COLBY_SITE_URL
                    . '/facebook-oauth-handler/') .
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
    public static function logoutURL($redirectURL = '/')
    {
        $state = new stdClass();
        $state->colby_redirect_uri = $redirectURL;

        $url = COLBY_SITE_URL . '/logout/' .
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
        $mysqli = Colby::mysqli();

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

        $sql = 'SELECT * FROM `ColbyUsers` WHERE `facebookId` = ' .
            $userId;

        $result = $mysqli->query($sql);

        if ($mysqli->error)
        {
            throw new RuntimeException($mysqli->error);
        }

        if (0 === $result->num_rows)
        {
            $userRow = null;
        }
        else
        {
            $userRow = $result->fetch_object();
        }

        $result->free();

        if ($userId === self::$currentUserId)
        {
            self::$currentUserRow = $userRow;
        }

        return $userRow;
    }
}

ColbyUser::initialize();
