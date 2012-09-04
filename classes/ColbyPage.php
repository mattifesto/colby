<?php

class ColbyPage
{
    private static $args = null;

    ///
    ///
    ///
    public static function begin($args)
    {
        if (!isset($args->header))
        {
            $args->header = COLBY_SITE_DIRECTORY .
                '/snippets/header.php';
        }

        // TODO: I think these checks should be in the individual header files
        //       I can imagine cases where the header is default (an exception)
        //       think about it

        if (!isset($args->title))
        {
            throw new RuntimeException('$args->title is not set ' .
                '- a title is required');
        }

        if (!isset($args->description))
        {
            throw new RuntimeException('$args->description is not set ' .
                '- a description is required');
        }

        self::$args = $args;

        ob_start();

        set_exception_handler('ColbyPage::handleException');

        // we include (instead of require) the header file
        // because we will show an exception page if the header doesn't exist
        //
        // as of this writing, the goal of exception pages is not to be pretty
        // but to send an email, or otherwise notify,
        // the developer about the exception
        // it doesn't matter if the page is pretty
        // because an unhandled exception is equivalent to a crash
        // it should never ever happen

        include($args->header);
    }

    ///
    ///
    ///
    public static function beginAdmin($args)
    {
        $args->header = COLBY_SITE_DIRECTORY . '/colby/snippets/header.php';
        $args->footer = COLBY_SITE_DIRECTORY . '/colby/snippets/footer.php';

        self::begin($args);
    }

    ///
    ///
    ///
    public static function end()
    {
        if (!isset(self::$args->footer))
        {
            self::$args->footer = COLBY_SITE_DIRECTORY .
                '/snippets/footer.php';
        }

        include(self::$args->footer);
    }

    ///
    ///
    ///
    public static function handleException($e)
    {
        // remove the current page exception handler
        // we got here by handling an exception so we're done with it

        restore_exception_handler();

        // since we're mid-page we know we have output buffering turned on
        // clear the buffer and turn off output buffering

        ob_end_clean();

        // for now just pass the exception to Colby

        Colby::handleException($e);
    }

    ///
    /// call this before any content has been output
    /// on a page that should only be viewed by verified users
    ///
    public static function requireVerifiedUser()
    {
        $userRow = ColbyUser::userRow();

        if ($userRow === null)
        {
            include(COLBY_SITE_DIRECTORY .
                '/colby/snippets/user-login-required-page.php');

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
                Colby::query('CALL VerifyUser(' . $userRow->id . ')');
            }
            else
            {
                include(COLBY_SITE_DIRECTORY .
                    '/colby/snippets/user-verification-required-page.php');

                exit;
            }
        }
    }
}
