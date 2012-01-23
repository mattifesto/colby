<?php

//
// colby
//
// version 0.0.8
//

error_reporting(E_ALL | E_STRICT);

require_once(__DIR__ . '/../colby-configuration.php');
require_once(__DIR__ . '/classes/MDContainer.php');

set_error_handler('Colby::handleError');
set_exception_handler('Colby::handleException');

class Colby
{
    // urlParse
    // BUGBUG: not exactly sure why we keep this around

    private static $urlParser;

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
    ///
    ///
    public static function includeEqualizeStylesheet()
    {
        echo '<link rel="stylesheet" type="text/css" href="',
            COLBY_SITE_URL,
            'colby/css/equalize.css">';
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

    /// <summary>
    ///
    /// </summary>
    public static function urlParser()
    {
        require_once(COLBY_SITE_PATH . '/colby/classes/ColbyURLParser.php');

        // TODO: document why is this in the singleton pattern
        // or don't use singleton pattern

        if (!isset($urlParser))
        {
            self::$urlParser = new ColbyURLParser();
        }

        return self::$urlParser;
    }

    ///
    /// this works best if called before any html output
    /// it sets a cookie
    ///
    public static function useUser()
    {
        require_once(__DIR__ . '/classes/ColbyUser.php');

        ColbyUser::initialize();
    }
}
