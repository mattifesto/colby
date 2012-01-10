<?php

//
// colby
//
// version 0.0.5
//

error_reporting(E_ALL | E_STRICT);

require_once(__DIR__ . '/classes/MDContainer.php');

class Colby
{
    private static $sitePath;
    private static $siteURL;

    private static $urlParser;

    /// <summary>
    ///
    /// </summary>
    public static function includeEqualizeStylesheet()
    {
        echo '<link rel="stylesheet" type="text/css" href="',
            Colby::$siteURL,
            'colby/css/equalize.css">';
    }

    /// <summary>
    ///
    /// </summary>
    public static function sitePath()
    {
        return Colby::$sitePath;
    }

    /// <summary>
    ///
    /// </summary>
    public static function setSitePath($sitePath)
    {
        Colby::$sitePath = $sitePath;
    }

    /// <summary>
    ///
    /// </summary>
    public static function siteURL()
    {
        return Colby::$siteURL;
    }

    /// <summary>
    ///
    /// </summary>
    public static function setSiteURL($siteURL)
    {
        Colby::$siteURL = $siteURL;
    }

    /// <summary>
    ///
    /// </summary>
    public static function urlParser()
    {
        require_once(self::$sitePath . '/colby/classes/MDURLParser.php');

        if (!isset($urlParser))
        {
            self::$urlParser = new MDURLParser();
        }

        return self::$urlParser;
    }
}
