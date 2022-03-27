<?php

define(
    'CBUserCookieName',
    'colby-user-encrypted-data'
);



final class
ColbyUser
{
    /* -- private static variables -- */



    /**
     * If a user is logged in we store their user CBID.
     */

    private static
    $currentUserCBID =
    null;



    /* -- functions -- */



    /**
     * @return bool
     */
    static function
    currentUserIsLoggedIn(
    ): bool
    {
        return
        !empty(
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
    static function
    getCurrentUserCBID(
    ): ?string
    {
        return
        ColbyUser::$currentUserCBID;
    }
    /* getCurrentUserCBID() */



    /**
     * @deprecated use ColbyUser::getCurrentUserCBID()
     */
    static function
    getCurrentUserID(
    ): ?string
    {
        return
        ColbyUser::getCurrentUserCBID();
    }
    /* getCurrentUserID() */



    /**
     * @param string $groupName
     *
     * @return string|false
     */
    static function
    groupNameToTableName(
        $groupName
    )
    {
        if (
            !preg_match(
                '/^[a-zA-Z0-9]+$/',
                $groupName
            )
        ) {
            return false;
        }

        return
        "ColbyUsersWhoAre{$groupName}";
    }
    // groupNameToTableName()



    /**
     * This function should be run only once. It is run when this class is first
     * loaded.
     *
     * @return void
     */
    static function
    initialize(
    ): void
    {
        if (
            !isset(
                $_COOKIE[CBUserCookieName]
            )
        ) {
            return;
        }

        $cookieCipherData =
        $_COOKIE[CBUserCookieName];

        try
        {
            $cookie =
            CBConvert::valueAsObject(
                Colby::decrypt(
                    $cookieCipherData
                )
            );

            if (
                $cookie === null
            ) {
                ColbyUser::logoutCurrentUser();

                return;
            }

            $expirationTimestamp =
            CBModel::valueAsInt(
                $cookie,
                'expirationTimestamp'
            ) ??
            PHP_INT_MIN;

            if (
                time() > $expirationTimestamp
            ) {
                ColbyUser::logoutCurrentUser();

                return;
            }

            $userCBID =
            CBModel::valueAsCBID(
                $cookie,
                'userCBID'
            );

            if (
                $userCBID === null
            ) {
                ColbyUser::logoutCurrentUser();

                return;
            }

            /* Success, the user is now logged in. */
            ColbyUser::$currentUserCBID =
            $userCBID;
        }

        catch (
            Throwable $exception
        ) {
            CBErrorHandler::report(
                $exception
            );

            ColbyUser::logoutCurrentUser();
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
    static function
    loginUser(
        string $userCBID,
        bool $shouldKeepSignedIn = false
    ): void {
        $userModelCBIDIsACBID =
        CBID::valueIsCBID(
            $userCBID
        );

        if (
            $userModelCBIDIsACBID !== true
        ) {
            throw new CBExceptionWithValue(
                'The $userCBID parameter is not a valid CBID.',
                $userCBID,
                'ca9e310f0892223959716455b88e1b16a446d61d'
            );
        }



        /**
         * Set the Colby user cookie.
         */

        if (
            $shouldKeepSignedIn
        ) {
            /* 30 days from now */
            $expirationTimestamp =
            time() +
            (
                60 *
                60 *
                24 *
                30
            );
        }

        else
        {
            /* 10 hours from now */
            $expirationTimestamp =
            time() +
            (
                60 *
                60 *
                10
            );
        }

        $cookie =
        (object)
        [
            'userCBID' =>
            $userCBID,

            'expirationTimestamp' =>
            $expirationTimestamp,
        ];

        $encryptedCookie =
        Colby::encrypt(
            $cookie
        );

        /* 60 days from now */
        $cookieExpirationTimestamp =
        time() +
        (
            60 *
            60 *
            24 *
            60
        );

        $path =
        '/';

        $domain =
        '';

        $secureConnectionsOnly =
        true;

        setcookie(
            CBUserCookieName,
            $encryptedCookie,
            $cookieExpirationTimestamp,
            $path,
            $domain,
            $secureConnectionsOnly
        );



        ColbyUser::$currentUserCBID =
        $userCBID;
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
    static function
    loginFacebookUser(
        int $facebookUserID,
        string $facebookAccessToken,
        string $facebookName
    ): void
    {
        $userCBID =
        CBUser::facebookUserIDToUserCBID(
            $facebookUserID
        );

        if (
            $userCBID === null
        ) {
            $userCBID =
            CBID::generateRandomCBID();
        }

        $userSpecUpdates =
        (object)
        [
            'className' =>
            'CBUser',

            'ID' =>
            $userCBID,

            'facebookAccessToken' =>
            $facebookAccessToken,

            'facebookName' =>
            $facebookName,

            'facebookUserID' =>
            $facebookUserID,

            'lastLoggedIn' =>
            time(),
        ];

        CBModelUpdater::update(
            $userSpecUpdates
        );

        ColbyUser::loginUser(
            $userCBID
        );
    }
    /* loginFacebookUser() */



    /**
     * This function must be called before any output is generated because it
     * sets a cookie.
     */
    static function
    logoutCurrentUser(
    ): void
    {
        $currentUserModelCBID =
        ColbyUser::getCurrentUserCBID();

        if (
            $currentUserModelCBID !== null
        ) {
            $userSpecUpdater =
            new CBModelUpdater(
                $currentUserModelCBID
            );

            $userSpec =
            $userSpecUpdater->getSpec();

            CBUser::setFacebookAccessToken(
                $userSpec,
                ''
            );

            CBDB::transaction(
                function () use (
                    $userSpecUpdater
                ): void
                {
                    $userSpecUpdater->save2();
                }
            );
        }

        ColbyUser::removeUserCookie();
    }
    // logoutCurrentUser()



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
    private static function
    removeUserCookie(
    ): void
    {
        $value =
        '';

        $expirationTimestamp =
        time() -
        (
            60 *
            60 *
            24
        );

        $path =
        '/';

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
