<?php

final class CBAdminPageForPagesTrash {

    /**
     * @return [string]
     */
    static function adminPageMenuNamePath() {
        return ['pages', 'trash'];
    }

    /**
     * @return stdClass
     */
    static function adminPagePermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @return null
     */
    static function adminPageRenderContent() {
        CBHTMLOutput::setTitleHTML('Pages Trash');
        CBHTMLOutput::setDescriptionHTML('Management of pages that are in the trash.');
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'v374.js', cbsysurl())];
    }

    /**
     * @return null
     */
    static function fetchPageSummaryModelsForAjax() {
        $response = new CBAjaxResponse();
        $response->models = CBDB::SQLToArray(
            'SELECT `keyValueData` FROM `CBPagesInTheTrash`',
            ['valueIsJSON' => true]
        );
        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return stdClass
     */
    static function fetchPageSummaryModelsForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @param hex160 $_POST['ID']
     *
     * @return null
     */
    static function recoverPageForAjax() {
        $response = new CBAjaxResponse();

        $ID = $_POST['ID'];

        CBPages::recoverRowWithDataStoreIDFromTheTrash($ID);

        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return stdClass
     */
    static function recoverPageForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @return [string]
     */
    static function requiredClassNames() {
        return ['CBUI'];
    }
}
