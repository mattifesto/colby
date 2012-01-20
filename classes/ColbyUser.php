<?php

class ColbyUser
{
    /// <summary>
    ///
    /// </summary>
    public static function id()
    {
        if (self::isLoggedIn())
        {
            return $_COOKIE['user_id'];
        }

        return null;
    }

    /// <summary>
    ///
    /// </summary>
    public static function isLoggedIn()
    {
        return (isset($_COOKIE['user_id']));
    }

    /// <summary>
    ///
    /// </summary>
    public static function loginURL($redirectURL = '/')
    {
        $state = new stdClass();
        $state->colby_redirect_uri = $redirectURL;

        $url = 'https://www.facebook.com/dialog/oauth' .
            '?client_id=' . COLBY_FACEBOOK_APP_ID .
            '&redirect_uri=' .
                urlencode(COLBY_SITE_URL
                    . 'facebook-oauth-handler/') .
            '&state=' . urlencode(json_encode($state));

        return $url;
    }

    /// <summary>
    ///
    /// </summary>
    public static function logoutURL($redirectURL = '/')
    {
        $state = new stdClass();
        $state->colby_redirect_uri = $redirectURL;

        $url = COLBY_SITE_URL . 'logout/' .
            '?state=' . urlencode(json_encode($state));

        return $url;
    }

    /// <summary>
    ///
    /// </summary>
    public static function name()
    {
        if (self::isLoggedIn())
        {
            return $_COOKIE['user_name'];
        }

        return null;
    }
}
