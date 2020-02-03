<?php

define(
    'CBUserCookieName',
    'colby-user-encrypted-data'
);



final class ColbyUser {

    /* -- private static variables -- -- -- -- -- */



    /**
     * If a user is logged in we store their user CBID.
     */
    private static $currentUserCBID = null;



    /* -- functions -- -- -- -- -- */



    /**
     * @return bool
     */
    static function currentUserIsLoggedIn(): bool {
        return !empty(
            ColbyUser::$currentUserCBID
        );
    }
    /* currentUserIsLoggedIn() */



    /**
     * @return CBID|null
     *
     *      Returns the current user's CBID if a user is logged in; otherwise
     *      null.
     */
    static function getCurrentUserCBID(): ?string {
        return ColbyUser::$currentUserCBID;
    }
    /* getCurrentUserCBID() */



    /**
     * @deprecated use ColbyUser::getCurrentUserCBID()
     */
    static function getCurrentUserID(): ?string {
        return ColbyUser::getCurrentUserCBID();
    }
    /* getCurrentUserID() */



    /**
     * @param string $groupName
     *
     * @return string|false
     */
    static function groupNameToTableName($groupName) {
        if (
            !preg_match(
                '/^[a-zA-Z0-9]+$/',
                $groupName
            )
        ) {
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
    static function initialize(): void {
        if (!isset($_COOKIE[CBUserCookieName])) {
            return;
        }

        $cookieCipherData = $_COOKIE[CBUserCookieName];

        try {
            $cookie = CBConvert::valueAsObject(
                Colby::decrypt($cookieCipherData)
            );

            if ($cookie === null) {
                ColbyUser::removeUserCookie();
                return;
            }

            $expirationTimestamp = CBModel::valueAsInt(
                $cookie,
                'expirationTimestamp'
            ) ?? PHP_INT_MIN;

            if (time() > $expirationTimestamp) {
                ColbyUser::removeUserCookie();
                return;
            }

            $userCBID = CBModel::valueAsCBID(
                $cookie,
                'userCBID'
            );

            if ($userCBID === null) {
                ColbyUser::removeUserCookie();
                return;
            }

            /* Success, the user is now logged in. */
            ColbyUser::$currentUserCBID = $userCBID;
        } catch (Throwable $exception) {
            CBErrorHandler::report($exception);
            ColbyUser::removeUserCookie();
        }
    }
    /* initialize() */



    /**
     * This is a very powerful function. It logs in the user whose model has the
     * CBID passed to this function. It does no verification whatsoever. This
     * should always be the last step of the login process and should be
     * prededed by verifications.
     *
     * @param CBID $userCBID
     *
     * @return void
     */
    static function loginUser(
        string $userCBID
    ): void {
        if (
            !CBID::valueIsCBID($userCBID)
        ) {
            throw new CBExceptionWithValue(
                'The $userCBID parameter is not a valid CBID.',
                $userCBID,
                'ca9e310f0892223959716455b88e1b16a446d61d'
            );
        }

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
            'userCBID' => $userCBID,

            /* 24 hours from now */
            'expirationTimestamp' => time() + (60 * 60 * 24),
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
    /* loginUser() */



    /**
     * This function is called at after Facebook authenticates a user that wants
     * to log in. It updates the database and sets a cookie in the user's
     * browser confirms their identity and that they are logged in for future
     * page requests.
     *
     * Since it sets a cookie it must be called before any HTML is ouput.
     *
     * @param int $facebookUserID
     * @param string $facebookAccessToken
     * @param object $facebookName
     *
     * @return void
     */
    static function loginFacebookUser(
        int $facebookUserID,
        string $facebookAccessToken,
        string $facebookName
    ): void {
        $countOfUsers = 1;

        $userCBID = CBUser::facebookUserIDToUserCBID(
            $facebookUserID
        );

        if ($userCBID === null) {
            $userCBID = CBID::generateRandomCBID();
            $countOfUsers = CBUsers::countOfUsers();
        }

        $userSpecUpdates = (object)[
            'className' => 'CBUser',
            'ID' => $userCBID,
            'facebookAccessToken' => $facebookAccessToken,
            'facebookName' => $facebookName,
            'facebookUserID' => $facebookUserID,
            'lastLoggedIn' => time(),
        ];

        CBModelUpdater::update(
            $userSpecUpdates
        );

        if ($countOfUsers < 2) {
            CBUser::initializeFirstUser($userCBID);
        }

        ColbyUser::loginUser(
            $userCBID
        );
    }
    /* loginFacebookUser() */



    /**
     * This function must be called before any output is generated because it
     * sets a cookie.
     */
    static function logoutCurrentUser() {
        ColbyUser::removeUserCookie();
    }



    /**
     * @param string $redirect
     *
     *      The URL to go to after logging out.
     *
     *      This URL should not be escaped for use in HTML.
     *
     *      The URL can be URL encoded or not. (If a case is found where it
     *      needs to be one or the other, update this documentation.)
     *
     *      If no URL is provided, $_SERVER['REQUEST_URI'] will be used.
     *
     * @return string
     *
     *      The URL to visit to log out. This URL will be properly URL encoded.
     *      This URL will not be escaped for use in HTML.
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
    /* logoutURL() */



    /**
     * The expiration timestamp is set to
     *
     *      current time - 24 hours
     *
     * because this value is guaranteed to be in the past in all time zones.
     *
     * This function must be called before any output is generated because it
     * sets a cookie.
     */
    private static function removeUserCookie() {
        $value = '';
        $expirationTimestamp = time() - (60 * 60 * 24);
        $path = '/';

        setcookie(
            CBUserCookieName,
            $value,
            $expirationTimestamp,
            $path
        );
    }
    /* removeUserCookie() */

}



ColbyUser::initialize();
