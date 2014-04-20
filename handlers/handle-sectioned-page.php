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
        if (isset($CBSections[$sectionModel->sectionTypeID]) &&
            $CBSections[$sectionModel->sectionTypeID]->snippetForHTML)
        {
            include $CBSections[$sectionModel->sectionTypeID]->snippetForHTML;
        }
        else
        {
            $schema         = isset($sectionModel->schema) ?
                                $sectionModel->schema : '<no schema>';
            $sectionTypeID  = isset($sectionModel->sectionTypeID) ?
                                $sectionModel->sectionTypeID : '<no section type ID>';

            echo "\n\n",
                 "<!-- There is no HTML snippet available to display this section.\n",
                 "     schema:          {$schema}\n",
                 "     section type ID: {$sectionTypeID} -->\n\n";
        }
    }
}
