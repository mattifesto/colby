<?php

require_once(__DIR__ . '/../colby-configuration.php');
require_once(__DIR__ . '/version.php');

class Colby
{
    // mysqli
    // holds the mysqli object if the request needs database access

    private static $mysqli = null;

    /**
     * If the site is marked as being debugged this function will send a message
     * to the PHP error log. If the site isn't being debugged it will do
     * nothing.
     *
     * This function works well for deprecated function messages and other
     * messages that a developer would want to know about but may not be able
     * take action on immediately.
     *
     * This function should not be used for messages that require immediate
     * action. Those issues should be resolved right away.
     *
     * @return void
     */
    public static function debugLog($message)
    {
        if (COLBY_SITE_IS_BEING_DEBUGGED)
        {
            error_log("Debug Log: {$message}");
        }
    }

    /**
     * @return string
     */
    public static function exceptionStackTrace($exception)
    {
        ob_start();

        include(COLBY_SITE_DIRECTORY .
            '/colby/snippets/exception-stack-trace.php');

        return ob_get_clean();
    }

    /**
     * @return bool
     */
    public static function isReadableFile($absoluteFilename)
    {
        // is_readable: file or directory exists and is readable
        // is_file: file is a regular file (not a directory)

        return (is_readable($absoluteFilename) && is_file($absoluteFilename));
    }

    /**
     * @return string | false
     */
    public static function findHandler($filename)
    {
        $absoluteHandlerFilename = COLBY_SITE_DIRECTORY . "/handlers/{$filename}";

        if (self::isReadableFile($absoluteHandlerFilename))
        {
            return $absoluteHandlerFilename;
        }

        $absoluteHandlerFilename = COLBY_SITE_DIRECTORY . "/colby/handlers/{$filename}";

        if (self::isReadableFile($absoluteHandlerFilename))
        {
            return $absoluteHandlerFilename;
        }
        else
        {
            return false;
        }
    }

    /**
     * @return string | false
     */
    public static function findSnippet($filename)
    {
        $absoluteSnippetFilename = COLBY_SITE_DIRECTORY . "/snippets/{$filename}";

        if (self::isReadableFile($absoluteSnippetFilename))
        {
            return $absoluteSnippetFilename;
        }

        $absoluteSnippetFilename = COLBY_SITE_DIRECTORY . "/colby/snippets/{$filename}";

        if (self::isReadableFile($absoluteSnippetFilename))
        {
            return $absoluteSnippetFilename;
        }
        else
        {
            return false;
        }
    }

    /**
     * @return void
     */
    public static function handleError($errno, $errstr, $errfile, $errline)
    {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    /**
     * @return void
     */
    public static function handleException($exception, $handlerName = null)
    {
        // exception handlers should never throw exceptions
        // if they do, it's very difficult to debug
        // in some cases while making more major system changes
        // throwing exceptions in exception handlers is unavoidable
        //     errors are converted to exceptions in Colby
        //     so even if we don't throw, exceptions can occur
        // so to be sure we always get some good feedback
        // we wrap all code in an exception handler in a try-catch block
        // and use error_log as a last resort to find out what went wrong
        // if an exception is thrown inside this exception handler

        try
        {
            $absoluteHandlerFilename = null;

            if ($handlerName)
            {
                $absoluteHandlerFilename = Colby::findHandler("handle-exception-{$handlerName}.php");
            }

            if (!$absoluteHandlerFilename)
            {
                $absoluteHandlerFilename = self::findHandler('handle-exception.php');
            }

            include($absoluteHandlerFilename);
        }
        catch (Exception $rareException)
        {
            error_log(var_export($rareException->getMessage(), true));
        }
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
                'constant COLBY_SITE_DIRECTORY has not been set');
        }

        if (!defined('COLBY_SITE_URL'))
        {
            throw new RuntimeException(
                'constant COLBY_SITE_URL has not been set');
        }

        if (!defined('COLBY_SITE_NAME'))
        {
            throw new RuntimeException(
                'constant COLBY_SITE_NAME has not been set');
        }

        if (!defined('COLBY_SITE_ADMINISTRATOR'))
        {
            throw new RuntimeException(
                'constant COLBY_SITE_ADMINISTRATOR has not been set');
        }

        if (!defined('COLBY_SITE_IS_BEING_DEBUGGED'))
        {
            throw new RuntimeException(
                'constant COLBY_SITE_IS_BEING_DEBUGGED has not been set');
        }

        // the order of these files might matter some day
        // files that depend on other files should be included after
        // at this time, none of these files depends on another
        // so they are in alphabetical order

        include_once(COLBY_SITE_DIRECTORY . '/colby/classes/ColbyArchive.php');

        include_once(COLBY_SITE_DIRECTORY . '/colby/classes/ColbyConvert.php');

        include_once(COLBY_SITE_DIRECTORY . '/colby/classes/ColbyOutputManager.php');

        include_once(COLBY_SITE_DIRECTORY . '/colby/classes/ColbyPageModel.php');

        include_once(COLBY_SITE_DIRECTORY . '/colby/classes/ColbyRequest.php');

        include_once(COLBY_SITE_DIRECTORY . '/colby/classes/ColbyUser.php');

        ColbyRequest::handleRequest();
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

            // The default MySQL character set is "latin1" but the tables use "utf8"

            if (!$mysqli->set_charset('utf8'))
            {
                throw new RuntimeException(
                    'Unable to set mysqli character set to "utf8".');
            }

            self::$mysqli = $mysqli;
        }

        return self::$mysqli;
    }

    ///
    /// simple way to run a query
    ///
    public static function query($sql)
    {
        $mysqli = Colby::mysqli();

        $result = $mysqli->query($sql);

        if ($mysqli->error)
        {
            throw new RuntimeException("MySQL error: {$mysqli->error}");
        }

        return $result;
    }

    ///
    ///
    ///
    public static function reportException($exception)
    {
        $headers = 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

        $subject = 'Exception thrown from "' .
            COLBY_SITE_NAME .
            '"';

        $stackTrace = htmlspecialchars(
            self::exceptionStackTrace($exception),
            ENT_QUOTES);

        $message = "<pre>{$stackTrace}</pre>\n";

        $result = mail(COLBY_SITE_ADMINISTRATOR,
            $subject,
            $message,
            $headers);
    }

    //
    //
    //
    public static function useImage()
    {
        include_once(COLBY_SITE_DIRECTORY .
            '/colby/classes/ColbyImage.php');
    }

    //
    //
    //
    public static function useRect()
    {
        include_once(COLBY_SITE_DIRECTORY .
            '/colby/classes/ColbyRect.php');
    }
}

Colby::initialize();
