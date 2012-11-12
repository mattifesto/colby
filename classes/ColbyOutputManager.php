<?php

class ColbyOutputManager
{
    private $name;
    private $hasBegun = false;

    /**
     * @return ColbyOutput
     */
    public function __construct($name = null)
    {
        $this->name = $name;
    }

    /**
     * @return void
     */
    public function begin()
    {
        ob_start();

        set_exception_handler(array($this, 'handleException'));

        $this->hasBegun = true;

        $absoluteHeaderSnippetFilename = null;

        if ($this->name)
        {
            $absoluteHeaderSnippetFilename = Colby::findSnippet("header-{$this->name}.php");
        }

        if (!$absoluteHeaderSnippetFilename)
        {
            $absoluteHeaderSnippetFilename = Colby::findSnippet('header.php');
        }

        include($absoluteHeaderSnippetFilename);
    }

    /**
     * @return ColbyOutputManager
     */
    public static function beginAjaxResponse()
    {
        $outputManager = new ColbyOutputManager('ajax');

        $outputManager->wasSuccessful = false;
        $outputManager->message = 'An official response message was never set.';

        $outputManager->begin();

        return $outputManager;
    }

    /**
     * @return ColbyOutputManager
     */
    public static function beginVerifiedUserAjaxResponse()
    {
        $outputManager = self::beginAjaxResponse();

        $outputManager->requireVerifiedUser();

        return $outputManager;
    }

    /**
     * @return ColbyOutputManager
     */
    public static function beginPage($title, $description, $name = null)
    {
        $outputManager = new ColbyOutputManager($name);

        $outputManager->title = $title;
        $outputManager->description = $description;

        $outputManager->begin();

        return $outputManager;
    }

    /**
     * @return ColbyOutputManager
     */
    public static function beginVerifiedUserPage($title, $description, $name = null)
    {
        $outputManager = self::beginPage($title, $description, $name);

        $outputManager->requireVerifiedUser();

        return $outputManager;
    }

    /**
     * @return void
     */
    public function end()
    {
        $absoluteFooterSnippetFilename = null;

        if ($this->name)
        {
            $absoluteFooterSnippetFilename = Colby::findSnippet("footer-{$this->name}.php");
        }

        if (!$absoluteFooterSnippetFilename)
        {
            $absoluteFooterSnippetFilename = Colby::findSnippet('footer.php');
        }

        include($absoluteFooterSnippetFilename);
    }

    /**
     * @return void
     */
    public function handleException($exception)
    {
        // Remove the current exception handler.
        // We got here by handling an exception so we're done with that handler.

        restore_exception_handler();

        // Since we're mid-output we know we have output buffering turned on.
        // Clear the buffer and turn off output buffering.

        ob_end_clean();

        // Use the main Colby exception handler to create exception related output.

        Colby::handleException($exception, $this->name);
    }

    /**
     * Call this before any content has been output on a page that should only be viewed by logged in users.
     *
     * @return object (user row) | exit
     */
    private function requireLoggedInUser()
    {
        $userRow = ColbyUser::userRow();

        if ($userRow)
        {
            return $userRow;
        }

        $absoluteHandlerFilename = null;

        if ($this->name)
        {
            $handlerFilename = "handle-user-log-in-required-{$this->name}.php";

            $absoluteHandlerFilename = Colby::findHandler($handlerFilename);
        }

        if (!$absoluteHandlerFilename)
        {
            $absoluteHandlerFilename = Colby::findHandler('handle-user-log-in-required.php');
        }

        if ($hasBegun)
        {
            ob_end_clean();
        }

        include($absoluteHandlerFilename);

        exit;
    }

    /**
     * Call this before any content has been output on a page that should only be viewed by verified users.
     *
     * @return void | exit
     */
    private function requireVerifiedUser()
    {
        $userRow = self::requireLoggedInUser();

        if ($userRow->hasBeenVerified)
        {
            return;
        }

        // If the first verified user id in the configuration file
        // matches the current user, then verify the current user.
        // This will usually happen only once in the lifetime of a website.
        // It's the simplest way to verify the first verified user.
        // That user can then use the admin pages to verify other users.

        if (COLBY_FACEBOOK_FIRST_VERIFIED_USER_ID === $userRow->facebookId)
        {
            Colby::query('CALL ColbyVerifyUser(' . $userRow->id . ')');

            return;
        }

        // The user is not verified.

        $absoluteHandlerFilename = null;

        if ($this->name)
        {
            $handlerFilename = "handle-user-verification-required-{$this->name}.php";

            $absoluteHandlerFilename = Colby::findHandler($handlerFilename);
        }

        if (!$absoluteHandlerFilename)
        {
            $absoluteHandlerFilename = Colby::findHandler('handle-user-verification-required.php');
        }

        if ($hasBegun)
        {
            ob_end_clean;
        }

        include($absoluteHandlerFilename);

        exit;
    }
}
