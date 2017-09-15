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
     * @return [object]
     *
     *      {
     *          ID: hex160
     *          created: int
     *          extension: string
     *          modified: int
     *          thumbnailURL: string
     *      }
     *
     */
    static function CBAjax_fetchImages() {
        $SQL = <<<EOT

            SELECT LOWER(HEX(`ID`)) as `ID`, `created`, `extension`, `modified`
            FROM `CBImages`
            ORDER BY `modified` DESC
            LIMIT 500

EOT;

        $images = CBDB::SQLToObjects($SQL);

        foreach ($images as $image) {
            $image->thumbnailURL = CBDataStore::flexpath($image->ID, "rw320.{$image->extension}", cbsiteurl());
        }

        return $images;
    }

    /**
     * @return string
     */
    static function CBAjax_fetchImages_group() {
        return 'Administrators';
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
