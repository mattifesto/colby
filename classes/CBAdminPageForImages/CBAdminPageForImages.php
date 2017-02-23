<?php

class CBAdminPageForImages {

    /**
     * @return [string]
     */
    public static function adminPageMenuNamePath() {
        return ['develop', 'images'];
    }

    /**
     * @return stdClass
     */
    public static function adminPagePermissions() {
        return (object)['group' => 'Developers'];
    }

    /**
     * @return void
     */
    public static function adminPageRenderContent() {
        CBHTMLOutput::setTitleHTML('Images Administration');
        CBHTMLOutput::setDescriptionHTML('Tools to administer website images.');
    }

    /**
     * @return null
     */
    public static function fetchImagesForAjax() {
        $response = new CBAjaxResponse();
        $SQL = <<<EOT

            SELECT LOWER(HEX(`ID`)) as `ID`, `created`, `extension`, `modified`
            FROM `CBImages`
            ORDER BY `modified` DESC
            LIMIT 20

EOT;

        $images = CBDB::SQLToObjects($SQL);

        foreach ($images as $image) {
            $image->thumbnailURL = CBDataStore::flexpath($image->ID, "rw320.{$image->extension}", CBSiteURL);
        }

        $response->images = $images;
        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return void
     */
    public static function fetchImagesForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @return [string]
     */
    public static function requiredCSSURLs() {
        return [Colby::flexnameForCSSForClass(CBSystemURL, __CLASS__)];
    }

    /**
     * @return [string]
     */
    public static function requiredJavaScriptURLs() {
        return [Colby::flexnameForJavaScriptForClass(CBSystemURL, __CLASS__)];
    }
}
