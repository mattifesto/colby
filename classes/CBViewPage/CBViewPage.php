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
    public static function compileSpecificationModelToRenderModel($specificationModel) {

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
            $r->sections = array_map(function($vs) {
                return CBView::compileSpecificationModelToRenderModel($vs);
            }, $s->sections);
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
         * 2015.02.20 TODO
         * URI should be updated in the ColbyPages table during this process.
         * I'm not sure if this is the place to do it, but the render model URI
         * should be the actual confirmed URI while the specification model URI
         * is the desired URI which may not become the actual URI if it is
         * already in use.
         */

        $r->URI                     = $s->URI;
        $r->URIAsHTML               = ColbyConvert::textToHTML($r->URI);

        return $r;
    }

    /**
     * @return stdClass
     */
    private static function compileSpecificationModelToSummaryViewModel($model) {
        $summaryView                            = CBPageSummaryView::init();
        $summaryViewModel                       = $summaryView->model();
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

        global $CBPageEditorAvailablePageListClassNames;

        $listClassNames         = array_merge($CBPageEditorAvailablePageListClassNames,
                                              $model->listClassNames,
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
    public static function renderAsHTML($renderModel) {
        $renderModel = self::upgradeRenderModel($renderModel);

        self::$renderModelContext = $renderModel;

        CBHTMLOutput::begin();

        include Colby::findFile('sections/public-page-settings.php');

        if (ColbyRequest::isForFrontPage()) {
            CBHTMLOutput::setTitleHTML(CBSiteNameHTML);
        } else {
            CBHTMLOutput::setTitleHTML($renderModel->titleHTML);
        }

        CBHTMLOutput::setDescriptionHTML($renderModel->descriptionHTML);

        foreach ($renderModel->sections as $viewRenderModel) {
            CBView::renderAsHTMLForRenderModel($viewRenderModel);
        }

        CBHTMLOutput::render();

        self::$renderModelContext = null;
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

        self::renderAsHTML(json_decode(file_get_contents($renderModelFilepath)));
    }

    /**
     * @return stdClass
     */
    public static function renderModelContext() {
        return self::$renderModelContext;
    }

    /**
     * This function updates the page data in the database and saves the page
     * model file.
     *
     * If the page happens to be in the trash, it will be moved out of the
     * trash. Saving pages in the trash is a contradictory behavior, so if a
     * page is in the trash and belongs in the trash, don't save it unless you
     * also want to move it out of the trash.
     *
     * @return void
     */
    public static function save($specificationModel) {

        try {

            $dataStoreID    = $specificationModel->dataStoreID;
            $mysqli         = Colby::mysqli();

            Colby::query('START TRANSACTION');

            if (!$specificationModel->rowID) {

                CBPages::deleteRowWithDataStoreID($dataStoreID);
                CBPages::deleteRowWithDataStoreIDFromTheTrash($dataStoreID);

                $rowData                    = CBPages::insertRow($dataStoreID);
                $specificationModel->rowID  = $rowData->rowID;
            }

            /**
             * 2015.02.20 TODO
             * We either need to pass the render model or do a lot more work in
             * updateDatabase because the specification model is no longer
             * guaranteed to have the values updateDatabase expects.
             */

            self::updateDatabase($specificationModel);

            $dataStore  = new CBDataStore($dataStoreID);

            $specificationModelJSON = json_encode($specificationModel);
            file_put_contents($dataStore->directory() . '/model.json', $specificationModelJSON, LOCK_EX);

            $renderModelJSON = json_encode(self::compileSpecificationModelToRenderModel($specificationModel));
            file_put_contents($dataStore->directory() . '/render-model.json', $renderModelJSON, LOCK_EX);

            self::addToRecentlyEditedPagesList($specificationModel);

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

        $response->wasSuccessful = true;
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
    private static function searchText($specificationModel) {
        $searchText     = array();
        $searchText[]   = $specificationModel->title;
        $searchText[]   = $specificationModel->description;

        foreach ($specificationModel->sections as $viewSpecificationModel) {
            $searchText[] = CBView::searchTextForSpecificationModel($viewSpecificationModel);
        }

        return implode(' ', $searchText);
    }

    /**
     * @return stdClass
     */
    public static function specificationModelWithID($ID) {
        $dataStore  = new CBDataStore($ID);
        $filepath   = $dataStore->directory() . '/model.json';

        return json_decode(file_get_contents($filepath));
    }

    /**
     * @return void
     */
    private static function updateDatabase($specificationModel) {

        $summaryViewModel       = self::compileSpecificationModelToSummaryViewModel($specificationModel);
        $rowData                = new stdClass();
        $rowData->className     = 'CBViewPage';
        $rowData->keyValueData  = json_encode($summaryViewModel);
        $rowData->rowID         = $specificationModel->rowID;
        $rowData->typeID        = null;
        $rowData->groupID       = $specificationModel->groupID;
        $rowData->titleHTML     = $specificationModel->titleHTML;
        $rowData->searchText    = self::searchText($specificationModel);
        $rowData->subtitleHTML  = $specificationModel->descriptionHTML;

        if ($specificationModel->isPublished)
        {
            $rowData->published             = $specificationModel->publicationTimeStamp;
            $rowData->publishedBy           = $specificationModel->publishedBy;
            $rowData->publishedYearMonth    = ColbyConvert::timestampToYearMonth($rowData->published);
        }
        else
        {
            $rowData->published             = null;
            $rowData->publishedBy           = null;
            $rowData->publishedYearMonth    = '';
        }

        CBPages::updateRow($rowData);

        self::removeFromEditablePageLists($specificationModel);

        if ($specificationModel->isPublished) {

            self::addToPageLists($specificationModel);
        }
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
