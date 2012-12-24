<?php

class ColbyOutputManager
{
    private $name;

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
    public static function createAjaxResponse()
    {
        $outputManager = new ColbyOutputManager('ajax');

        $outputManager->wasSuccessful = false;
        $outputManager->message = 'An official response message was never set.';

        return $outputManager;
    }

    /**
     * @return ColbyOutputManager
     */
    public static function beginAjaxResponse()
    {
        $outputManager = self::createAjaxResponse();

        $outputManager->begin();

        return $outputManager;
    }

    /**
     * @return ColbyOutputManager
     */
    public static function beginVerifiedUserAjaxResponse()
    {
        $outputManager = self::createAjaxResponse();

        $outputManager->requireVerifiedUser();
        $outputManager->begin();

        return $outputManager;
    }

    /**
     * @return ColbyOutputManager
     */
    public static function createPage($titleHTML, $descriptionHTML, $name = null)
    {
        $outputManager = new ColbyOutputManager($name);

        $outputManager->titleHTML = $titleHTML;
        $outputManager->descriptionHTML = $descriptionHTML;

        return $outputManager;
    }

    /**
     * @return ColbyOutputManager
     */
    public static function beginPage($titleHTML, $descriptionHTML, $name = null)
    {
        $outputManager = self::createPage($titleHTML, $descriptionHTML, $name);

        $outputManager->begin();

        return $outputManager;
    }

    /**
     * @return ColbyOutputManager
     */
    public static function beginVerifiedUserPage($titleHTML, $descriptionHTML, $name = null)
    {
        $outputManager = self::createPage($titleHTML, $descriptionHTML, $name);

        $outputManager->requireVerifiedUser();
        $outputManager->begin();

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
     * These methods must remain private because that's how their timing is enforced.
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

        include($absoluteHandlerFilename);

        exit;
    }

    /**
     * Call this before any content has been output on a page that should only be viewed by verified users.
     * These methods must remain private because that's how their timing is enforced.
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

        include($absoluteHandlerFilename);

        exit;
    }
}
