<?php

/**
 * All interactions with the Facebook API should happen in this class. The API
 * has relatively frequent version upgrades and if the Facebook interaction is
 * spread around, updates are more difficult.
 */
final class CBFacebook {

    const loginStateCookieName = "facebook-login-state";



    /**
     * https://developers.facebook.com/docs/facebook-login/
     * manually-build-a-login-flow/#confirm
     *
     * @param string $code
     *
     * @return object
     */
    static function fetchAccessTokenObject(string $code): stdClass {

        /**
         * @NOTE: 2017_03_28
         *
         *      The redirect_uri in the request below is not used to redirect
         *      anything. However, it is tested to confirm that it matches the
         *      redirect_uri value that was present in the previous request that
         *      provided the $code value.
         */

        $redirectURI = (
            cbsiteurl() .
            '/colby/facebook-oauth-handler/'
        );

        $URL = (
            'https://graph.facebook.com/v3.3/oauth/access_token' .
            '?client_id=' . CBFacebookPreferences::getAppID() .
            '&redirect_uri=' . urlencode($redirectURI) .
            '&client_secret=' . CBFacebookPreferences::getAppSecret() .
            '&code=' . $code
        );

        return CBFacebook::fetchGraphAPIResponse($URL);
    }
    /* fetchAccessTokenObject() */



    /**
     * @param string $URL
     *
     * @return object
     */
    static function fetchGraphAPIResponse(
        string $URL
    ): stdClass {
        $curlHandle = curl_init();

        curl_setopt($curlHandle, CURLOPT_URL, $URL);
        curl_setopt($curlHandle, CURLOPT_HEADER, 0);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);

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

        curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT, 5);

        $response = curl_exec($curlHandle);

        if ($response === false) {
            $error = curl_errno($curlHandle);
            curl_close($curlHandle);
            throw new RuntimeException(
                "Facebook OAuth: The request to exchange a code for an " .
                "access token failed with the error: {$error}"
            );
        }

        $httpCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);

        if ($httpCode === 400) {
            $object = json_decode($response);

            throw new RuntimeException(
                'Error retrieving Facebook Graph API response: ' .
                $object->error->type .
                ', ' .
                $object->error->message
            );
        }

        curl_close($curlHandle);

        return json_decode($response);
    }
    /* fetchGraphAPIResponse() */



    /**
     * https://developers.facebook.com/docs/graph-api/reference/user
     *
     * @param string $accessToken
     *
     * @return object
     */
    static function fetchUserProperties(string $accessToken): stdClass {
        $fields = 'fields=name,metadata{type}';

        /**
         * Here "me" is translated by Facebook into the user ID of the user
         * associated with the access token.
         */

        $URL =
        "https://graph.facebook.com/v3.3/me?access_token=" .
        "{$accessToken}&metadata=1&{$fields}";

        return CBFacebook::fetchGraphAPIResponse($URL);
    }
    /* fetchUserProperties() */



    /**
     * @param string|null $redirectURL
     *
     *      Sending the user to this URL, usually by presenting a link that
     *      appears to look like a login button, will request that Facebook
     *      authenticate the user. The authentication may or may not need to
     *      present UI to the user depending on the user's current state with
     *      Facebook. After the user is authenticated, Facebook will redirect
     *      the browser to the Colby Facebook oauth handler. Once the Colby
     *      Facebook oauth handler has processed Facebook's response and
     *      verified a successful login, the browser will be redirected to this
     *      URL.
     *
     *      Any errors or a failure to authenticate will present error messages
     *      to the user and the browser will not be redirected to this URL.
     *
     *      This parameter does not need to be URL encoded. If no value is
     *      provided, $_SERVER['REQUEST_URI'] (the current page) will be used.
     *
     * @return string
     *
     *      The returned URL is properly URL encoded.
     */
    static function loginURL(?string $redirectURI = null): string {
        if (empty($redirectURI)) {
            $redirectURI = $_SERVER['REQUEST_URI'];
        }

        $state = (object)[
            'colby_redirect_uri' => $redirectURI,
        ];

        $URL =
        cbsiteurl() .
        '/colby/facebook-login/' .
        '?state=' .
        urlencode(
            json_encode($state)
        );

        return $URL;
    }
    /* loginURL() */



    /**
     * https://developers.facebook.com/docs/facebook-login/
     * manually-build-a-login-flow/#login
     *
     * @NOTE 2017_03_28
     *
     *      The Facebook URL below uses www.facebook.com instead of
     *      graph.facebook.com. This is on purpose, documented as such, and is
     *      required for this URL to work properly. The documentation doesn't
     *      say why.
     *
     * @return string
     */
    static function loginURLForFacebook(): string {
        $redirectURI = (
            cbsiteurl() .
            '/colby/facebook-oauth-handler/'
        );

        $loginURL = (
            'https://www.facebook.com/v3.3/dialog/oauth' .
            '?client_id=' .
            urlencode(
                CBFacebookPreferences::getAppID()
            ) .
            '&redirect_uri=' .
            urlencode($redirectURI)
        );

        return $loginURL;
    }
    /* loginURLForFacebook() */



    /**
     * https://developers.facebook.com/docs/graph-api/reference/user/picture/
     *
     * @param string $facebookID
     *
     * @return string
     */
    static function userImageURL(
        string $facebookID
    ): string {
        $userImageURL =
        "https://graph.facebook.com/v3.3/{$facebookID}/picture?type=large";

        return $userImageURL;
    }
    /* userImageURL() */

}
/* CBFacebook */
