<?php

class CBImages {

    /**
     * @return void
     */
    public static function reduceImageFile($sourceFilepath, $destinationFilepath, $projection) {
        $src    = $projection->source;
        $dst    = $projection->destination;
        $size   = getimagesize($sourceFilepath);
        $output = imagecreatetruecolor($dst->width, $dst->height);

        switch ($size[2]) {
            case IMAGETYPE_GIF:
                $input = imagecreatefromgif($sourceFilepath);
                break;
            case IMAGETYPE_JPEG:
                $input = imagecreatefromjpeg($sourceFilepath);
                break;
            case IMAGETYPE_PNG:
                $input = imagecreatefrompng($sourceFilepath);
                imagealphablending($output, false);
                imagesavealpha($output, true);
                $transparent = imagecolorallocatealpha($output, 255, 255, 255, 127);
                imagefilledrectangle($output, 0, 0, $dst->width, $dst->height, $transparent);
                break;
            default:
                throw new RuntimeException(
                    "The image type for the file \"{$sourceFilepath}\" is not supported.");
                break;
        }

        imagecopyresampled($output, $input,
            $dst->x,     $dst->y,      $src->x,     $src->y,
            $dst->width, $dst->height, $src->width, $src->height);

        switch ($size[2]) {
            case IMAGETYPE_GIF:
                imagegif($output, $destinationFilepath);
                break;
            case IMAGETYPE_JPEG:
                imagejpeg($output, $destinationFilepath, /* quality: */ 90);
                break;
            case IMAGETYPE_PNG:
                imagepng($output, $destinationFilepath);
                break;
        }
    }

    /**
     * @return void
     */
    public static function update() {

        $SQL = <<<EOT

            CREATE TABLE IF NOT EXISTS `CBImages`
            (
                `ID`        BINARY(20) NOT NULL,
                `created`   BIGINT NOT NULL,
                `extension` VARCHAR(10) NOT NULL,
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
    private static function updateRow($ID, $timestamp, $extension) {
        $extensionAsSQL = ColbyConvert::textToSQL($extension);
        $extensionAsSQL = "'{$extension}'";
        $IDAsSQL        = ColbyConvert::textToSQL($ID);
        $IDAsSQL        = "UNHEX('{$IDAsSQL}')";
        $timestampAsSQL = (int)$timestamp;
        $SQL            = <<<EOT

            INSERT INTO `CBImages`
                (`ID`, `created`, `extension`, `modified`)
            VALUES
                ({$IDAsSQL}, {$timestampAsSQL}, {$extensionAsSQL}, {$timestampAsSQL})
            ON DUPLICATE KEY UPDATE
                `modified` = {$timestampAsSQL}

EOT;

        Colby::query($SQL);
    }

    /**
     * This function was initially created to replace a class named
     * CBAPIUploadImage. The API is not ideal, but it matches what that class
     * had. Eventually this function should be replace with a better API.
     *
     * @return void
     */
    public static function uploadAndReduceForAjax() {
        $response = new CBAjaxResponse();

        $originalInfo           = self::uploadImageWithName('image');
        $originalFilename       = "original.{$originalInfo->extension}";
        $dataStore              = new CBDataStore($originalInfo->ID);
        $projection             = CBProjection::withSize($originalInfo->size[0], $originalInfo->size[1]);
        $destinationFilename    = '';

        if (isset($_POST['reduceToWidth'])) {
            $width                  = (int)$_POST['reduceToWidth'];
            $destinationFilename   .= "rw{$width}";
            $projection             = CBProjection::reduceWidth($projection, $width);
        }

        if (isset($_POST['reduceToHeight'])) {
            $height                 = (int)$_POST['reduceToHeight'];
            $destinationFilename   .= "rh{$height}";
            $projection             = CBProjection::reduceHeight($projection, $height);
        }

        if (isset($_POST['cropToWidth'])) {
            $width                  = (int)$_POST['cropToWidth'];
            $destinationFilename   .= "cwc{$width}";
            $projection             = CBProjection::cropWidthFromCenter($projection, $width);
        }

        if (isset($_POST['cropToHeight'])) {
            $height                 = (int)$_POST['cropToHeight'];
            $destinationFilename   .= "chc{$height}";
            $projection             = CBProjection::cropHeightFromCenter($projection, $height);
        }

        if ($destinationFilename) {
            $destinationFilename    = "{$destinationFilename}.{$originalInfo->extension}";
            $originalFilepath       = $dataStore->directory() . "/{$originalFilename}";
            $destinationFilepath    = $dataStore->directory() . "/{$destinationFilename}";

            self::reduceImageFile($originalFilepath, $destinationFilepath, $projection);
        } else {
            $destinationFilename    = $originalFilename;
            $originalFilepath       = $dataStore->directory() . "/{$originalFilename}";
            $destinationFilepath    = $dataStore->directory() . "/{$destinationFilename}";
        }

        $size                       = getimagesize($destinationFilepath);
        $response->filename         = $destinationFilename;
        $response->URL              = $dataStore->URL() . "/{$destinationFilename}";
        $response->URLForHTML       = ColbyConvert::textToHTML($response->URL);
        $response->actualWidth      = $size[0];
        $response->actualHeight     = $size[1];
        $response->wasSuccessful    = true;

        $response->send();
    }

    /**
     * @return void
     */
    public static function uploadAndReduceForAjaxPermissions() {
        $permissions        = new stdClass();
        $permissions->group = 'Administrators';

        return $permissions;
    }

    /**
     * @return stdClass
     */
    public static function uploadImageWithName($name) {
        ColbyImageUploader::verifyUploadedFile($name);

        Colby::query('START TRANSACTION');

        try {
            $timestamp          = isset($_POST['timestamp']) ? $_POST['timestamp'] : time();
            $temporaryFilepath  = $_FILES[$name]['tmp_name'];

            $size               = getimagesize($temporaryFilepath);
            $type               = $size[2];
            $extension          = image_type_to_extension($type, /* include dot: */ false);
            $ID                 = sha1_file($temporaryFilepath);
            $dataStore          = new CBDataStore($ID);
            $filename           = "original.{$extension}";
            $permanentFilepath  = $dataStore->directory() . "/{$filename}";

            self::updateRow($ID, $timestamp, $extension);
            $dataStore->makeDirectory();
            move_uploaded_file($temporaryFilepath, $permanentFilepath);
        } catch (Exception $exception) {
            Colby::query('ROLLBACK');
            throw $exception;
        }

        Colby::query('COMMIT');

        $info               = new stdClass();
        $info->size         = $size;
        $info->ID           = $ID;
        $info->extension    = $extension;

        return $info;
    }
}
