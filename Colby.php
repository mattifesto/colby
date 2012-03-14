<?php

//
// colby
//
// version 1.1.1
//

class Colby
{
    // mysqli
    // holds the mysqli object if the request needs database access

    private static $mysqli = null;

    ///
    ///
    ///
    public static function handleError($errno, $errstr, $errfile, $errline)
    {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    ///
    ///
    ///
    public static function handleException($e)
    {
        require_once(__DIR__ . '/pages/exception.php');
    }

    ///
    /// TODO: remove this function
    /// just document the location of equalize.css
    ///
    public static function includeEqualizeStylesheet()
    {
        error_log('includeEqualizeStylesheet() has been deprecated ' .
            'just directly include /colby/css/equalize.css');
            
        echo '<link rel="stylesheet" type="text/css" href="',
            COLBY_SITE_URL,
            '/colby/css/equalize.css">';
    }

    ///
    /// this function should be run only once
    /// it is run automatically when Colby is first included
    ///
    public static function initialize()
    {
        // Colby sites always run with all error reporting turned on
        // in the worst case scenario
        // the production environment will have newer PHP
        // which may lead to surprise errors
        // but those errors should still be fixed in the dev environment

        error_reporting(E_ALL | E_STRICT);

        set_error_handler('Colby::handleError');
        set_exception_handler('Colby::handleException');

        if (!defined('COLBY_SITE_DIRECTORY'))
        {
            throw new RuntimeException(
                'required constant COLBY_SITE_DIRECTORY has not been set');
        }

        if (!defined('COLBY_SITE_URL'))
        {
            throw new RuntimeException(
                'required constant COLBY_SITE_URL has not been set');
        }
    }

    /// <summary>
    ///
    /// </summary>
    public static function mysqli()
    {
        if (null === self::$mysqli)
        {
            $mysqli = new mysqli(
                COLBY_MYSQL_HOST,
                COLBY_MYSQL_USER,
                COLBY_MYSQL_PASSWORD,
                COLBY_MYSQL_DATABASE);

            if ($mysqli->connect_error)
            {
                throw new RuntimeException($mysqli->connect_error);
            }

            self::$mysqli = $mysqli;
        }

        return self::$mysqli;
    }

    ///
    /// converts plain text into formatted content HTML
    ///
    ///  - trims whitespace
    ///  - converts html special characters to entities
    ///  - interprets textual formatting to create formatted content HTML
    ///
    public static function textToFormattedContent($text)
    {
        $html = self::textToHTML($text);

        $html = preg_replace('/[\r\n]+/', "\n<p>", $html);

        if ($html)
        {
            $html = "<p>{$html}";
        }

        return $html;
    }

    ///
    /// converts plain text to HTML
    ///
    ///  - trims whitespace
    ///  - converts html special characters to entities
    ///
    /// this fuction exists because it's so easy to forget
    /// the details on how this should be done
    /// such as the inclusion of ENT_QUOTES
    /// also because if I figure out something else is required
    /// I can now change it in just one place
    ///
    public static function textToHTML($text)
    {
        return htmlspecialchars(trim($text), ENT_QUOTES);
    }

    ///
    ///
    ///
    public static function useURLParser()
    {
        require_once(COLBY_SITE_DIRECTORY .
            '/colby/classes/ColbyURLParser.php');
    }

    ///
    /// this works best if called before any html output
    /// it sets a cookie
    ///
    public static function useUser()
    {
        require_once(__DIR__ . '/classes/ColbyUser.php');
    }

    ///
    ///
    ///
    public static function writeExceptionStackTrace($e)
    {
        require(__DIR__ . '/snippets/exception-stack-trace.php');
    }
}

Colby::initialize();
