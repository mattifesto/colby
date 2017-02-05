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
     * @deprecated use CBImages::reduceImage()
     *
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
        error_log('Call made to deprecated function: ' . __METHOD__);
        $originalImageFilepath = CBImages::IDToOriginalFilepath($ID);

        if ($operation === null || $originalImageFilepath === false) {
            return $originalImageFilepath;
        }

        $extension = pathinfo($originalImageFilepath, PATHINFO_EXTENSION);

        CBImages::reduceImage($ID, $extension, $operation);

        return CBDataStore::flexpath($ID, "{$operation}.{$extension}", CBSiteDirectory);
    }

    /**
     * @param string $path
     *  Example: "/data/28/12/580870551ac6833f1ea589a9490d37d48302/rw400.png"
     *
     * @return bool
     */
    static function makeAndSendImageForPath($path) {
        if (!preg_match('%^/data/([0-9a-f]{2})/([0-9a-f]{2})/([0-9a-f]{36})/([^/]+)$%', $path, $matches)) {
            return false;
        }

        $ID = "{$matches[1]}{$matches[2]}{$matches[3]}";
        $basename = $matches[4];
        $pathinfo = pathinfo($basename);
        $operation = $pathinfo['filename'];
        $extension = $pathinfo['extension'];

        if (!in_array($operation, CBSitePreferences::onDemandImageResizeOperations())) {
            return false;
        }

        if (!file_exists(CBDataStore::flexpath($ID, "original.{$extension}", CBSiteDirectory))) {
            return false;
        }

        CBImages::reduceImage($ID, $extension, $operation);

        $reducedFilepath = CBSiteDirectory . $path;
        $size = getimagesize($reducedFilepath);
        $mimeType = image_type_to_mime_type($size[2]);

        /* serve image to browser */
        header('Content-Type:' . $mimeType);
        readfile($reducedFilepath);

        return true;
    }

    /**
     * Creates a reduced image for an operation only if the reduced image
     * doesn't already exist.
     *
     * @param hex160 $ID
     *  The image ID
     * @param string $extension
     *  The image extension
     * @param string $operation
     *  The reduction operation, example: "rs200clc200"
     *
     * @return stdClass (image)
     */
    static function reduceImage($ID, $extension, $operation) {
        $sourceFilepath = CBDataStore::flexpath($ID, "original.{$extension}", CBSiteDirectory);
        $destinationFilepath = CBDataStore::flexpath($ID, "{$operation}.{$extension}", CBSiteDirectory);

        if (!is_file($destinationFilepath)) {
            $size = getimagesize($sourceFilepath);
            $projection = CBProjection::withSize($size[0], $size[1]);
            $projection = CBProjection::applyOpString($projection, $operation);

            CBImages::reduceImageFile($sourceFilepath, $destinationFilepath, $projection);
        }

        $size = getimagesize($destinationFilepath);

        return (object)[
            'extension' => $extension,
            'filename' => $operation,
            'height' => $size[1],
            'ID' => $ID,
            'width' => $size[0],
        ];
    }

    /**
     * @return null
     */
    public static function reduceImageFile($sourceFilepath, $destinationFilepath, $projection, $args = []) {
        $quality = null;
        extract($args, EXTR_IF_EXISTS);

        ini_set('memory_limit', '256M');

        $src = $projection->source;
        $dst = $projection->destination;
        $size = getimagesize($sourceFilepath);
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
                if ($quality === null) {
                    imagejpeg($output, $destinationFilepath);
                } else {
                    imagejpeg($output, $destinationFilepath, $quality);
                }
                break;
            case IMAGETYPE_PNG:
                imagepng($output, $destinationFilepath);
                break;
        }
    }

    /**
     * @param string $_POST['extension']
     * @param hex160 $_POST['ID']
     * @param string $_POST['operation']
     *
     * @return {
     *      image : {
     *          extension : string,
     *          filename : string,
     *          height : int,
     *          ID : hex160,
     *          width : int,
     *      }
     *  }
     */
    static function reduceImageForAjax() {
        $response = new CBAjaxResponse();
        $extension = $_POST['extension'];
        $ID = $_POST['ID'];
        $operation = $_POST['operation'];
        $response->image = CBImages::reduceImage($ID, $extension, $operation);
        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return stdClass
     */
    static function reduceImageForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @return null
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
     * @return null
     */
    private static function updateRow($ID, $timestamp, $extension) {
        $extensionAsSQL = ColbyConvert::textToSQL($extension);
        $extensionAsSQL = "'{$extension}'";
        $IDAsSQL = ColbyConvert::textToSQL($ID);
        $IDAsSQL = "UNHEX('{$IDAsSQL}')";
        $timestampAsSQL = (int)$timestamp;
        $SQL = <<<EOT

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

        $originalInfo = CBImages::uploadImageWithName('image');
        $originalFilename = "original.{$originalInfo->extension}";
        $dataStore = new CBDataStore($originalInfo->ID);
        $projection = CBProjection::withSize($originalInfo->size[0], $originalInfo->size[1]);
        $destinationFilename = '';

        if (isset($_POST['reduceToWidth'])) {
            $width = (int)$_POST['reduceToWidth'];
            $destinationFilename   .= "rw{$width}";
            $projection = CBProjection::reduceWidth($projection, $width);
        }

        if (isset($_POST['reduceToHeight'])) {
            $height = (int)$_POST['reduceToHeight'];
            $destinationFilename   .= "rh{$height}";
            $projection = CBProjection::reduceHeight($projection, $height);
        }

        if (isset($_POST['cropToWidth'])) {
            $width = (int)$_POST['cropToWidth'];
            $destinationFilename   .= "cwc{$width}";
            $projection = CBProjection::cropWidthFromCenter($projection, $width);
        }

        if (isset($_POST['cropToHeight'])) {
            $height = (int)$_POST['cropToHeight'];
            $destinationFilename   .= "chc{$height}";
            $projection = CBProjection::cropHeightFromCenter($projection, $height);
        }

        if ($destinationFilename) {
            $destinationFilename = "{$destinationFilename}.{$originalInfo->extension}";
            $originalFilepath = $dataStore->directory() . "/{$originalFilename}";
            $destinationFilepath = $dataStore->directory() . "/{$destinationFilename}";

            CBImages::reduceImageFile($originalFilepath, $destinationFilepath, $projection);
        } else {
            $destinationFilename = $originalFilename;
            $originalFilepath = $dataStore->directory() . "/{$originalFilename}";
            $destinationFilepath = $dataStore->directory() . "/{$destinationFilename}";
        }

        $size = getimagesize($destinationFilepath);
        $response->filename = $destinationFilename;
        $response->URL = $dataStore->URL() . "/{$destinationFilename}";
        $response->URLForHTML = ColbyConvert::textToHTML($response->URL);
        $response->actualWidth = $size[0];
        $response->actualHeight = $size[1];
        $response->wasSuccessful = true;

        $response->send();
    }

    /**
     * @return null
     */
    public static function uploadAndReduceForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * 2015.06.04 This is intended to be the primary Ajax function used to
     * upload and resize an image.
     *
     * @param file $_POST['image']
     * @param [string] $_POST['imageSizesAsJSON']
     *      This option will be used less now that automatic image resizing
     *      exists.
     *
     *      Example: ['rw300', 'rh400', 'rs200clc200']
     *
     * @return null
     * @return Ajax
     *
     *      {
     *          stdClass image
     *          [stdClass] sizes
     *      }
     */
    public static function uploadForAjax() {
        $response = new CBAjaxResponse();
        $image = CBImages::uploadImageWithName('image');
        $basename = "{$image->filename}.{$image->extension}";

        $sizes['original'] = (object)[
            'height' => $image->height,
            'width' => $image->width,
            'URL' => CBDataStore::toURL([
                'ID' => $image->ID,
                'filename' => $basename,
            ]),
        ];

        $requestedSizes = isset($_POST['imageSizesAsJSON']) ? json_decode($_POST['imageSizesAsJSON']) : [];

        foreach ($requestedSizes as $operation) {
            $reducedImage = CBImages::reducedImage($image->ID, $image->extension, $operation);
            $reducedImageBasename = "{$reducedImage->filename}.{$reducedImage->extension}";

            $sizes[$operation] = (object)[
                'height' => $reducedImage->height,
                'width' => $reducedImage->width,
                'URL' => CBDataStore::toURL([
                    'ID' => $reducedImage->ID,
                    'filename' => $reducedImageBasename,
                ]),
            ];
        }

        $response->extension = $image->extension;   /* @deprecated use $image->extension */
        $response->ID = $image->ID;                 /* @deprecated use $image->ID */
        $response->image = (object)[
            'base' => $image->filename,             /* @deprecated use $image->filename */
            'extension' => $image->extension,
            'filename' => $image->filename,
            'height' => $image->height,
            'ID' => $image->ID,
            'width' => $image->width,
        ];
        $response->sizes = $sizes;
        $response->message = "Image uploaded successfully";
        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return null
     */
    public static function uploadForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @param string $name
     *
     * @return stdClass (image)
     *
     *      {
     *          string extension,
     *          string filename,
     *          int height,
     *          hex160 ID,
     *          int width,
     *      }
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
            $timestamp = isset($_POST['timestamp']) ? $_POST['timestamp'] : time();
            $extension = image_type_to_extension(/* type: */ $size[2], /* include dot: */ false);
            $ID = sha1_file($temporaryFilepath);
            $dataStore = new CBDataStore($ID);
            $filename = "original";
            $basename = "{$filename}.{$extension}";
            $permanentFilepath = $dataStore->directory() . "/{$basename}";

            CBImages::updateRow($ID, $timestamp, $extension);
            $dataStore->makeDirectory();
            move_uploaded_file($temporaryFilepath, $permanentFilepath);
            Colby::query('COMMIT');
        } catch (Exception $exception) {
            Colby::query('ROLLBACK');
            throw $exception;
        }

        $image = new stdClass();
        $image->extension = $extension;
        $image->filename = $filename;
        $image->filenameFromDataStore = $basename;
        $image->height = $size[1];
        $image->ID = $ID;
        $image->size = $size; // @deprecated use width and height
        $image->width = $size[0];

        return $image;
    }
}
