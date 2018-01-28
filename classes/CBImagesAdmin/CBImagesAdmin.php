<?php

class CBImagesAdmin {

    /**
     * @return string
     */
    static function CBAdmin_group() {
        return 'Developers';
    }

    /**
     * @return [string]
     */
    static function CBAdmin_menuNamePath(): array {
        return ['develop', 'images'];
    }

    /**
     * @return void
     */
    static function CBAdmin_render() {
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
        return [Colby::flexpath(__CLASS__, 'v374.css', cbsysurl())];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'v374.js', cbsysurl())];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return ['CBUI', 'CBUISectionItem4', 'CBUIStringsPart'];
    }
}
