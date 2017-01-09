<?php

final class CBViewPage {

    private static $modelContext;

    /**
     * @param hex160 $ID
     * @param bool $create
     *      If a spec doesn't already exist for the ID, an empty one will be
     *      created if this parameter is true. There are enough processes for
     *      pages that this option has measurable benefits.
     *
     * @return stdClass|false
     */
    static function fetchSpecByID($ID, $create = false) {
        $spec = CBModels::fetchSpecByID($ID);

        if (empty($spec)) {
            $iteration = CBViewPage::iterationForID(['ID' => $ID]);

            if ($iteration !== false) {
                $spec = CBViewPage::specWithID($ID, $iteration);
            }

            if (empty($spec) && $create) {
                $spec = (object)[
                    'className' => __CLASS__,
                    'ID' => $ID,
                ];
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

        $IDAsSQL = ColbyConvert::textToSQL($ID);
        $SQL = "SELECT `iteration` FROM `ColbyPages` WHERE `archiveID` = UNHEX('{$IDAsSQL}')";

        return CBDB::SQLToValue($SQL);
    }

    /**
     * @deprecated use CBPageContext::current()
     *
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
     *  An empty string will be returned if no image is available.
     */
    static function modelToImageURL($model) {
        $image = null;

        if (!empty($model->image)) {
            $image = $model->image;
        } else if (!empty($model->thumbnailURL)) {
            $image = CBImage::URIToImage($model->thumbnailURL);

            if (empty($image)) {
                return $model->thumbnailURL;
            }
        }

        if (empty($image)) {
            return '';
        } else {
            return CBDataStore::flexpath($image->ID, "rw1280.{$image->extension}", CBSiteURL);
        }
    }

    /**
     * @param stdClass $model
     *
     * @return string
     */
    static function modelToSearchText($model) {
        $searchText = array();
        $searchText[] = $model->title;
        $searchText[] = $model->description;

        if (!empty($model->layout->className)) {
            if (is_callable($function = "{$model->layout->className}::modelToSearchText")) {
                $searchText[] = call_user_func($function, $model->layout);
            }
        }

        self::$modelContext = $model;

        CBPageContext::push([
            'descriptionAsHTML' => $model->descriptionHTML,
            'ID' => $model->ID,
            'publishedTimestamp' => $model->publicationTimeStamp,
            'titleAsHTML' => $model->titleHTML,
        ]);

        foreach ($model->sections as $modelForView) {
            $searchText[] = CBView::modelToSearchText($modelForView);
        }

        CBPageContext::pop();

        self::$modelContext = null;

        return implode(' ', $searchText);
    }

    /**
     * @deprecated use renderModelAsHTML
     *  Once all pages are saved to the CBModels table, this function will no
     *  longer be called.
     *
     * @return null
     */
    public static function renderAsHTMLForID($ID, $iteration) {
        $directory = CBDataStore::directoryForID($ID);

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

        $model = json_decode(file_get_contents($filepath));

        if (empty($model->ID)) {
            $model->ID = $model->dataStoreID;
        }

        CBViewPage::renderModelAsHTML($model);
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

        CBViewPage::$modelContext = $model; /* deprecated */

        CBPageContext::push([
            'descriptionAsHTML' => $model->descriptionHTML,
            'ID' => $model->ID,
            'imageURL' => CBViewPage::modelToImageURL($model),
            'publishedTimestamp' => $model->publicationTimeStamp,
            'titleAsHTML' => $model->titleHTML,
        ]);

        CBHTMLOutput::begin();

        if ($model->classNameForSettings) {
            CBHTMLOutput::$classNameForSettings = $model->classNameForSettings;
        }

        $dependencyIDs = array_reduce($model->sections, function($carry, $viewModel) {
            $carry = array_merge($carry, CBView::modelToModelDependencies($viewModel));
            return $carry;
        }, []);

        CBModelCache::cacheModelsByID($dependencyIDs);

        $renderContentCallback = function () use ($model) {
            array_walk($model->sections, 'CBView::renderModelAsHTML');
        };

        if (!empty($model->layout->className)) {
            CBHTMLOutput::requireClassName($model->layout->className);

            if (is_callable($renderLayout = "{$model->layout->className}::render")) {
                call_user_func($renderLayout, $model->layout, $renderContentCallback);
            }
        } else {
            $renderContentCallback();
        }

        CBHTMLOutput::render();

        CBPageContext::pop();

        CBViewPage::$modelContext = null; /* deprecated */
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

        $model->classNameForSettings = isset($spec->classNameForSettings) ? trim($spec->classNameForSettings) : '';
        $model->description = isset($spec->description) ? $spec->description : '';
        $model->image = CBModel::value($spec, 'image', null, 'CBImage::specToModel');
        $model->isPublished = isset($spec->isPublished) ? !!$spec->isPublished : false;
        $model->iteration = 0;
        $model->publicationTimeStamp = isset($spec->publicationTimeStamp) ? (int)$spec->publicationTimeStamp : ($model->isPublished ? $time : null);
        $model->publishedBy = isset($spec->publishedBy) ? $spec->publishedBy : null;
        $model->schemaVersion = isset($spec->schemaVersion) ? $spec->schemaVersion : null; /* Deprecated? */
        $model->thumbnailURL = isset($spec->thumbnailURL) ? $spec->thumbnailURL : null; /* deprecated, use `image` */
        $model->title = isset($spec->title) ? $spec->title : '';
        $model->URI = isset($spec->URI) ? trim($spec->URI) : '';
        $model->URI = $model->URI !== '' ? $model->URI : $model->ID;

        /**
         * Layout
         */

        if (isset($spec->layout)) {
            $model->layout = CBModel::specToOptionalModel($spec->layout);
        }

        /**
         * Views
         */

        if (isset($spec->sections)) {
            /* TODO: use CBModel::specToOptionalModel */
            $model->sections = array_map('CBView::specToModel', $spec->sections);
        } else {
            $model->sections = array();
        }

        /**
         * Computed values
         */

        $model->descriptionHTML = ColbyConvert::textToHTML($model->description);
        $model->thumbnailURLAsHTML = ColbyConvert::textToHTML($model->thumbnailURL);
        $model->titleHTML = ColbyConvert::textToHTML($model->title);
        $model->URIAsHTML = ColbyConvert::textToHTML($model->URI);

        if (empty($model->image) && !empty($model->thumbnailURL)) {
            $model->image = CBImage::URIToImage($model->thumbnailURL);
        }

        return $model;
    }

    /**
     * @return stdClass|false
     */
    public static function specWithID($ID, $iteration) {
        $directory = CBDataStore::directoryForID($ID);

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

        $model->schemaVersion = 3;

        // 2015.09.19 classNameForSettings
        if (!isset($model->classNameForSettings)) {
            $model->classNameForSettings = '';
        }

        return $model;
    }
}
