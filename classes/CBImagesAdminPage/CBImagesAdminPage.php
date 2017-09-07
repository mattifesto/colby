<?php

class CBImagesAdminPage {

    /**
     * @return [string]
     */
    static function adminPageMenuNamePath() {
        return ['develop', 'images'];
    }

    /**
     * @return stdClass
     */
    static function adminPagePermissions() {
        return (object)['group' => 'Developers'];
    }

    /**
     * @return void
     */
    static function adminPageRenderContent() {
        CBHTMLOutput::setTitleHTML('Images Administration');
        CBHTMLOutput::setDescriptionHTML('Tools to administer website images.');
    }

    /**
     * @return null
     */
    static function fetchImagesForAjax() {
        $response = new CBAjaxResponse();
        $SQL = <<<EOT

            SELECT LOWER(HEX(`ID`)) as `ID`, `created`, `extension`, `modified`
            FROM `CBImages`
            ORDER BY `modified` DESC
            LIMIT 20

EOT;

        $images = CBDB::SQLToObjects($SQL);

        foreach ($images as $image) {
            $image->thumbnailURL = CBDataStore::flexpath($image->ID, "rw320.{$image->extension}", CBSitePreferences::siteURL());
        }

        $response->images = $images;
        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return void
     */
    static function fetchImagesForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [Colby::flexnameForCSSForClass(CBSystemURL, __CLASS__)];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexnameForJavaScriptForClass(CBSystemURL, __CLASS__)];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return ['CBUI'];
    }
}
