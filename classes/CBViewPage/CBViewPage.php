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
     * @return {stdClass}
     */
    private static function modelToSummaryViewModel($model) {
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
     * @return null
     */
    public static function fetchRecentlyEditedPagesListForAjax() {
        $response = new CBAjaxResponse();
        $SQL = <<<EOT

            SELECT      HEX(`page`.`archiveID`), `page`.`keyValueData`
            FROM        `CBPageLists` AS `list`
            LEFT JOIN   `ColbyPages` AS `page`
            ON          `page`.`ID` = `list`.`pageRowID`
            WHERE       `list`.`listClassName`  = 'CBRecentlyEditedPages'
            ORDER BY    `sort1` DESC

EOT;

        $response->pages = CBDB::SQLToArray($SQL, ['valueIsJSON' => true]);
        $response->pages = CBViewPage::fixKeyValueDataArray($response->pages);
        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return {stdClass}
     */
    public static function fetchRecentlyEditedPagesListForAjaxPermissions() {
        $permissions        = new stdClass();
        $permissions->group = 'Administrators';

        return $permissions;
    }

    /**
     * @return null
     */
    public static function fetchSpecForAjax() {
        $response = new CBAjaxResponse();
        $ID = $_POST['id'];
        $iteration = CBViewPage::iterationForID(['ID' => $ID]);

        if ($iteration !== false) {
            $spec = CBViewPage::specWithID($ID, $iteration);

            if (!$spec) {
                throw new RuntimeException("No spec was found for the page ID: {$ID}");
            }

            $response->modelJSON = json_encode($spec);
        } else if (isset($_POST['id-to-copy'])) {
            $IDToCopy = $_POST['id-to-copy'];
            $iteration = CBViewPage::iterationForID(['ID' => $IDToCopy]);
            $spec = CBViewPage::specWithID($IDToCopy, $iteration);

            if (!$spec) {
                throw new RuntimeException("No spec was found for the page ID: {$IDToCopy}");
            }

            // Perform the copy
            $spec->dataStoreID = $ID;
            $spec->created = time();
            $spec->updated = $spec->created;
            $spec->title = isset($spec->title) ? "{$spec->title} Copy" : 'Copied Page';
            unset($spec->isPublished);
            unset($spec->iteration);
            unset($spec->publicationTimeStamp);
            unset($spec->pubishedBy);
            unset($spec->URI);
            unset($spec->URIIsStatic);

            $response->modelJSON = json_encode($spec);
        }

        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return {stdClass}
     */
    public static function fetchSpecForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @return null
     */
    public static function fetchSearchResultsForAjax() {
        $response = new CBAjaxResponse();
        $queryText = $_POST['query-text'];
        $words = preg_split('/[\s,]+/', $queryText, null, PREG_SPLIT_NO_EMPTY);
        $searchClausesForSQL = [];

        foreach ($words as $word) {
            if (strlen($word) > 2) {
                $wordForSQL = ColbyConvert::textToSQl($word);
                $searchClausesForSQL[] = "`searchText` LIKE '%{$wordForSQL}%'";
            }
        }

        if (empty($searchClausesForSQL)) {
            $response->pages = [];
        } else {
            $searchClausesForSQL = implode(' AND ', $searchClausesForSQL);
            $SQL = <<<EOT

                SELECT      HEX(`archiveID`), `keyValueData`
                FROM        `ColbyPages`
                WHERE       `className` = 'CBViewPage' AND
                            {$searchClausesForSQL}
                ORDER BY    `published` IS NULL DESC,
                            `titleHTML` ASC

EOT;
            $response->pages = CBDB::SQLToArray($SQL, ['valueIsJSON' => true]);
            $response->pages = CBViewPage::fixKeyValueDataArray($response->pages);
        }

        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return {stdClass}
     */
    public static function fetchSearchResultsForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @return null
     */
    public static function fetchUnpublishedPagesListForAjax() {
        $response = new CBAjaxResponse();
        $SQL = <<<EOT

            SELECT  HEX(`archiveID`), `keyValueData`
            FROM    `ColbyPages`
            WHERE   `className` = 'CBViewPage' AND
                    `published` IS NULL

EOT;

        $response->pages = CBDB::SQLToArray($SQL, ['valueIsJSON' => true]);
        $response->pages = CBViewPage::fixKeyValueDataArray($response->pages);
        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return {stdClass}
     */
    public static function fetchUnpublishedPagesListForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @deprecated This function should be removed when we make sure every
     * row in `ColbyPages` has a `keyValueData` value. This may require writing
     * an update all pages type of process.
     *
     * @param [{hex160} => null|{stdClass}]
     *
     * @return [{stdClass}]
     */
    private static function fixKeyValueDataArray(array $pages) {
        $pages = cb_array_map_assoc(function($ID, $page) {
            if ($page === null) {
                return (object) [
                    'dataStoreID' => $ID,
                    'title' => 'Page Needs to be Updated'
                ];
            } else {
                return $page;
            }
        }, $pages);

        return array_values($pages);
    }

    /**
     * @return {int} | false
     */
    public static function iterationForID($args) {
        $ID = null;
        extract($args, EXTR_IF_EXISTS);

        $IDAsSQL    = ColbyConvert::textToSQL($ID);
        $SQL        = "SELECT `iteration` FROM `ColbyPages` WHERE `archiveID` = UNHEX('{$IDAsSQL}')";

        return CBDB::SQLToValue($SQL);
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
        $model = CBViewPage::upgradeRenderModel($model);

        // The `upgradeRenderModel` function will return `false` when the query
        // string has values that are unrecognized and indicate that this page
        // does not exist.
        if ($model === false) {
            include Colby::findHandler('handle-default.php');
            return;
        }

        self::$modelContext = $model;

        CBHTMLOutput::begin();

        if ($model->classNameForSettings) {
            CBHTMLOutput::$classNameForSettings = $model->classNameForSettings;
        }

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
                    throw new RuntimeException('Further changes to this page cannot currently be saved because the page has been updated by another user or in another window. Reload this page to fetch those updates and then continue editing.');
                }

                $spec->iteration++;
            } else {
                CBPages::insertRow($ID);
                $spec->iteration = 1;
            }

            $iteration  = $spec->iteration;
            $model      = CBViewPage::specToModel($spec);

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
     * @return null
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

        /* Optional values */

        if (isset($spec->classNameForKind) &&
            is_string($spec->classNameForKind) &&
            strlen($value = trim($spec->classNameForKind)) > 0)
        {
            $model->classNameForKind = $value;
        } else {
            $model->classNameForKind = null;
        }

        $model->classNameForSettings    = isset($spec->classNameForSettings) ? trim($spec->classNameForSettings) : '';
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
        $model->URI                     = isset($spec->URI) ? trim($spec->URI) : '';
        $model->URI                     = $model->URI !== '' ? $model->URI : $model->dataStoreID;

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
        $model->URIAsHTML               = ColbyConvert::textToHTML($model->URI);

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

        $summaryViewModel           = CBViewPage::modelToSummaryViewModel($model);
        $rowData                    = new stdClass();
        $rowData->className         = 'CBViewPage';
        $rowData->classNameForKind  = $model->classNameForKind;
        $rowData->ID                = $model->dataStoreID;
        $rowData->keyValueData      = json_encode($summaryViewModel);
        $rowData->iteration         = $model->iteration;
        $rowData->titleHTML         = $model->titleHTML;
        $rowData->searchText        = CBViewPage::modelToSearchText($model);
        $rowData->subtitleHTML      = $model->descriptionHTML;
        $rowData->thumbnailURL      = $model->thumbnailURLAsHTML;
        $rowData->URI               = $model->URI;

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
     * This function performs a render time transform on a page model. This may
     * mean upgrading old models, but more likely it means transforming model
     * properties in response to query variables. This is how a single page can
     * become multiple pages using the query string and the page kind.
     *
     * @return {stdClass} | false
     *  Returns the modified model. A false value is returned when the query
     *  variables lead to a page that does not exist and a 404 page should be
     *  rendered.
     */
    private static function upgradeRenderModel($model) {

         // Version 2

        if (!isset($model->updated)) {
            $model->updated = time();
        }

        if (!isset($model->created)) {
            $model->created = $model->updated;
        }

        // Version 3

        if (!isset($model->listClassNames)) {
            $model->listClassNames = array();
        }

        $model->schemaVersion = 3;

        // classNameForKind

        if (isset($model->classNameForKind) && class_exists($classNameForKind = $model->classNameForKind)) {
            if (is_callable($function = "{$classNameForKind}::createModelForKind")) {
                $modelForKind = call_user_func($function);

                if ($modelForKind === false) {
                    return false;
                } else {
                    $model->modelForKind = $modelForKind;
                }
            } else {
                $model->modelForKind = null;
            }

            if (is_callable($function = "{$classNameForKind}::transformTitle")) {
                $model->title       = call_user_func($function, $model->title, ['modelForKind' => $modelForKind]);
                $model->titleHTML   = ColbyConvert::textToHTML($model->title);
            }

            if (is_callable($function = "{$classNameForKind}::transformDescription")) {
                $model->description     = call_user_func($function, $model->description, ['modelForKind' => $modelForKind]);
                $model->descriptionHTML = ColbyConvert::textToHTML($model->title);
            }
        }

        // 2015.09.19 classNameForSettings
        if (!isset($model->classNameForSettings)) {
            $model->classNameForSettings = '';
        }

        return $model;
    }
}
