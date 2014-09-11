<?php

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed-ajax.php';
}

$response = new CBAjaxResponse();

include_once Colby::findFile('page-renderer-configuration.php');

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

CBPageUpdateRecentlyEditedPages($model);

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

$response->send();


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
    else if (isset($sectionModel->className))
    {
        $model          = $sectionModel;
        $viewClassName  = $model->className;
        $view           = $viewClassName::initWithModel($model);

        $text = $view->searchText();
    }

    return $text;
}

/**
 * @return void
 */
function CBPageUpdateRecentlyEditedPages($model) {

    $hasUpdated = false;

    while (!$hasUpdated) {

        $tuple = CBDictionaryTuple::initWithKey('CBRecentlyEditedPages');

        if ($tuple->value) {

            $pages = $tuple->value;

        } else {

            $pages = array();
        }

        $tuple->value = CBPageUpdateRecentlyEditedPagesArray($pages, $model);

        $hasUpdated = $tuple->updateForNumber();
    }
}

/**
 * @return array
 */
function CBPageUpdateRecentlyEditedPagesArray($pages, $model) {

    foreach ($pages as $index => $page) {

        if ($page->dataStoreID == $model->dataStoreID) {

            unset($pages[$index]);

            break;
        }
    }

    $page = new stdClass();
    $page->dataStoreID  = $model->dataStoreID;
    $page->published    = $model->publicationTimeStamp;
    $page->title        = $model->title;
    $page->updated      = time();

    array_unshift($pages, $page);

    return array_values($pages);
}
