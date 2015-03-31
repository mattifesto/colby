<?php

/**
 * 2014.09.25 Version 2
 *
 *  Added the `created` and `updated` properties. When pages were first
 *  developed this information was provided by the `ColbyArchive` class. But
 *  `ColbyArchive` has been deprecated and for pages that don't use it, this
 *  information is not available.
 *
 *  The design of these properties requires that if the properties do not exist
 *  it should not cause an error. If they do not exist, it is okay for a process
 *  to set them to reasonable values if it needs them. It is okay to set the
 *  `created` property to the same value as the `updated` property if the
 *  `created` property is not yet set. There's no need to try to guess when the
 *  page was actually created if that information is not readily available.
 *
 * 2014.09.26 Version 3
 *
 *  Added the `listClassNames` property which holds an array of list class
 *  names representing the lists which include this page.
 */
final class CBViewPage {

    private static $renderModelContext;

    /**
     * @return void
     */
    private static function addToPageLists($model) {
        $pageRowID = (int)$model->rowID;

        if ($model->isPublished) {
            $publishedAsSQL = (int)$model->publicationTimeStamp;
            $yearMonthAsSQL = ColbyConvert::timestampToYearMonth($model->publicationTimeStamp);
            $yearMonthAsSQL = "'{$yearMonthAsSQL}'";
        } else {
            $publishedAsSQL = 'NULL';
            $yearMonthAsSQL = 'NULL';
        }

        $listClassNames = isset($model->listClassNames) ? $model->listClassNames : array();

        foreach ($listClassNames as $className) {
            $classNameAsSQL = ColbyConvert::textToSQL($className);
            $SQL            = <<<EOT

                INSERT INTO
                    `CBPageLists`
                SET
                    `pageRowID`     = {$pageRowID},
                    `listClassName` = '{$classNameAsSQL}',
                    `sort1`         = {$yearMonthAsSQL},
                    `sort2`         = {$publishedAsSQL}

EOT;

            Colby::query($SQL);
        }
    }

