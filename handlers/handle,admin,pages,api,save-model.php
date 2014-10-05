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

$modelJSON      = $_POST['model-json'];
$model          = json_decode($modelJSON);

CBPageTemplate::upgradeModel($model);

$summaryView    = CBPageCreatePageSummaryViewWithPageModel($model);

/**
 *
 */

$rowData                = new stdClass();
$rowData->keyValueData  = json_encode($summaryView->model());
$rowData->rowID         = (int)$model->rowID;
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

CBPageRemoveFromEditablePageLists($model);
CBPageAddToPageLists($model);
CBPageUpdateRecentlyEditedPagesList($model);

/**
 * Re-encode the model to JSON in case any changes have been made and then save.
 */

$modelJSON          = json_encode($model);
$dataStore          = new CBDataStore($model->dataStoreID);
$dataStoreDirectory = $dataStore->directory();

file_put_contents("{$dataStoreDirectory}/model.json", $modelJSON, LOCK_EX);


/**
 * Send the response
 */

$response->wasSuccessful = true;

$response->send();


/**
 * The properties are currently set directly on the model. Eventually the
 * summary view will have an API.
 *
 * @return CBPageSummaryView
 */
function CBPageCreatePageSummaryViewWithPageModel($pageModel)
{
    $summaryView                            = CBPageSummaryView::init();
    $summaryViewModel                       = $summaryView->model();

    $summaryViewModel->created              = $pageModel->created;
    $summaryViewModel->dataStoreID          = $pageModel->dataStoreID;
    $summaryViewModel->description          = $pageModel->description;
    $summaryViewModel->descriptionHTML      = $pageModel->descriptionHTML;
    $summaryViewModel->isPublished          = $pageModel->isPublished;
    $summaryViewModel->publicationTimeStamp = $pageModel->publicationTimeStamp;
    $summaryViewModel->publishedBy          = $pageModel->publishedBy;
    $summaryViewModel->thumbnailURL         = $pageModel->thumbnailURL;
    $summaryViewModel->title                = $pageModel->title;
    $summaryViewModel->titleHTML            = $pageModel->titleHTML;
    $summaryViewModel->updated              = $pageModel->updated;
    $summaryViewModel->URI                  = $pageModel->URI;

    return $summaryView;
}

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
function CBPageAddToPageLists($model) {

    $pageRowID  = (int)$model->rowID;
    $updated    = (int)$model->updated;
    $yearMonth  = gmdate('Ym', $updated);

    foreach ($model->listClassNames as $className) {

        $classNameForSQL    = ColbyConvert::textToSQL($className);
        $SQL                = <<<EOT

            INSERT INTO
                `CBPageLists`
            SET
                `pageRowID`     = {$pageRowID},
                `listClassName` = '{$classNameForSQL}',
                `sort1`         = {$yearMonth},
                `sort2`         = {$updated}

EOT;

        Colby::query($SQL);
    }
}

/**
 * This removes the page from all page lists that this handler is capable of
 * adding it to. This is because instead of doing a "add list if not already
 * added" it's more efficient to remove from any possible lists and then add
 * all the relevant ones back.
 *
 * This also solves a problem that could occur where a list may be removed from
 * the model, but not the database. This would be rare, but if it were to occur
 * the page would never be removed from that list. This way after executing this
 * function and then refreshing the lists, the lists are guaranteed to be
 * synchronized.
 *
 * This process does not remove the post from non-editable lists so any
 * processes that use custom lists will not be affected.
 *
 * @return void
 */
function CBPageRemoveFromEditablePageLists($model) {

    global $CBPageEditorAvailablePageListClassNames;

    $listClassNames         = array_merge($CBPageEditorAvailablePageListClassNames,
                                          $model->listClassNames,
                                          ['CBRecentlyEditedPages']);

    $listClassNames         = array_unique($listClassNames);
    $listClassNamesForSQL   = array();

    foreach ($listClassNames as $listClassName) {

        $classNameForSQL        = ColbyConvert::textToSQL($listClassName);
        $classNameForSQL        = "'{$classNameForSQL}'";
        $listClassNamesForSQL[] = $classNameForSQL;
    }

    $listClassNamesForSQL   = implode(',', $listClassNamesForSQL);
    $pageRowID      = (int)$model->rowID;
    $SQL            = <<<EOT

        DELETE FROM
            `CBPageLists`
        WHERE
            `pageRowID` = {$pageRowID} AND
            `listClassName` IN ({$listClassNamesForSQL})

EOT;

    Colby::query($SQL);
}

/**
 * @return void
 */
function CBPageUpdateRecentlyEditedPagesList($model) {

    /**
     * 2014.09.25
     *  The recently edited pages list used to be stored in a dictionary tuple
     * but this wasn't properly updated when pages were deleted. The page list
     * functionality now better supports this feature.
     *
     * Remove this line after all sites are updated.
     */

    CBDictionaryTuple::deleteForKey('CBRecentlyEditedPages');

    $pageRowID  = (int)$model->rowID;
    $updated    = (int)$model->updated;
    $SQL        = <<<EOT

        INSERT INTO
            `CBPageLists`
        SET
            `pageRowID`     = {$pageRowID},
            `listClassName` = 'CBRecentlyEditedPages',
            `sort1`         = {$updated},
            `sort2`         = NULL

EOT;

    Colby::query($SQL);
}
