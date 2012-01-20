<?php

//
// colby
//
// version 0.0.6
//

error_reporting(E_ALL | E_STRICT);

require_once(__DIR__ . '/../colby-configuration.php');
require_once(__DIR__ . '/classes/MDContainer.php');

class Colby
{
    private static $urlParser;

    /// <summary>
    ///
    /// </summary>
    public static function includeEqualizeStylesheet()
    {
        echo '<link rel="stylesheet" type="text/css" href="',
            COLBY_SITE_URL,
            'colby/css/equalize.css">';
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

    /// <summary>
    ///
    /// </summary>
    public static function useUser()
    {
        require_once(__DIR__ . '/classes/ColbyUser.php');
    }
}
