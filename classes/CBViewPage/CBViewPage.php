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
     * @return null
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
     * @deprecated
     *
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
        $model = (object)[
            'className' => 'CBViewPage',
        ];
        $model->ID = null;
        $model->description = '';
        $model->descriptionHTML = '';
        $model->groupID = null;
        $model->isPublished = false;
        $model->listClassNames = array();
        $model->publicationTimeStamp = null;
        $model->publishedBy = null;
        $model->schema = 'CBPage';
        $model->schemaVersion = 3;
        $model->sections = array();
        $model->thumbnailURL = null;
        $model->title = '';
        $model->titleHTML = '';
        $model->URI = null;
        $model->URIIsStatic = false;

        return $model;
    }

    /**
     * @param hex160 $ID
     *
     * @return stdClass|false
     */
    public static function fetchSpecByID($ID) {
        $spec = CBModels::fetchSpecByID($ID);

        if ($spec === false) {
            $iteration = CBViewPage::iterationForID(['ID' => $ID]);

            if ($iteration !== false) {
                $spec = CBViewPage::specWithID($ID, $iteration);

                if (empty($spec)) {
                    throw new RuntimeException("The spec is missing for the following ID: {$ID}");
                }
            }
        } else if ($spec->className !== 'CBViewPage') {
            throw new RuntimeException("The spec with the following ID is not a CBViewPage: {$ID}");
        }

        return $spec;
    }

    /**
     * The current behavior of this function is to set the modelJSON response
     * if the spec exists and to not set it if it doesn't.
     *
     * @return null
     */
    public static function fetchSpecForAjax() {
        $response = new CBAjaxResponse();
        $ID = $_POST['id'];
        $spec = CBViewPage::fetchSpecByID($ID);

        if ($spec === false) {
            if (isset($_POST['id-to-copy'])) {
                $IDToCopy = $_POST['id-to-copy'];
                $spec = CBViewPage::fetchSpecByID($IDToCopy);

                if ($spec === false) {
                    throw new RuntimeException("No spec was found for the page ID: {$IDToCopy}");
                }

                // Perform the copy
                $spec->ID = $ID;
                $spec->title = isset($spec->title) ? "{$spec->title} Copy" : 'Copied Page';
                unset($spec->dataStoreID);
                unset($spec->isPublished);
                unset($spec->iteration);
                unset($spec->publicationTimeStamp);
                unset($spec->publishedBy);
                unset($spec->URI);
                unset($spec->URIIsStatic);
                unset($spec->version);
            }
        }

        if ($spec) {
            $response->modelJSON = json_encode($spec);
        }

        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return stdClass
     */
    public static function fetchSpecForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @deprecated This will not be necessary when all pages are saved as models.
     *
     * @return int|false
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
     * @return stdClass
     */
    public static function makeSpecForID($args) {
        $ID = null;
        extract($args, EXTR_IF_EXISTS);

        $spec = CBViewPage::fetchSpecByID($ID);

        if (!$spec) {
            $spec = (object)[
                'ID' => $ID,
                'className' => __CLASS__,
            ];
        }

        return $spec;
    }

    /**
     * @return stdClass|null
     */
    public static function modelContext() {
        return self::$modelContext;
    }

    /**
     * @param [hex160] $IDs
     *
     * @return null
     */
    public static function modelsWillDelete(array $IDs) {
        CBPages::deletePagesByID($IDs);
        CBPages::deletePagesFromTrashByID($IDs);
    }

    /**
     * @param [stdClass] $tuples
     *
     * @return null
     */
    public static function modelsWillSave(array $tuples) {
        $models = array_map(function($tuple) { return $tuple->model; }, $tuples);
        CBPages::save($models);
    }

    /**
     * @return string
     */
    public static function modelToSearchText($model) {
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
     * @return null
     */
    private static function removeFromEditablePageLists($model, $pageRowID) {
        $availableListNames     = CBViewPageLists::availableListNames();
        $listClassNames         = isset($model->listClassNames) ? $model->listClassNames : [];
        $listClassNames         = array_merge($availableListNames, $listClassNames);
        $listClassNames         = array_unique($listClassNames);
        $listClassNamesForSQL   = [];

        if (empty($listClassNames)) { return; }

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
     * @return null
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

        CBViewPage::renderModelAsHTML(json_decode(file_get_contents($filepath)));
    }

    /**
     * @return null
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

        CBViewPage::$modelContext = $model;

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

        CBViewPage::$modelContext = null;
    }

    /**
     * @deprecated since version 147. Use `modelContext` instead.
     *
     * @return stdClass
     */
    public static function renderModelContext() {
        return CBViewPage::$modelContext;
    }

    /**
     * This function updates the page data in the database and saves the page
     * model files.
     *
     * @param stdClass $args['spec']
     *
     * @return null
     */
    public static function save($args) {
        $spec = $args['spec'];

        if (empty($spec->ID)) {
            $spec->ID = $spec->dataStoreID;
        }

        CBModels::save([$spec]);
    }

    /**
     * @return null
     */
    public static function saveEditedPageForAjax() {
        $response = new CBAjaxResponse();
        $spec = json_decode($_POST['model-json']);

        CBViewPage::save(['spec' => $spec]);

        /**
         * @deprecated The following line should go away with page lists
         */
        CBViewPage::updatePageLists(CBModels::fetchModelByID($spec->ID));

        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return stdClass
     */
    public static function saveEditedPageForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @param stdClass $spec
     *
     * @return stdClass
     */
    public static function specToModel($spec) {
        $model = (object)[
            'ID' => $spec->ID,
            'className' => __CLASS__,
        ];
        $time = time();

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
        $model->description             = isset($spec->description) ? $spec->description : '';
        $model->isPublished             = isset($spec->isPublished) ? !!$spec->isPublished : false;
        $model->iteration               = 0;
        $model->listClassNames          = isset($spec->listClassNames) ? $spec->listClassNames : array();
        $model->publicationTimeStamp    = isset($spec->publicationTimeStamp) ? (int)$spec->publicationTimeStamp : ($model->isPublished ? $time : null);
        $model->publishedBy             = isset($spec->publishedBy) ? $spec->publishedBy : null;
        $model->schemaVersion           = isset($spec->schemaVersion) ? $spec->schemaVersion : null; /* Deprecated? */
        $model->thumbnailURL            = isset($spec->thumbnailURL) ? $spec->thumbnailURL : null;
        $model->title                   = isset($spec->title) ? $spec->title : '';
        $model->URI                     = isset($spec->URI) ? trim($spec->URI) : '';
        $model->URI                     = $model->URI !== '' ? $model->URI : $model->ID;

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
     * @return stdClass|false
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

        if (empty($spec->ID)) {
            $spec->ID = $spec->dataStoreID;
        }

        if (!isset($spec->iteration)) {
            $spec->iteration = 1;
        }

        return $spec;
    }

    /**
     * @return null
     */
    private static function updatePageLists($model) {
        $IDAsSQL = CBHex160::toSQL($model->ID);
        $pageRowID = CBDB::SQLToValue("SELECT `ID` FROM `ColbyPages` WHERE `archiveID` = {$IDAsSQL}");

        CBViewPage::removeFromEditablePageLists($model, $pageRowID);

        if ($model->isPublished) {
            CBViewPage::addToPageLists($model, $pageRowID);
        }
    }

    /**
     * This function performs a render time transform on a page model. This may
     * mean upgrading old models, but more likely it means transforming model
     * properties in response to query variables. This is how a single page can
     * become multiple pages using the query string and the page kind.
     *
     * @return stdClass|false
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
