<?php

/**
 * All interactions with the Facebook API should happen in this class. The API
 * has relatively frequent version upgrades and if the Facebook interaction is
 * spread around, updates are more difficult.
 */
final class
CBFacebook
{
    /**
     * https://bit.ly/32zPqWq
     *
     * @param string $code
     * @param string $destinationURL
     *
     * @return object
     */
    static function
    fetchAccessTokenObject(
        string $code,
        string $destinationURL
    ): stdClass
    {
        /**
         * @NOTE: 2017_03_28
         *
         *      The redirect_uri in the request below is not used to redirect
         *      anything. However, it is tested to confirm that it matches the
         *      redirect_uri value that was present in the previous request that
         *      provided the $code value.
         */

        $redirectURI =
        CBFacebook::redirectURI(
            $destinationURL
        );

        $URL =
        'https://graph.facebook.com/v13.0/oauth/access_token' .
        '?client_id=' . CBFacebookPreferences::getAppID() .
        '&redirect_uri=' . urlencode($redirectURI) .
        '&client_secret=' . CBFacebookPreferences::getAppSecret() .
        '&code=' . $code;

        return
        CBFacebook::fetchGraphAPIResponse(
            $URL
        );
    }
    /* fetchAccessTokenObject() */



    /**
     * @param string $URL
     *
     * @return object
     */
    static function
    fetchGraphAPIResponse(
        string $URL
    ): stdClass
    {
        $curlHandle =
        curl_init();

        curl_setopt(
            $curlHandle,
            CURLOPT_URL,
            $URL
        );

        curl_setopt(
            $curlHandle,
            CURLOPT_HEADER,
            0
        );

        curl_setopt(
            $curlHandle,
            CURLOPT_RETURNTRANSFER,
            1
        );

        /**
         * @NOTE 2014_08_03
         *
         *      Sometimes Facebook responds very slowly to these requests. It's
         *      not the web, direct requests from the server are also slow.
         *      There's a theory that it has something to do with "round robin
         *      DNS", but I never figured that out. However, setting this time
         *      out for some reason makes it respond faster. There should be a
         *      time out regardless, but I just wanted to document the
         *      importance of this.
         */

        curl_setopt(
            $curlHandle,
            CURLOPT_CONNECTTIMEOUT,
            5
        );

        $response =
        curl_exec(
            $curlHandle
        );

        if (
            $response === false
        ) {
            $error =
            curl_errno(
                $curlHandle
            );

            curl_close(
                $curlHandle
            );

            throw new RuntimeException(
                "Facebook OAuth: The request to exchange a code for an " .
                "access token failed with the error: {$error}"
            );
        }

        $httpCode =
        curl_getinfo(
            $curlHandle,
            CURLINFO_HTTP_CODE
        );

        if (
            $httpCode === 400
        ) {
            $object =
            json_decode(
                $response
            );

            throw new RuntimeException(
                'Error retrieving Facebook Graph API response: ' .
                $object->error->type .
                ', ' .
                $object->error->message
            );
        }

        curl_close(
            $curlHandle
        );

        return
        json_decode(
            $response
        );
    }
    /* fetchGraphAPIResponse() */



    /**
     * https://developers.facebook.com/docs/graph-api/reference/user
     *
     * @param string $accessToken
     *
     * @return object
     */
    static function
    fetchUserProperties(
        string $accessToken
    ): stdClass
    {
        /**
         * Here "me" is translated by Facebook into the user ID of the user
         * associated with the access token.
         */

        $URL =
        'https://graph.facebook.com/v13.0/me' .
        "?access_token={$accessToken}" .
        "&fields=id,name";

        return
        CBFacebook::fetchGraphAPIResponse(
            $URL
        );
    }
    /* fetchUserProperties() */



    /**
     * https://bit.ly/2wh9Gjl
     *
     * @NOTE 2017_03_28
     *
     *      The Facebook URL below uses www.facebook.com instead of
     *      graph.facebook.com. This is on purpose, documented as such, and is
     *      required for this URL to work properly. The documentation doesn't
     *      say why.
     *
     * @param string $destinationURL
     *
     * @return string
     */
    static function
    oauthURLAtFacebookWebsite(
        string $destinationURL = '/'
    ): string
    {
        $redirectURI =
        CBFacebook::redirectURI(
            $destinationURL
        );

        $oauthURLAtFacebookWebsite =
        'https://www.facebook.com/v13.0/dialog/oauth' .
        '?client_id=' .
        urlencode(
            CBFacebookPreferences::getAppID()
        ) .
        '&redirect_uri=' .
        urlencode($redirectURI);

        return
        $oauthURLAtFacebookWebsite;
    }
    /* oauthURLAtFacebookWebsite() */



    /**
     * This function returns the redirect URI that Facebook oauth will redirect
     * to once a login attempt is completed, pass or fail.
     *
     * @param string $destinationURL
     *
     * @return string
     */
    static function
    redirectURI(
        string $destinationURL = '/'
    ): string
    {
        $state =
        (object)[
            'destinationURL' =>
            $destinationURL,
        ];

        $stateAsJSON =
        json_encode(
            $state
        );

        /**
         * The $redirectURI holds the URL that Facebook will redirect to after
         * a Facebook login is attempted.
         */

        $redirectURI =
        cbsiteurl() .
        '/colby/facebook-oauth-handler/?state=' .
        urlencode(
            $stateAsJSON
        );

        return
        $redirectURI;
    }
    /* redirectURI() */



    /**
     * https://developers.facebook.com/docs/graph-api/reference/user/picture/
     *
     * @param string $facebookID
     *
     * @return string
     */
    static function
    userImageURL(
        string $facebookID
    ): string
    {
        $userImageURL =
        "https://graph.facebook.com/v13.0/{$facebookID}/picture?type=large";

        return
        $userImageURL;
    }
    /* userImageURL() */

}
/* CBFacebook */
