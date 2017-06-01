<?php

/**
 * All interactions with the Facebook API should happen in this class. The API
 * has relatively frequent version upgrades and if the Facebook interaction is
 * spread around, updates are more difficult.
 */
final class CBFacebook {

    /**
     * @param string $code
     *
     * @return stdClass
     */
    static function fetchAccessTokenObject($code) {

        /**
         * NOTE: 2017.03.28
         * The `redirect_uri` in the request below is not used to redirect
         * anything. However, it is tested to confirm that it matches the
         * `redirect_uri` value that was present in the previous request that
         * provided the $code value.
         */

        $redirectURI = CBSitePreferences::siteURL() . '/colby/facebook-oauth-handler/';
        $URL = 'https://graph.facebook.com/v2.9/oauth/access_token' .
            '?client_id=' . COLBY_FACEBOOK_APP_ID .
            '&redirect_uri=' . urlencode($redirectURI) .
            '&client_secret=' . COLBY_FACEBOOK_APP_SECRET .
            '&code=' . $code;

        return CBFacebook::fetchGraphAPIResponse($URL);
    }

    static function fetchGraphAPIResponse($URL) {
        $curlHandle = curl_init();

        curl_setopt($curlHandle, CURLOPT_URL, $URL);
        curl_setopt($curlHandle, CURLOPT_HEADER, 0);
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);

        /**
         * NOTE: 2014.08.03
         * Sometimes Facebook responds very slowly to these requests. It's not the
         * web, direct requests from the server are also slow. There's a theory that
         * it has something to do with "round robin DNS", but I never figured that out.
         * However, setting this time out for some reason makes it respond faster.
         * There should be a time out regardless, but I just wanted to document the
         * importance of this.
         */
        curl_setopt($curlHandle, CURLOPT_CONNECTTIMEOUT, 5);

        $response = curl_exec($curlHandle);

        if ($response === false) {
            $error = curl_errno($curlHandle);
            curl_close($curlHandle);
            throw new RuntimeException("Facebook OAuth: The request to exchange a code for an access token failed with the error: {$error}");
        }

        $httpCode = curl_getinfo($curlHandle, CURLINFO_HTTP_CODE);

        if ($httpCode === 400) {
            $object = json_decode($response);

            throw new RuntimeException('Error retrieving Facebook Graph API response: ' .
                $object->error->type .
                ', ' .
                $object->error->message);
        }

        curl_close($curlHandle);

        return json_decode($response);
    }

    /**
     * @param string $accessToken
     *
     * @return stdClass
     */
    static function fetchUserProperties($accessToken) {
        $fields = 'fields=name,metadata{type}';
        $URL = "https://graph.facebook.com/v2.9/me?access_token={$accessToken}&metadata=1&{$fields}";

        return CBFacebook::fetchGraphAPIResponse($URL);
    }

    /**
     * @param $redirectURL
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
     *      The returned URL is properly URL encoded.
     */
    static function loginURL($redirectURL = null) {
        if (!$redirectURL) {
            $redirectURL = $_SERVER['REQUEST_URI'];
        }

        $state = new stdClass();
        $state->colby_redirect_uri = $redirectURL;

        $redirectURI = CBSitePreferences::siteURL() . '/colby/facebook-oauth-handler/';

        /**
         * NOTE: 2017.03.28
         * The Facebook URL below uses www.facebook.com instead of
         * graph.facebook.com. This is on purpose, documented as such, and is
         * required for this URL to work properly. The documentation doesn't say
         * why.
         */

        $URL = 'https://www.facebook.com/v2.9/dialog/oauth' .
            '?client_id=' . CBFacebookAppID .
            '&redirect_uri=' . urlencode($redirectURI) .
            '&state=' . urlencode(json_encode($state));

        return $URL;
    }

    /**
     * @param string $facebookID
     *
     * @return string
     */
    static function userImageURL($facebookID) {
        return "https://graph.facebook.com/v2.9/{$facebookID}/picture?type=large";
    }
}
