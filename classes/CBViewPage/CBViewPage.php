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

    private static $modelContext;

    /**
     * @return void
     */
    private static function addToPageLists($model, $pageRowID) {
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
    public static function addToRecentlyEditedPagesList($model, $pageRowID) {
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
     * @return {int} | false
     */
    public static function iterationForID($args) {
        $ID = null;
        extract($args, EXTR_IF_EXISTS);

        $IDAsSQL    = ColbyConvert::textToSQL($ID);
        $SQL        = "SELECT `iteration` FROM `ColbyPages` WHERE `archiveID` = UNHEX('{$IDAsSQL}')";
        $result     = Colby::query($SQL);

        if ($row = $result->fetch_object()) {
            $iteration = $row->iteration;
        } else {
            $iteration = false;
        }

        $result->free();

        return $iteration;
    }

    /**
     * Returns either the lastest spec or a new spec for a page ID.
     *
     * TODO: Consider adding a "clobber" argument to specify that the function
     * should return a spec with only the minimal necessary data to allow it to
     * save. This would be used by callers that want to drop all of the current
     * spec data and restart fresh. It would save the time of a file load
     * and also prevent old data from accumulating over time for pages that have
     * a longer life and are frequently modified.
     *
     * @return {stdClass}
     */
    public static function makeSpecForID($args) {
        $ID = null;
        extract($args, EXTR_IF_EXISTS);

        $iteration  = CBViewPage::iterationForID(['ID' => $ID]);
        $spec       = CBViewPage::specWithID($ID, $iteration);

        if (!$spec) {
            $spec               = CBView::modelWithClassName(__CLASS__);
            $spec->dataStoreID  = $ID;
            $spec->created      = time();
            $spec->updated      = $spec->created;

            /**
             * This is for the case where a page exists but is not currently
             * a view page.
             */
            if ($iteration) {
                $spec->iteration = (int)$iteration;
            }
        }

        return $spec;
    }

    /**
     * @return {stdClass} | null
     */
    public static function modelContext() {
        return self::$modelContext;
    }

    /**
     * @return string
     */
    private static function modelToSearchText($model) {
        $searchText         = array();
        $searchText[]       = $model->title;
        $searchText[]       = $model->description;
        self::$modelContext = $model;

        foreach ($model->sections as $modelForView) {
            $searchText[] = CBView::modelToSearchText($modelForView);
        }

        self::$modelContext = null;

        return implode(' ', $searchText);
    }

    /**
     * This function removes the post from all editable page lists because
     * editable page lists are specified in `$spec->listClassNames`.
     * This page may be in other page lists but those are managed by other
     * processes.
     *
     * @return void
     */
    private static function removeFromEditablePageLists($model, $pageRowID) {
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
        $SQL                    = <<<EOT

            DELETE FROM `CBPageLists`
            WHERE       `pageRowID` = {$pageRowID} AND
                        `listClassName` IN ({$listClassNamesForSQL})

EOT;

        Colby::query($SQL);
    }

    /**
     * @return void
     */
    public static function renderAsHTMLForID($ID, $iteration) {
        $directory  = CBDataStore::directoryForID($ID);

        if (is_file($filepath = "{$directory}/model-{$iteration}.json")) {

            // Pages edited after installing version 137 of Colby will have a
            // model file saved for each iteration.

        } else if (is_file($filepath = "{$directory}/render-model.json")) {

            // If this file exists it means that this page was editing during
            // a small window of time in which this was the name of the model
            // file. Whenever a page is edited this file is deleted, but if
            // the page hasn't been edited since that time it will still exist.

        } else {

            // If neither of the two previous files exist the name of the
            // model file will be "model.json". TODO: It would be nice to
            // create an update that would go through every view page and
            // canonicalize this filename so that these final two conditions
            // can be removed.

            $filepath = "{$directory}/model.json";
        }

        self::renderModelAsHTML(json_decode(file_get_contents($filepath)));
    }

    /**
     * @return void
     */
    public static function renderModelAsHTML($model) {
        $model = self::upgradeRenderModel($model);

        self::$modelContext = $model;

        CBHTMLOutput::begin();

        include Colby::findFile('sections/public-page-settings.php');

        if (ColbyRequest::isForFrontPage()) {
            CBHTMLOutput::setTitleHTML(CBSiteNameHTML);
        } else {
            CBHTMLOutput::setTitleHTML($model->titleHTML);
        }

        CBHTMLOutput::setDescriptionHTML($model->descriptionHTML);

        $dependencyIDs = array_reduce($model->sections, function($carry, $viewModel) {
            $carry = array_merge($carry, CBView::modelToModelDependencies($viewModel));
            return $carry;
        }, []);

        CBModelCache::cacheModelsByID($dependencyIDs);

        array_walk($model->sections, 'CBView::renderModelAsHTML');

        CBHTMLOutput::render();

        self::$modelContext = null;
    }

    /**
     * @deprecated since version 147. Use `modelContext` instead.
     * @return stdClass
     */
    public static function renderModelContext() {
        return self::$modelContext;
    }

    /**
     * This function updates the page data in the database and saves the page
     * model files.
     *
     * @return void
     */
    public static function save($args) {
        $spec = $updatePageLists = false;
        extract($args, EXTR_IF_EXISTS);

        $ID = $spec->dataStoreID;

        if (!CBHex160::is($ID)) {
            throw new InvalidArgumentException("The `spec` argument contains an invalid ID: {$ID}");
        }

        try {
            Colby::query('START TRANSACTION');

            if (isset($spec->iteration)) {
                $iteration = CBPages::fetchIterationForUpdate($ID);

                if ($iteration != $spec->iteration) {
                    throw new RuntimeException('This page has been updated by another user.');
                }

                $spec->iteration++;
            } else {
                CBPages::insertRow($ID);
                $spec->iteration = 1;
            }

            $iteration  = $spec->iteration;
            $model      = self::specToModel($spec);

            if ($model->isPublished) {
                $preferredURI = isset($spec->URI) ? $spec->URI : $ID;
            } else {
                $preferredURI = null;
            }

            $actualURIs         = CBPages::updateURIs(['preferredURIs' => [$ID => $preferredURI]]);
            $model->URI         = $actualURIs[$ID];
            $model->URIAsHTML   = $model->URI === null ? null : ColbyConvert::textToHTML($model->URI);

            self::updateDatabase([
                'model'             => $model,
                'updatePageLists'   => $updatePageLists]);

            $directory  = CBDataStore::directoryForID($ID);
            $specJSON   = json_encode($spec);
            $modelJSON  = json_encode($model);

            CBDataStore::makeDirectoryForID($ID);
            file_put_contents("{$directory}/spec-{$iteration}.json", $specJSON, LOCK_EX);
            file_put_contents("{$directory}/model-{$iteration}.json", $modelJSON, LOCK_EX);

            /**
             * If the spec and model for the last iteration were saved less
             * than 30 seconds ago, remove them. We don't need every iteration
             * archived, just the ones before a long pause in editing.
             */

            $previous = $iteration - 1;

            if (is_file($filepath = "{$directory}/spec-{$previous}.json") &&
                    time() - filemtime($filepath) < /* 2 minutes: */ (2 * 60)) {
                unlink($filepath);
                unlink("{$directory}/model-{$previous}.json");
            }

            /**
             * Remove deprecated files.
             */

            if (is_file($filepath = "{$directory}/render-model.json")) {
                unlink($filepath);
            }

            if (is_file($filepath = "{$directory}/model.json")) {
                unlink($filepath);
            }
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
        $spec = json_decode($_POST['model-json']);

        self::save([
            'spec'              => $spec,
            'updatePageLists'   => true]);

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
     * @return {stdClass}
     */
    public static function specToModel($spec) {
        $model              = CBView::modelWithClassName(__CLASS__);
        $model->dataStoreID = $spec->dataStoreID;
        $time               = time();

        /**
         * Optional values
         */

        $model->classNameForKind        = isset($spec->classNameForKind) ? $spec->classNameForKind : null;
        $model->created                 = isset($spec->created) ? $spec->created : $time;
        $model->description             = isset($spec->description) ? $spec->description : '';
        $model->isPublished             = isset($spec->isPublished) ? !!$spec->isPublished : false;
        $model->iteration               = $spec->iteration;
        $model->listClassNames          = isset($spec->listClassNames) ? $spec->listClassNames : array();
        $model->publicationTimeStamp    = isset($spec->publicationTimeStamp) ? (int)$spec->publicationTimeStamp : ($model->isPublished ? $time : null);
        $model->publishedBy             = isset($spec->publishedBy) ? $spec->publishedBy : null;
        $model->schemaVersion           = isset($spec->schemaVersion) ? $spec->schemaVersion : null; /* Deprecated? */
        $model->thumbnailURL            = isset($spec->thumbnailURL) ? $spec->thumbnailURL : null;
        $model->title                   = isset($spec->title) ? $spec->title : '';
        $model->updated                 = isset($spec->updated) ? $spec->updated : $model->created;

        /**
         * Views
         */

        if (isset($spec->sections)) {
            $model->sections = array_map('CBView::specToModel', $spec->sections);
        } else {
            $model->sections = array();
        }

        /**
         * Computed values
         */

        $model->descriptionHTML         = ColbyConvert::textToHTML($model->description);
        $model->thumbnailURLAsHTML      = ColbyConvert::textToHTML($model->thumbnailURL);
        $model->titleHTML               = ColbyConvert::textToHTML($model->title);

        /**
         * The URI and URIAsHTML values will be set in the save function
         * because the values are dependent on whether the URI is available or
         * not.
         */

        return $model;
    }

    /**
     * @return stdClass | false
     */
    public static function specWithID($ID, $iteration) {
        $directory  = CBDataStore::directoryForID($ID);

        if (is_file($filepath = "{$directory}/spec-{$iteration}.json")) {

            // Pages edited after installing version 137 of Colby will have a
            // spec file saved for each iteration.

        } else if (is_file($filepath = "{$directory}/spec.json")) {

            // Pages edited for a brief time before version 137 will have a
            // spec file with this name.

        } else if (is_file($filepath = "{$directory}/model.json")) {

            // Pages that were last edited before the spec/model split use the
            // model file as the spec file. TODO: It would be nice to
            // create an update that would go through every view page and
            // canonicalize this filename so that these final two conditions
            // can be removed.

        } else {
            return false;
        }

        $spec = json_decode(file_get_contents($filepath));

        if (!isset($spec->iteration)) {
            $spec->iteration = 1;
        }

        return $spec;
    }

    /**
     * @return void
     */
    private static function updateDatabase($args) {
        $model = $updatePageLists = false;
        extract($args, EXTR_IF_EXISTS);

        $summaryViewModel           = self::compileSpecificationModelToSummaryViewModel($model);
        $rowData                    = new stdClass();
        $rowData->className         = 'CBViewPage';
        $rowData->classNameForKind  = $model->classNameForKind;
        $rowData->ID                = $model->dataStoreID;
        $rowData->keyValueData      = json_encode($summaryViewModel);
        $rowData->typeID            = null;
        $rowData->groupID           = null;
        $rowData->iteration         = $model->iteration;
        $rowData->titleHTML         = $model->titleHTML;
        $rowData->searchText        = self::modelToSearchText($model);
        $rowData->subtitleHTML      = $model->descriptionHTML;
        $rowData->thumbnailURL      = $model->thumbnailURLAsHTML;

        if ($model->isPublished) {
            $rowData->published         = $model->publicationTimeStamp;
            $rowData->publishedBy       = $model->publishedBy;
            $rowData->publishedMonth    = (int)ColbyConvert::timestampToYearMonth($rowData->published);
        } else {
            $rowData->published         = null;
            $rowData->publishedBy       = null;
            $rowData->publishedMonth    = null;
        }

        CBPages::updateRow($rowData);

        if ($updatePageLists === true) {
            $IDAsSQL    = CBHex160::toSQL($model->dataStoreID);
            $pageRowID  = CBDB::SQLToValue("SELECT `ID` FROM `ColbyPages` WHERE `archiveID` = {$IDAsSQL}");

            self::removeFromEditablePageLists($model, $pageRowID);

            if ($model->isPublished) {
                self::addToPageLists($model, $pageRowID);
            }

            self::addToRecentlyEditedPagesList($model, $pageRowID);
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
