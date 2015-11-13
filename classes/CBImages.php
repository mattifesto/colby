<?php

class CBImages {

    /**
     * Deletes an image from the database and deletes all versions of the image
     * from disk. This function should rarely be used as images are generally
     * considered to be permanent resources because it's impossible to track
     * dependencies especially since external sites and Google can link to them.
     * Only delete when necessary or in specific situations where dependencies
     * are understood.
     *
     * @return null
     */
    public static function deleteByID($ID) {
        $IDAsSQL = CBHex160::toSQL($ID);
        $SQL = "DELETE FROM `CBImages` WHERE `ID` = {$IDAsSQL}";
        Colby::query($SQL);
        CBDataStore::deleteByID($ID);
    }

    /**
     * @param {hex160} $ID
     *
     * @return {string} | false
     *  The original image filepath or false if the image doesn't exist
     */
    public static function IDToOriginalFilepath($ID) {
        $filepaths = glob($s = CBDataStore::filepath([
            'ID' => $ID,
            'filename' => 'original.*'
        ]));

        if (empty($filepaths)) {
            return false;
        } else {
            return $filepaths[0];
        }
    }

    /**
     * @param {hex160} $ID
     * @param {string} $operation
     *  An operation string describing the desired image. Images will only be
     *  reduced, not enlarged and the result image may be smaller than the
     *  maximum possible image size for the operation string.
     *
     *  Ex: "rs200clc200" - Reduce the short edge to 200 pixels and crop the
     *  long edge to the center 200 pixels.
     *
     *  If null is specified as the operation string then the original image
     *  filepath will be returned.
     *
     * @return {string} | false
     *  The image filepath for false if an image with this ID doesn't exist
     */
    public static function makeImage($ID, $operation = null) {
        $originalImageFilepath = CBImages::IDToOriginalFilepath($ID);

        if ($operation === null || $originalImageFilepath === false) {
            return $originalImageFilepath;
        }

        $extension = pathinfo($originalImageFilepath, PATHINFO_EXTENSION);
        $imageFilepath = CBDataStore::filepath([
            'ID' => $ID,
            'filename' => "{$operation}.{$extension}"
        ]);

        if (!is_file($imageFilepath)) {
            $size = getimagesize($originalImageFilepath);
            $projection = CBProjection::withSize($size[0], $size[1]);
            $projection = CBProjection::applyOpString($projection, $operation);

            CBImages::reduceImageFile($originalImageFilepath, $imageFilepath, $projection);
        }

        return $imageFilepath;
    }

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
    public static function install() {
        $SQL = <<<EOT

            CREATE TABLE IF NOT EXISTS `CBImages` (
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
     * @deprecated use uploadForAjax
     *
     * @return null
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
     * 2015.06.04 This is intended to be the primary Ajax function used to
     * upload and resize an image.
     */
    public static function uploadForAjax() {
        $response           = new CBAjaxResponse();
        $info               = self::uploadImageWithName('image');
        $originalFilepath   = CBDataStore::filepath([
            'ID'            => $info->ID,
            'filename'      => $info->filenameFromDataStore ]);
        $requestedSizes     = isset($_POST['imageSizesAsJSON']) ? json_decode($_POST['imageSizesAsJSON']) : [];
        $sizes['original']  = (object)[
            'height'        => $info->height,
            'width'         => $info->width,
            'URL'           => CBDataStore::toURL([
                'ID'        => $info->ID,
                'filename'  => $info->filenameFromDataStore ])
        ];

        foreach ($requestedSizes as $size) {
            $filename   = "{$size}.{$info->extension}";
            $filepath   = CBDataStore::filepath(['ID' => $info->ID, 'filename' => $filename]);
            $projection = CBProjection::withSize($info->width, $info->height);
            $projection = CBProjection::applyOpString($projection, $size);

            CBImages::reduceImageFile($originalFilepath, $filepath, $projection);

            $sizes[$size]       = (object)[
                'height'        => $projection->destination->height,
                'width'         => $projection->destination->width,
                'URL'           => CBDataStore::toURL([ 'ID' => $info->ID, 'filename' => $filename ])
            ];
        }

        $response->extension        = $info->extension;
        $response->ID               = $info->ID;
        $response->sizes            = $sizes;
        $response->wasSuccessful    = true;
        $response->send();
    }

    /**
     * @return void
     */
    public static function uploadForAjaxPermissions() {
        $permissions        = new stdClass();
        $permissions->group = 'Administrators';

        return $permissions;
    }

    /**
     * @return stdClass
     */
    public static function uploadImageWithName($name) {
        ColbyImageUploader::verifyUploadedFile($name);

        $temporaryFilepath = $_FILES[$name]['tmp_name'];
        $size = getimagesize($temporaryFilepath);

        if ($size === false || !in_array($size[2], [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG])) {
            throw new Exception('The file specified is either not a an image or has a file format that is not supported.');
        }

        Colby::query('START TRANSACTION');

        try {
            $timestamp          = isset($_POST['timestamp']) ? $_POST['timestamp'] : time();
            $extension          = image_type_to_extension(/* type: */ $size[2], /* include dot: */ false);
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

        $info                           = new stdClass();
        $info->extension                = $extension;
        $info->filenameFromDataStore    = $filename;
        $info->height                   = $size[1];
        $info->ID                       = $ID;
        $info->size                     = $size; // @deprecated use width and height
        $info->width                    = $size[0];

        return $info;
    }
}
