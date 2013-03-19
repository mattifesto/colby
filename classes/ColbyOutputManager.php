<?php

class ColbyOutputManager
{
    private $template;

    /**
     * @return ColbyOutput
     */
    public function __construct($template = 'html-page')
    {
        $this->template = $template;

        $snippetFilename = Colby::findSnippet("{$this->template}-construct.php");

        if ($snippetFilename)
        {
            include $snippetFilename;
        }
    }

    /**
     * @return void
     */
    public function begin()
    {
        ob_start();

        set_exception_handler(array($this, 'handleException'));

        $snippetFilename = Colby::findSnippet("{$this->template}-begin.php");

        if ($snippetFilename)
        {
            include $snippetFilename;
        }
    }

    /**
     * @return ColbyOutputManager
     */
    public static function createAjaxResponse()
    {
        $outputManager = new ColbyOutputManager('ajax-response');

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
    public static function createPage($titleHTML, $descriptionHTML, $template = null)
    {
        $outputManager = new ColbyOutputManager($template);

        $outputManager->titleHTML = $titleHTML;
        $outputManager->descriptionHTML = $descriptionHTML;

        return $outputManager;
    }

    /**
     * @return ColbyOutputManager
     */
    public static function beginPage($titleHTML, $descriptionHTML, $template = null)
    {
        $outputManager = self::createPage($titleHTML, $descriptionHTML, $template);

        $outputManager->begin();

        return $outputManager;
    }

    /**
     * @return ColbyOutputManager
     */
    public static function beginVerifiedUserPage($titleHTML, $descriptionHTML, $template = null)
    {
        $outputManager = self::createPage($titleHTML, $descriptionHTML, $template);

        $outputManager->requireVerifiedUser();
        $outputManager->begin();

        return $outputManager;
    }

    /**
     * This function discards the page and any output that has been written
     * since the page was begun. In most cases, another page will be begun after
     * this function is called.
     *
     * @return void
     */
    public function discard()
    {
        restore_exception_handler();

        ob_end_clean();
    }

    /**
     * This function includes the appropriate footer, ends output buffering,
     * and flushes all content, which sends it to the browser.
     *
     * @return void
     */
    public function end()
    {
        $snippetFilename = Colby::findSnippet("{$this->template}-end.php");

        if ($snippetFilename)
        {
            include $snippetFilename;
        }

        restore_exception_handler();

        ob_end_flush();
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

        Colby::handleException($exception, $this->template);
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

        if ($this->template)
        {
            $handlerFilename = "handle-user-log-in-required-{$this->template}.php";

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

        if ($this->template)
        {
            $handlerFilename = "handle-user-verification-required-{$this->template}.php";

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
