<?php

include CBSystemDirectory . '/sections/equalize.php';

$publicPageSettingsCSSURL = Colby::findFile('sections/public-page-settings.css', Colby::returnURL);

CBHTMLOutput::addCSSURL($publicPageSettingsCSSURL);
CBHTMLOutput::addCSSURL('https://fonts.googleapis.com/css?family=Source+Sans+Pro:400');
CBHTMLOutput::addCSSURL('https://fonts.googleapis.com/css?family=Source+Sans+Pro:700');

CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/Colby.js');

if (defined('GOOGLE_UNIVERSAL_ANALYTICS_TRACKING_ID'))
{
    CBHTMLOutput::addJavaScriptSnippet(CBSystemDirectory . '/javascript/snippet-google-universal-analytics.php');
}
