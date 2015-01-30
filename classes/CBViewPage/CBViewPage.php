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
class CBViewPage extends CBPage {

    private static $pageContext;

    protected $model;
    protected $subviews;

    /**
     * 2014.10.22
     *  The code in this function was first copied out of `CBPageTemplate`.
     *  This is the correct home for the code and that class should eventually
     *  either be replaced by or at the very least call this class to replace
     *  its functions.
     *
     * @return instance type
     */
    public static function init() {

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

        $page                           = parent::init();
        $page->model                    = $model;
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

        $modelJSON          = file_get_contents($dataStore->directory() . '/model.json');
        $page               = parent::init();
        $page->model        = json_decode($modelJSON);
        $page->ID           = $page->model->dataStoreID;
        $page->subviews     = array();
        self::$pageContext  = $page;

        $page->upgradeModel();

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
    private function addToPageLists() {

        $pageRowID  = (int)$this->model->rowID;
        $updated    = (int)$this->model->updated;
        $yearMonth  = gmdate('Ym', $updated);

        foreach ($this->model->listClassNames as $className) {

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
     * This code was copied from `handle,admin,pages,api,save-model.php`. This
     * is the correct home for the code but the two locations will need to be
     * kept in sync until that file is updated to use this class.
     *
     * @return CBView
     */
    private function createSummaryView() {

        $summaryView                            = CBPageSummaryView::init();
        $summaryViewModel                       = $summaryView->model();

        $summaryViewModel->created              = $this->model->created;
        $summaryViewModel->dataStoreID          = $this->model->dataStoreID;
        $summaryViewModel->description          = $this->model->description;
        $summaryViewModel->descriptionHTML      = $this->model->descriptionHTML;
        $summaryViewModel->isPublished          = $this->model->isPublished;
        $summaryViewModel->publicationTimeStamp = $this->model->publicationTimeStamp;
        $summaryViewModel->publishedBy          = $this->model->publishedBy;
        $summaryViewModel->thumbnailURL         = $this->model->thumbnailURL;
        $summaryViewModel->title                = $this->model->title;
        $summaryViewModel->titleHTML            = $this->model->titleHTML;
        $summaryViewModel->updated              = $this->model->updated;
        $summaryViewModel->URI                  = $this->model->URI;

        return $summaryView;
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

            $mysqli->autocommit(false);

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

            $mysqli->rollback();

            throw $exception;
        }

        $mysqli->commit();
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
    private function removeFromEditablePageLists() {

        global $CBPageEditorAvailablePageListClassNames;

        $listClassNames         = array_merge($CBPageEditorAvailablePageListClassNames,
                                              $this->model->listClassNames,
                                              array('CBRecentlyEditedPages'));

        $listClassNames         = array_unique($listClassNames);
        $listClassNamesForSQL   = array();

        foreach ($listClassNames as $listClassName) {

            $classNameForSQL        = ColbyConvert::textToSQL($listClassName);
            $classNameForSQL        = "'{$classNameForSQL}'";
            $listClassNamesForSQL[] = $classNameForSQL;
        }

        $listClassNamesForSQL   = implode(',', $listClassNamesForSQL);
        $pageRowID              = (int)$this->model->rowID;
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

        $summaryView            = $this->createSummaryView();

        $rowData                = new stdClass();
        $rowData->keyValueData  = json_encode($summaryView->model());
        $rowData->rowID         = $this->model->rowID;
        $rowData->typeID        = CBPageTypeID;
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

        $this->removeFromEditablePageLists();

        if ($this->model->isPublished) {

            $this->addToPageLists();
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
