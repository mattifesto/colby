<?php

//
// colby
//
// version 0.0.3
//

require_once(__DIR__ . '/classes/MDContainer.php');

class Colby
{
    private static $sitePath;
    private static $siteUrl;

    private static $urlParser;

    /// <summary>
    ///
    /// </summary>
    public static function includeEqualizeStylesheet()
    {
        echo '<link rel="stylesheet" type="text/css" href="',
            Colby::$siteUrl,
            'colby/css/equalize.css">';
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
    public static function setSiteUrl($siteUrl)
    {
        Colby::$siteUrl = $siteUrl;
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
