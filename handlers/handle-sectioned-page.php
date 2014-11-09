<?php

/**
 * Expected variables: $dataStoreID
 */

include_once Colby::findFile('page-renderer-configuration.php');


CBHTMLOutput::begin();

include Colby::findFile('sections/public-page-settings.php');

$viewPage       = CBViewPage::initWithID($dataStoreID);
$pageModel      = $viewPage->model();

/**
 * At some point in the future we will have an official way of getting the
 * page object from a "page context" and the page model will not be passed
 * throughout the rendering calls. As a temporary workaround while making
 * changes this variable is made available globally.
 */

global $CBHackSectionedPagesPageModel;
$CBHackSectionedPagesPageModel = $pageModel;

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
        if (isset($sectionModel->className))
        {
            $model          = $sectionModel;
            $viewClassName  = $model->className;
            $view           = $viewClassName::initWithModel($model);

            $view->renderHTML();
        }
        else if (isset($sectionModel->sectionTypeID) &&
                 isset($CBSections[$sectionModel->sectionTypeID]) &&
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
