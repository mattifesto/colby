<?php

/**
 * @deprecated 2017.07.24
 *
 * Replaced with CBPageTitleAndDescriptionView.css. Themes in general are also
 * deprecated. Remove this file once install has been run on all websites.
 */
final class CBWellKnownThemeForPageTitleAndDescription {

    const ID = '664d22662308f7443e4c3b43683d4934de087b86';

    /**
     * @return null
     */
    static function install() {
        Colby::query('START TRANSACTION');
        CBModels::deleteByID(CBWellKnownThemeForPageTitleAndDescription::ID);
        Colby::query('COMMIT');
    }
}
