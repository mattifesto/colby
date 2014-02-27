<?php

/**
 * Expected variables: $dataStoreID
 */

include_once CBSystemDirectory . '/classes/CBHTMLOutput.php';
include_once CBSystemDirectory . '/classes/CBDataStore.php';


CBHTMLOutput::addCSSURL(CBSystemURL . '/css/equalize.css');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/html5shiv.js');
CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/ColbyEqualize.js');

if (defined('GOOGLE_UNIVERSAL_ANALYTICS_TRACKING_ID'))
{
    CBHTMLOutput::addJavaScriptSnippet(CBSystemDirectory . '/javascript/snippet-google-universal-analytics.php');
}

include_once Colby::findFile('page-renderer-configuration.php');


CBHTMLOutput::begin();


$dataStore      = new CBDataStore($dataStoreID);
$pageModelJSON  = file_get_contents($dataStore->directory() . '/model.json');
$pageModel      = json_decode($pageModelJSON);

CBHTMLOutput::setTitleHTML($pageModel->titleHTML);
CBHTMLOutput::setDescriptionHTML($pageModel->descriptionHTML);


CBSectionedPageRenderSections($pageModel->sections, $pageModel);


CBHTMLOutput::render();


/**
 * @return void
 */
function CBSectionedPageRenderSections($sections, $pageModel)
{
    global $CBSectionSnippets;

    foreach ($sections as $sectionModel)
    {
        include $CBSectionSnippets[$sectionModel->sectionTypeID];
    }
}
