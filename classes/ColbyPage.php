<?php

class ColbyPage
{
    public static $title = COLBY_SITE_NAME;
    public static $description = COLBY_SITE_DESCRIPTION;
    public static $cssURLs = array();
    public static $jsURLs = array();

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
    ///
    ///
    public static function writeHeader()
    {
        ob_start();

        set_exception_handler('ColbyPage::handleException');

        require(COLBY_SITE_DIRECTORY . '/snippets/header.php');
    }

    ///
    ///
    ///
    public static function writeFooter()
    {
        require(COLBY_SITE_DIRECTORY . '/snippets/footer.php');

        restore_exception_handler();

        ob_end_flush();
    }
}
