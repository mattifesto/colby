<?php

include_once CBSystemDirectory . '/classes/CBDataStore.php';
include_once CBSystemDirectory . '/classes/CBPages.php';
include_once Colby::findFile('page-renderer-configuration.php');


$response = new ColbyOutputManager('ajax-response');

$response->begin();

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    $response->message = 'You are not authorized to use this feature.';

    goto done;
}

/**
 *
 */

$modelJSON  = $_POST['model-json'];
$model      = json_decode($modelJSON);


/**
 *
 */

$rowData                = new stdClass();
$rowData->rowID         = $model->rowID;
$rowData->typeID        = CBPageTypeID;
$rowData->groupID       = $model->groupID;
$rowData->titleHTML     = $model->titleHTML;
$rowData->searchText    = CBPageGenerateSearchText($model);
$rowData->subtitleHTML  = $model->descriptionHTML;

if ($model->isPublished)
{
    $rowData->published             = $model->publicationTimeStamp;
    $rowData->publishedBy           = $model->publishedBy;
    $rowData->publishedYearMonth    = ColbyConvert::timestampToYearMonth($model->publicationTimeStamp);
}
else
{
    $rowData->published             = null;
    $rowData->publishedBy           = null;
    $rowData->publishedYearMonth    = '';
}

CBPages::updateRow($rowData);


/**
 *
 */

$dataStore          = new CBDataStore($model->dataStoreID);
$dataStoreDirectory = $dataStore->directory();

file_put_contents("{$dataStoreDirectory}/model.json", $modelJSON, LOCK_EX);


/**
 * Send the response
 */

$response->wasSuccessful = true;

done:

$response->end();


/**
 * @return string
 */
function CBPageGenerateSearchText($pageModel)
{
    $searchText[] = $pageModel->title;
    $searchText[] = $pageModel->description;

    foreach ($pageModel->sections as $sectionModel)
    {
        $searchText[] = CBPageGenerateSectionSearchText($pageModel, $sectionModel);
    }

    return implode(' ', $searchText);
}

/**
 * This function exists so that the included file will only have access to the
 * `$pageModel` and `$sectionModel` variables.
 *
 * return string
 */
function CBPageGenerateSectionSearchText($pageModel, $sectionModel)
{
    global $CBSectionSnippetsForSearchText;

    $text = null;


    if (isset($sectionModel->sectionTypeID) &&
        isset($CBSectionSnippetsForSearchText[$sectionModel->sectionTypeID]))
    {
        ob_start();

        include $CBSectionSnippetsForSearchText[$sectionModel->sectionTypeID];

        $text = ob_get_clean();
    }
    else
    {
        $model          = $sectionModel;
        $viewClassName  = $model->className;
        $view           = $viewClassName::initWithModel($model);

        $text = $view->searchText();
    }

    return $text;
}
