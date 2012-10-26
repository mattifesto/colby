<?php

class ColbyAjax
{
    ///
    ///
    ///
    public static function /* void */ begin()
    {
        ob_start();

        set_exception_handler('ColbyAjax::handleException');
    }

    ///
    ///
    ///
    public static function /* void */ end()
    {
        header('Content-type: application/json');

        ob_end_flush();
    }

    ///
    ///
    ///
    public static function /* void */ handleException($exception)
    {
        // remove the current ajax exception handler
        // we got here by handling an exception so we're done with it

        restore_exception_handler();

        // since we're mid-output we know we have output buffering turned on
        // clear the buffer and turn off output buffering

        ob_end_clean();

        // for now just pass the exception to Colby

        Colby::handleException($exception, 'ajax');
    }

    ///
    /// call this before any content has been output
    /// on a page that should only be viewed by verified users
    ///
    public static function /* void */ requireVerifiedUser()
    {
        $userRow = ColbyUser::userRow();

        if ($userRow === null)
        {
            include(COLBY_SITE_DIRECTORY .
                '/colby/snippets/user-login-required-ajax-response.php');

            exit;
        }
        else if (!$userRow->hasBeenVerified)
        {
            // If the first verified user id in the configuration file
            // matches the current user, then verify the current user.
            // This will usually happen only once in the lifetime of a website.
            // It's the simplest way to verify the first verified user.
            // That user can then use the admin pages to verify other users.

            if (COLBY_FACEBOOK_FIRST_VERIFIED_USER_ID === $userRow->facebookId)
            {
                Colby::query('CALL ColbyVerifyUser(' . $userRow->id . ')');
            }
            else
            {
                include(COLBY_SITE_DIRECTORY .
                    '/colby/snippets/user-verification-required-ajax-response.php');

                exit;
            }
        }
    }
}
