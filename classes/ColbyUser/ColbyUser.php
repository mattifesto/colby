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



    /**
     * @param int $facebookUserID
     *
     * @return object|null
     *
     *      {
     *          userCBID: CBID
     *
     *          userNumericID: int (deprecated)
     *      }
     */
    private static function facebookUserIDToCBUserIDs(
        int $facebookUserID
    ): ?stdClass {
        $SQL = <<<EOT

            SELECT  LOWER(HEX(hash)) AS userCBID,
                    id as userNumericID

            FROM    ColbyUsers

            WHERE   facebookId = {$facebookUserID}

        EOT;

        return CBDB::SQLToObjectNullable($SQL);
    }
    /* facebookUserIDToCBUserIDs() */



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
        $userSpec = ColbyUser::updateFacebookUser(
            $facebookUserID,
            $facebookAccessToken,
            $facebookName
        );

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
            'userCBID' => $userSpec->ID,

            /* deprecated */
            'userNumericID' => $userSpec->userNumericID,

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
    /* loginCurrentUser() */



    /**
     * This function must be called before any output is generated because it
     * sets a cookie.
     */
    static function logoutCurrentUser() {
        ColbyUser::removeUserCookie();
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



    /**
     * @param int $facebookUserID
     * @param string $facebookAccessToken
     * @param object $facebookName
     *
     * @return object
     *
     *      Returns the updated CBUser model for the user.
     */
    static function updateFacebookUser(
        int $facebookUserID,
        string $facebookAccessToken,
        string $facebookName
    ): stdClass {
        $isFirstUser = false;

        if ($facebookUserID <= 0) {
            throw CBException::createWithValue(
                'The Facebook user ID is invalid.',
                $facebookUserID,
                '2417ee1efc46f05e3ae75cd6050ea4c52c719a65'
            );
        }

        $CBUserIDs = ColbyUser::facebookUserIDToCBUserIDs(
            $facebookUserID
        );

        $facebookNameAsSQL = CBDB::stringToSQL($facebookName);

        Colby::query('START TRANSACTION');

        if ($CBUserIDs !== null) {
            $SQL = <<<EOT

                UPDATE  ColbyUsers

                SET     facebookName = {$facebookNameAsSQL}

                WHERE   id = {$CBUserIDs->userNumericID}

            EOT;

            Colby::query($SQL);
        } else {
            $CBUserIDs = (object)[
                'userCBID' => CBID::generateRandomCBID(),
            ];

            $userCBIDAsSQL = CBID::toSQL($CBUserIDs->userCBID);

            $SQL = <<<EOT

                INSERT INTO ColbyUsers (
                    hash,
                    facebookId,
                    facebookName
                ) VALUES (
                    {$userCBIDAsSQL},
                    {$facebookUserID},
                    {$facebookNameAsSQL}
                )

            EOT;

            Colby::query($SQL);

            $CBUserIDs->userNumericID = intval(Colby::mysqli()->insert_id);

            /* Detect first user */

            $SQL = <<<EOT

                SELECT  COUNT(*)
                FROM    ColbyUsers

            EOT;

            $count = CBDB::SQLToValue($SQL);

            if ($count === '1') {
                $isFirstUser = true;
            }
        }

        $updater = CBModelUpdater::fetch(
            (object)[
                'className' => 'CBUser',
                'ID' => $CBUserIDs->userCBID,
                'facebookAccessToken' => $facebookAccessToken,
                'facebookName' => $facebookName,
                'facebookUserID' => $facebookUserID,
                'lastLoggedIn' => time(),
                'title' => $facebookName,
                'userNumericID' => $CBUserIDs->userNumericID,
            ]
        );

        if ($updater->working != $updater->original) {
            CBModels::save($updater->working);
        }

        if ($isFirstUser) {
            CBUserGroup::addUsers(
                'CBAdministratorsUserGroup',
                $CBUserIDs->userCBID
            );

            CBUserGroup::addUsers(
                'CBDevelopersUserGroup',
                $CBUserIDs->userCBID
            );
        }

        /* All database updates are complete */

        Colby::query('COMMIT');

        return $updater->working;
    }
    /* updateFacebookUser() */

}



ColbyUser::initialize();
