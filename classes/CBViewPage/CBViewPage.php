<?php

/**
 * This page class represents an instance of an editable page containing
 * a list of views. This is the page type created by the Colby page editor.
 *
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
final class CBViewPage extends CBPage {

    private static $pageContext;

    protected $model;
    protected $subviews;

    /**
     * @return stdClass
     */
    public static function compileSpecificationModelToRenderModel($model) {
        return json_decode(json_encode($model));
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
     * @return instance type
     */
    public static function init() {
        $page                           = parent::init();
        $page->model                    = self::createDefaultModel();
        $page->model->dataStoreID       = $page->ID;
        $page->subviews                 = array();

        return $page;
    }

    /**
     * @return instance type | null
     */
    public static function initForImportWithID($ID)
    {
        $page = self::initWithID($ID);

        if ($page) {

            /**
             * This page is being imported from another site and therefore the
             * row ID won't refer to the correct row. Setting the row ID to null
             * will cause a row to be generated for the page in this site's
             * database.
             */

            $page->model->rowID = null;
        }

        return $page;
    }

    /**
     * @return instance type | null
     */
    public static function initWithID($ID)
    {
        $dataStore      = new CBDataStore($ID);
        $modelFilepath  = $dataStore->directory() . '/model.json';

        if (!file_exists($modelFilepath)) {

            return null;
        }

        $modelJSON  = file_get_contents($dataStore->directory() . '/model.json');
        $model      = json_decode($modelJSON);

        return self::initWithModel($model);
    }

    /**
     * @return instance type
     */
    public static function initWithModel($model) {
        $page               = parent::init();
        $page->model        = $model;
        $page->ID           = $page->model->dataStoreID;
        $page->subviews     = array();

        $page->upgradeModel();

        self::$pageContext = $page;

        foreach ($page->model->sections as $subviewModel) {

            /**
             * Instantiating the each view will upgrade its model in place
             * adding and removing properties as appropriate. Because of this
             * in place upgrade, the reference to the model object will not
             * need to be updated in the page model.
             */

            $page->subviews[] = CBView::createViewWithModel($subviewModel);
        }

        self::$pageContext = null;

        return $page;
    }

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
    public function model() {

        return $this->model;
    }

    /**
     * @return instance type
     */
    public static function pageContext() {

        return self::$pageContext;
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
    public function save() {

        try {

            $dataStoreID    = $this->model->dataStoreID;
            $mysqli         = Colby::mysqli();

            Colby::query('START TRANSACTION');

            if (!$this->model->rowID) {

                CBPages::deleteRowWithDataStoreID($dataStoreID);
                CBPages::deleteRowWithDataStoreIDFromTheTrash($dataStoreID);

                $rowData            = CBPages::insertRow($dataStoreID);
                $this->model->rowID = $rowData->rowID;
            }

            $this->updateDatabase();

            $modelJSON  = json_encode($this->model);
            $dataStore  = new CBDataStore($dataStoreID);

            file_put_contents($dataStore->directory() . '/model.json', $modelJSON, LOCK_EX);

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
        $response       = new CBAjaxResponse();
        $modelJSON      = $_POST['model-json'];
        $model          = json_decode($modelJSON);
        $page           = CBViewPage::initWithModel($model);

        $page->save();

        self::addToRecentlyEditedPagesList($model);

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
    private function searchText() {
        $searchText     = array();
        $searchText[]   = $this->model->title;
        $searchText[]   = $this->model->description;

        foreach ($this->subviews as $subview) {
            $searchText[] = $subview->searchText();
        }

        return implode(' ', $searchText);
    }

    /**
     * This function removes the post from all editable page lists because
     * editable page lists are specified in `$this->model->listClassNames`.
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
    public function renderHTML() {

        self::$pageContext = $this;

        /**
         * 2014.12.31
         * `CBHackSectionedPagesPageModel` was a temporary mothod of providing
         * the page context while rendering. Its users should be updaded to
         * use the `pageContext` method on this class and it should be
         * removed.
         */

        global $CBHackSectionedPagesPageModel;
        $CBHackSectionedPagesPageModel = $this->model;

        CBHTMLOutput::begin();

        include Colby::findFile('sections/public-page-settings.php');

        if (ColbyRequest::isForFrontPage())
        {
            CBHTMLOutput::setTitleHTML(CBSiteNameHTML);
        }
        else
        {
            CBHTMLOutput::setTitleHTML($this->model->titleHTML);
        }

        CBHTMLOutput::setDescriptionHTML($this->model->descriptionHTML);

        foreach ($this->subviews as $subview) {

            $subview->renderHTML();
        }

        CBHTMLOutput::render();

        self::$pageContext = null;
    }

    /**
     * @return void
     */
    private function updateDatabase() {

        $summaryViewModel       = self::compileSpecificationModelToSummaryViewModel($this->model);
        $rowData                = new stdClass();
        $rowData->className     = 'CBViewPage';
        $rowData->keyValueData  = json_encode($summaryViewModel);
        $rowData->rowID         = $this->model->rowID;
        $rowData->typeID        = null;
        $rowData->groupID       = $this->model->groupID;
        $rowData->titleHTML     = $this->model->titleHTML;
        $rowData->searchText    = $this->searchText();
        $rowData->subtitleHTML  = $this->model->descriptionHTML;

        if ($this->model->isPublished)
        {
            $rowData->published             = $this->model->publicationTimeStamp;
            $rowData->publishedBy           = $this->model->publishedBy;
            $rowData->publishedYearMonth    = ColbyConvert::timestampToYearMonth($rowData->published);
        }
        else
        {
            $rowData->published             = null;
            $rowData->publishedBy           = null;
            $rowData->publishedYearMonth    = '';
        }

        CBPages::updateRow($rowData);

        self::removeFromEditablePageLists($this->model);

        if ($this->model->isPublished) {

            self::addToPageLists($this->model);
        }
    }

    /**
     * @return void
     */
    private function upgradeModel() {

        $model = $this->model;

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
    }
}
