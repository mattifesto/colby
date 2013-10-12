<?php

class ColbyOutputManager
{
    private $templateName;

    public $cssFilenames = array();
    public $javaScriptFilenames = array();

    public $titleHTML = '';
    public $descriptionHTML = '';

    /**
     * @return ColbyOutput
     */
    public function __construct($templateName = 'html-page')
    {
        $this->templateName = $templateName;

        $snippetFilename = Colby::findSnippet("{$this->templateName}-construct.php");

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

        $snippetFilename = Colby::findSnippet("{$this->templateName}-begin.php");

        if ($snippetFilename)
        {
            include $snippetFilename;
        }
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
        $snippetFilename = Colby::findSnippet("{$this->templateName}-end.php");

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

        Colby::handleException($exception, $this->templateName);
    }
}
