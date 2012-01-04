<?php

//
// colby
//
// version 0.0.2
//

require_once(__DIR__ . '/classes/MDContainer.php');

class Colby
{
    private static $siteUrl;

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
    public static function setSiteUrl($siteUrl)
    {
        Colby::$siteUrl = $siteUrl;
    }
}
