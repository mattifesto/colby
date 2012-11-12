<?php

class ColbyAjax
{
    /**
     * @return void
     */
    public static function begin()
    {
        ob_start();

        set_exception_handler('ColbyAjax::handleException');
    }

    /**
     * @return void
     */
    public static function end()
    {
        header('Content-type: application/json');

        ob_end_flush();
    }

    /**
     * @return void
     */
    public static function handleException($exception)
    {
        // remove the current exception handler
        // we got here by handling an exception so we're done with it

        restore_exception_handler();

        // since we're mid-output we know we have output buffering turned on
        // clear the buffer and turn off output buffering

        ob_end_clean();

        // for now just pass the exception to Colby

        Colby::handleException($exception, 'ajax');
    }

    /**
     * Call this before any content has been output on a page that should only be viewed by verified users.
     *
     * @return void
     */
    public static function requireVerifiedUser()
    {
        $userRow = ColbyUser::userRow();

        if ($userRow === null)
        {
            $handler = Colby::findHandler('handle-user-log-in-required-ajax.php');

            include($handler);

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
                $handler = Colby::findHandler('handle-user-verification-required-ajax.php');

                include($handler);

                exit;
            }
        }
    }
}