    /**
     * @return void
     */
    public static function addToRecentlyEditedPagesList($model) {
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

    /**
     * @return stdClass
     */
    public static function specToModel($specificationModel) {
        $s = $specificationModel;

        /**
         * Required values
         */

        $r              = new stdClass();
        $r->dataStoreID = $s->dataStoreID;
        $r->created     = $s->created;
        $r->updated     = $s->updated;

        /**
         * Optional values
         */

        $r->description             = isset($s->description) ? $s->description : '';
        $r->isPublished             = isset($s->isPublished) ? $s->isPublished : false;
        $r->iteration               = $s->iteration;
        $r->listClassNames          = isset($s->listClassNames) ? $s->listClassNames : array();
        $r->publicationTimeStamp    = isset($s->publicationTimeStamp) ? $s->publicationTimeStamp : null;
        $r->publishedBy             = isset($s->publishedBy) ? $s->publishedBy : null;
        $r->rowID                   = isset($s->rowID) ? $s->rowID : null; /* Deprecated */
        $r->schemaVersion           = isset($s->schemaVersion) ? $s->schemaVersion : null; /* Deprecated? */
        $r->thumbnailURL            = isset($s->thumbnailURL) ? $s->thumbnailURL : null;
        $r->title                   = isset($s->title) ? $s->title : '';

        /**
         * Views
         */

        if (isset($s->sections)) {
            $r->sections = array_map('CBView::specToModel', $s->sections);
        } else {
            $r->sections = array();
        }

        /**
         * Computed values
         */

        $r->descriptionHTML         = ColbyConvert::textToHTML($r->description);
        $r->thumbnailURLAsHTML      = ColbyConvert::textToHTML($r->thumbnailURL);
        $r->titleHTML               = ColbyConvert::textToHTML($r->title);

        /**
         * The URI and URIAsHTML values will be set in the save function
         * because the values are dependent on whether the URI is available or
         * not.
         */

        return $r;
    }

    /**
     * @return stdClass
     */
    private static function compileSpecificationModelToSummaryViewModel($model) {
        $summaryViewModel                       = CBPageSummaryView::specToModel();
        $summaryViewModel->created              = $model->created;
        $summaryViewModel->dataStoreID          = $model->dataStoreID;
        $summaryViewModel->description          = $model->description;
        $summaryViewModel->descriptionHTML      = $model->descriptionHTML;
        $summaryViewModel->isPublished          = $model->isPublished;
        $summaryViewModel->publicationTimeStamp = $model->publicationTimeStamp;
        $summaryViewModel->publishedBy          = $model->publishedBy;
        $summaryViewModel->thumbnailURL         = $model->thumbnailURL;
        $summaryViewModel->title                = $model->title;
        $summaryViewModel->titleHTML            = $model->titleHTML;
        $summaryViewModel->updated              = $model->updated;
        $summaryViewModel->URI                  = $model->URI;

        return $summaryViewModel;
    }

    /**
     * 2015.02.20
     * This function is being created as deprecated. The reason is that this
     * class is moving to a paradigm where the default model object is just an
     * empty stdClass. If a property doesn't exist, its value is considered to
     * be null-ish. For now, however, the code still expects all of these
     * properties to exist.
     *
     * @return stdClass
     */
    public static function createDefaultModel() {
        $model                          = new stdClass();
        $model->created                 = null;
        $model->dataStoreID             = null;
        $model->description             = '';
        $model->descriptionHTML         = '';
        $model->groupID                 = null;
        $model->isPublished             = false;
        $model->listClassNames          = array();
        $model->publicationTimeStamp    = null;
        $model->publishedBy             = null;
        $model->rowID                   = null;
        $model->schema                  = 'CBPage';
        $model->schemaVersion           = 3;
        $model->sections                = array();
        $model->thumbnailURL            = null;
        $model->title                   = '';
        $model->titleHTML               = '';
        $model->updated                 = null;
        $model->URI                     = null;
        $model->URIIsStatic             = false;

        return $model;
    }

    /**
     * This function removes the post from all editable page lists because
     * editable page lists are specified in `$specificationModel->listClassNames`.
     * This page may be in other page lists but those are managed by other
     * processes.
     *
     * @return void
     */
    private static function removeFromEditablePageLists($model) {
        $availableListNames     = CBViewPageLists::availableListNames();
        $listClassNames         = isset($model->listClassNames) ? $model->listClassNames : array();
        $listClassNames         = array_merge($availableListNames,
                                              $listClassNames,
                                              array('CBRecentlyEditedPages'));

        $listClassNames         = array_unique($listClassNames);
        $listClassNamesForSQL   = array();

        foreach ($listClassNames as $listClassName) {

            $classNameForSQL        = ColbyConvert::textToSQL($listClassName);
            $classNameForSQL        = "'{$classNameForSQL}'";
            $listClassNamesForSQL[] = $classNameForSQL;
        }

        $listClassNamesForSQL   = implode(',', $listClassNamesForSQL);
        $pageRowID              = (int)$model->rowID;
        $SQL                    = <<<EOT

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
    public static function renderAsHTMLForID($ID) {
        $dataStore              = new CBDataStore($ID);
        $renderModelFilepath    = $dataStore->directory() . '/render-model.json';

        if (!is_file($renderModelFilepath)) {
            $renderModelFilepath = $dataStore->directory() . '/model.json';
        }

        self::renderModelAsHTML(json_decode(file_get_contents($renderModelFilepath)));
    }

    /**
     * @return void
     */
    public static function renderModelAsHTML($model) {
        $model = self::upgradeRenderModel($model);

        self::$renderModelContext = $model;

        CBHTMLOutput::begin();

        include Colby::findFile('sections/public-page-settings.php');

        if (ColbyRequest::isForFrontPage()) {
            CBHTMLOutput::setTitleHTML(CBSiteNameHTML);
        } else {
            CBHTMLOutput::setTitleHTML($model->titleHTML);
        }

        CBHTMLOutput::setDescriptionHTML($model->descriptionHTML);

        foreach ($model->sections as $modelForView) {
            CBView::renderModelAsHTML($modelForView);
        }

        CBHTMLOutput::render();

        self::$renderModelContext = null;
    }

    /**
     * @return stdClass
     */
    public static function renderModelContext() {
        return self::$renderModelContext;
    }

    /**
     * This function updates the page data in the database and saves the page
     * model files.
     *
     * @return void
     */
    public static function save($specificationModel) {
        $ID = $specificationModel->dataStoreID;

        try {
            Colby::query('START TRANSACTION');

            if (isset($specificationModel->iteration)) {
                $data = CBPages::selectIterationAndURIForUpdate($ID);

                if ($data->iteration != $specificationModel->iteration) {
                    throw new RuntimeException('This page has been updated by another user.');
                }

                $specificationModel->iteration++;
            } else {
                $data                           = CBPages::insertRow($ID);
                $specificationModel->iteration  = $data->iteration;
                $specificationModel->rowID      = $data->rowID;
            }

            $renderModel = self::specToModel($specificationModel);

            if ($data->URI != $specificationModel->URI && CBPages::updateURI($ID, $specificationModel->URI)) {
                $renderModel->URI = $specificationModel->URI;
            } else {
                $renderModel->URI = $data->URI;
            }

            $renderModel->URIAsHTML = ColbyConvert::textToHTML($renderModel->URI);

            self::updateDatabase($renderModel);

            $dataStore              = new CBDataStore($ID);
            $specificationModelJSON = json_encode($specificationModel);
            $renderModelJSON        = json_encode($renderModel);

            $dataStore->makeDirectory();
            file_put_contents($dataStore->directory() . '/model.json', $specificationModelJSON, LOCK_EX);
            file_put_contents($dataStore->directory() . '/render-model.json', $renderModelJSON, LOCK_EX);
        } catch (Exception $exception) {
            Colby::query('ROLLBACK');

            throw $exception;
        }

        Colby::query('COMMIT');
    }

    /**
     * @return void
     */
    public static function saveEditedPageForAjax() {
        $response           = new CBAjaxResponse();
        $specificationModel = json_decode($_POST['model-json']);

        self::save($specificationModel);

        $response->rowID            = $specificationModel->rowID;
        $response->wasSuccessful    = true;
        $response->send();
    }

    /**
     * @return stdClass
     */
    public static function saveEditedPageForAjaxPermissions() {
        $permissions        = new stdClass();
        $permissions->group = 'Administrators';

        return $permissions;
    }

    /**
     * @return string
     */
    private static function modelToSearchText($model) {
        $searchText     = array();
        $searchText[]   = $model->title;
        $searchText[]   = $model->description;

        foreach ($model->sections as $modelForView) {
            $searchText[] = CBView::modelToSearchText($modelForView);
        }

        return implode(' ', $searchText);
    }

    /**
     * @return stdClass | false
     */
    public static function specificationModelWithID($ID) {
        $dataStore  = new CBDataStore($ID);
        $filepath   = $dataStore->directory() . '/model.json';

        if (is_file($filepath)) {
            $model = json_decode(file_get_contents($filepath));
        } else {
            return false;
        }

        if (!isset($model->iteration)) {
            $model->iteration = 1;
        }

        return $model;
    }

    /**
     * @return void
     */
    private static function updateDatabase($model) {

        $summaryViewModel       = self::compileSpecificationModelToSummaryViewModel($model);
        $rowData                = new stdClass();
        $rowData->className     = 'CBViewPage';
        $rowData->keyValueData  = json_encode($summaryViewModel);
        $rowData->rowID         = $model->rowID;
        $rowData->typeID        = null;
        $rowData->groupID       = null;
        $rowData->iteration     = $model->iteration;
        $rowData->titleHTML     = $model->titleHTML;
        $rowData->searchText    = self::modelToSearchText($model);
        $rowData->subtitleHTML  = $model->descriptionHTML;

        if ($model->isPublished)
        {
            $rowData->published             = $model->publicationTimeStamp;
            $rowData->publishedBy           = $model->publishedBy;
            $rowData->publishedYearMonth    = ColbyConvert::timestampToYearMonth($rowData->published);
        }
        else
        {
            $rowData->published             = null;
            $rowData->publishedBy           = null;
            $rowData->publishedYearMonth    = '';
        }

        CBPages::updateRow($rowData);

        self::removeFromEditablePageLists($model);

        if ($model->isPublished) {
            self::addToPageLists($model);
        }

        self::addToRecentlyEditedPagesList($model);
    }

    /**
     * @return void
     */
    private static function upgradeRenderModel($model) {

        /**
         * Version 2
         */

        if (!isset($model->updated)) {

            $model->updated = time();
        }

        if (!isset($model->created)) {

            $model->created = $model->updated;
        }

        /**
         * Version 3
         */

        if (!isset($model->listClassNames)) {

            $model->listClassNames = array();
        }

        $model->schemaVersion = 3;

        return $model;
    }
}
