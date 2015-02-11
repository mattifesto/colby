<?php

if (!ColbyUser::current()->isOneOfThe('Administrators'))
{
    return include CBSystemDirectory . '/handlers/handle-authorization-failed-ajax.php';
}

$response       = new CBAjaxResponse();
$modelJSON      = $_POST['model-json'];
$model          = json_decode($modelJSON);
$page           = CBViewPage::initWithModel($model);

$page->save();

CBPageUpdateRecentlyEditedPagesList($model);

$response->wasSuccessful = true;
$response->send();

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
