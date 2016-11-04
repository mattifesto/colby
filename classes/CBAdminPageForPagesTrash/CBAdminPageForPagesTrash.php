<?php

final class CBAdminPageForPagesTrash {

    /**
     * @return [string]
     */
    public static function adminPageMenuNamePath() {
        return ['pages', 'trash'];
    }

    /**
     * @return stdClass
     */
    public static function adminPagePermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @return null
     */
    public static function adminPageRenderContent() {
        CBHTMLOutput::setTitleHTML('Pages Trash');
        CBHTMLOutput::setDescriptionHTML('Management of pages that are in the trash.');
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
    public static function requiredClassNames() {
        return ['CBUI'];
    }

    /**
     * @return [string]
     */
    static function requiredJavaScriptURLs() {
        return [Colby::flexnameForJavaScriptForClass(CBSystemURL, __CLASS__)];
    }
}
