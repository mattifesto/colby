<?php

/**
 * Expected variables: $dataStoreID
 */

include_once CBSystemDirectory . '/classes/CBHTMLOutput.php';
include_once CBSystemDirectory . '/classes/CBDataStore.php';


if (defined('GOOGLE_UNIVERSAL_ANALYTICS_TRACKING_ID'))
{
    CBHTMLOutput::addJavaScriptSnippet(CBSystemDirectory . '/javascript/snippet-google-universal-analytics.php');
}


include_once Colby::findFile('page-renderer-configuration.php');


CBHTMLOutput::begin();


include CBSystemDirectory . '/sections/equalize.php';


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
