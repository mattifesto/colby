<?php

class CBAdminPageForImages {

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
            $image->thumbnailURL = CBAdminPageForImages::IDToThumbnailURL([
                'ID' => $image->ID,
                'extension' => $image->extension
            ]);
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
     * @return {string}
     */
    public static function IDToThumbnailURL($args) {
        $ID = $extension = '';
        extract($args, EXTR_IF_EXISTS);

        $thumbnailFilepath = CBDataStore::filepath([
            'ID' => $ID,
            'filename' => "rs200clc200.{$extension}"
        ]);

        if (!is_file($thumbnailFilepath)) {
            // create
        }

        if (is_file($thumbnailFilepath)) {
            return CBDataStore::toURL([
                'ID' => $ID,
                'filename' => "rs200clc200.{$extension}"
            ]);
        } else {
            return '';
        }
    }

    /**
     * @return void
     */
    public static function renderAsHTML() {
        if (!ColbyUser::current()->isOneOfThe('Developers')) {
            return include CBSystemDirectory . '/handlers/handle-authorization-failed.php';
        }

        CBHTMLOutput::$classNameForSettings = 'CBPageSettingsForAdminPages';
        CBHTMLOutput::begin();
        CBHTMLOutput::setTitleHTML('Images Administration');
        CBHTMLOutput::setDescriptionHTML('Tools to administer website images.');
        CBHTMLOutput::addCSSURL(CBSystemURL . '/javascript/CBAdminImageThumbnail.css');
        CBHTMLOutput::addJavaScriptURL(CBSystemURL . '/javascript/CBAdminImageThumbnailFactory.js');
        CBHTMLOutput::addCSSURL(CBAdminPageForImages::URL('CBAdminPageForImages.css'));
        CBHTMLOutput::addJavaScriptURL(CBAdminPageForImages::URL('CBAdminPageForImages.js'));

        $spec                           = new stdClass();
        $spec->selectedMenuItemName     = 'develop';
        $spec->selectedSubmenuItemName  = 'images';
        CBAdminPageMenuView::renderModelAsHTML(CBAdminPageMenuView::specToModel($spec));

        echo '<main></main>';

        CBAdminPageFooterView::renderModelAsHTML();
        CBHTMLOutput::render();
    }

    /**
     * @return {string}
     */
    public static function URL($filename) {
        $className = __CLASS__;
        return CBSystemURL . "/classes/{$className}/{$filename}";
    }
}
