<?php

/**
 * 2015.11.12
 *
 * The image uploader was allowing PSD files to be uploaded as images when it
 * shouldn't have been. This update removes all existing PSD images and they
 * are no longer allowed to be uploaded as images.
 */
class CBUpgradesForVersion174 {

    /**
     * @return null
     */
    public static function run() {
        $SQL = <<<EOT

            SELECT  LOWER(HEX(`ID`))
            FROM    `CBImages`
            WHERE   `extension` = 'psd'

EOT;

        $IDs = CBDB::SQLToArray($SQL);

        array_walk($IDs, 'CBImages::deleteByID');
    }
}
