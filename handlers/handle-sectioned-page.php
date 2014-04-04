<?php

/**
 * Expected variables: $dataStoreID
 */

include_once CBSystemDirectory . '/classes/CBHTMLOutput.php';
include_once CBSystemDirectory . '/classes/CBDataStore.php';
include_once Colby::findFile('page-renderer-configuration.php');


CBHTMLOutput::begin();

include Colby::findFile('sections/public-page-settings.php');


$dataStore      = new CBDataStore($dataStoreID);
$pageModelJSON  = file_get_contents($dataStore->directory() . '/model.json');
$pageModel      = json_decode($pageModelJSON);

if (ColbyRequest::isForFrontPage())
{
    CBHTMLOutput::setTitleHTML(CBSiteNameHTML);
}
else
{
    CBHTMLOutput::setTitleHTML($pageModel->titleHTML);
}

CBHTMLOutput::setDescriptionHTML($pageModel->descriptionHTML);


CBSectionedPageRenderSections($pageModel->sections, $pageModel);


CBHTMLOutput::render();


/**
 * @return void
 */
function CBSectionedPageRenderSections($sections, $pageModel)
{
    global $CBSections;

    foreach ($sections as $sectionModel)
    {
        include $CBSections[$sectionModel->sectionTypeID]->snippetForHTML;
    }
}
