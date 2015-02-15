<?php

class CBImages {

    /**
     * @return void
     */
    public static function update() {

        $SQL = <<<EOT

            CREATE TABLE IF NOT EXISTS `CBImages`
            (
                `ID`        BINARY(20) NOT NULL,
                `created`   BIGINT NOT NULL,
                `modified`  BIGINT NOT NULL,

                PRIMARY KEY (`ID`)
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8
            COLLATE=utf8_unicode_ci

EOT;

        Colby::query($SQL);
    }

    /**
     * @return void
     */
    private static function updateRow($ID, $timestamp) {
        $timestampAsSQL = (int)$timestamp;
        $IDAsSQL        = ColbyConvert::textToSQL($ID);
        $IDAsSQL        = "UNHEX('{$IDAsSQL}')";

        $SQL = <<<EOT

            INSERT INTO `CBImages`
                (`ID`, `created`, `modified`)
            VALUES
                ({$IDAsSQL}, {$timestampAsSQL}, {$timestampAsSQL})
            ON DUPLICATE KEY UPDATE
                `modified` = {$timestampAsSQL}

EOT;

        Colby::query($SQL);
    }

    /**
     * @return void
     */
    public static function uploadImageForAjax() {
        $response = new CBAjaxResponse();

        Colby::query('START TRANSACTION');

        try {
            $timestamp  = isset($_POST['timestamp']) ? $_POST['timestamp'] : time();
            $uploader   = ColbyImageUploader::uploaderForName('image');
            $ID         = $uploader->sha1();
            $dataStore  = new CBDataStore($ID);
            $filename   = 'original' .  $uploader->canonicalExtension();
            $filepath   = $dataStore->directory() . "/{$filename}";

            self::updateRow($ID, $timestamp);
            $dataStore->makeDirectory();
            $uploader->moveToFilename($filepath);
        } catch (Exception $exception) {
            Colby::query('ROLLBACK');
            throw $exception;
        }

        Colby::query('COMMIT');

        $response->imageFilename    = $filename;
        $response->imageURL         = $dataStore->URL() . "/{$filename}";
        $response->imageSizeX       = $uploader->sizeX();
        $response->imageSizeY       = $uploader->sizeY();
        $response->wasSuccessful    = true;

        $response->send();
    }
}
