<?php

class CBImages {

    /**
     * @return ?model (CBImage)
     */
    static function CBAjax_upload(): ?stdClass {
        return CBImages::uploadImageWithName('file');
    }


    /**
     * @return string
     */
    static function CBAjax_upload_group(): string {
        return 'Administrators';
    }


    /**
     * This function is called by CBImage::CBModels_willDelete() and shouldn't
     * be called otherwise. To delete an image call
     *
     *      CBModels::deleteByID(<imageID>)
     *
     * @param string $ID
     *
     * @return void
     */
    static function deleteByID(string $ID): void {
        $IDAsSQL = CBHex160::toSQL($ID);
        $SQL = "DELETE FROM `CBImages` WHERE `ID` = {$IDAsSQL}";

        Colby::query($SQL);
    }


    /**
     * @param {hex160} $ID
     *
     * @return {string} | false
     *  The original image filepath or false if the image doesn't exist
     */
    static function IDToOriginalFilepath($ID) {
        $filepaths = glob(
            $s = CBDataStore::filepath(
                [
                    'ID' => $ID,
                    'filename' => 'original.*'
                ]
            )
        );

        if (empty($filepaths)) {
            return false;
        } else {
            return $filepaths[0];
        }
    }


    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $SQL = <<<EOT

            CREATE TABLE IF NOT EXISTS `CBImages` (
                `ID`        BINARY(20) NOT NULL,
                `created`   BIGINT NOT NULL,
                `extension` VARCHAR(10) NOT NULL,
                `modified`  BIGINT NOT NULL,

                PRIMARY KEY (`ID`)
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_520_ci

EOT;

        Colby::query($SQL);
    }


    /**
     * This function determines in an ID is a valid CBImage ID.
     *
     * @param mixed $ID
     *
     * @return bool
     */
    static function isInstance($ID): bool {
        if (!CBHex160::is($ID)) {
            return false;
        }

        $IDAsSQL = CBHex160::toSQL($ID);
        $SQL = <<<EOT

            SELECT COUNT(*)
            FROM `CBImages`
            WHERE `ID` = {$IDAsSQL}

EOT;

        return boolval(
            CBDB::SQLToValue($SQL)
        );
    }


    /**
     * This function is called by ColbyRequest before declaring a request a 404
     * error. It will produce generated image sizes for CBImages. The next
     * request for that URL will just return the image file that's been saved
     * to disk.
     *
     * @param string $path
     *
     *  Example: "/data/28/12/580870551ac6833f1ea589a9490d37d48302/rw400.png"
     *
     *  This parameter may be any path, the function determines where it can
     *  react to the path.
     *
     * @return bool
     *
     *  Returns true if an image was generated and sent; otherwise false.
     */
    static function makeAndSendImageForPath($path) {
        if (
            !preg_match(
                '%^/data/([0-9a-f]{2})/([0-9a-f]{2})/([0-9a-f]{36})/([^/]+)$%',
                $path,
                $matches
            )
        ) {
            return false;
        }

        $ID = "{$matches[1]}{$matches[2]}{$matches[3]}";
        $basename = $matches[4];
        $pathinfo = pathinfo($basename);
        $operation = $pathinfo['filename'];
        $extension = $pathinfo['extension'];

        if (
            !in_array(
                $operation,
                CBSitePreferences::onDemandImageResizeOperations()
            )
        ) {
            return false;
        }

        if (
            !file_exists(
                CBDataStore::flexpath(
                    $ID,
                    "original.{$extension}",
                    cbsitedir()
                )
            )
        ) {
            return false;
        }

        CBImages::reduceImage($ID, $extension, $operation);

        $reducedFilepath = cbsitedir() . $path;
        $size = CBImage::getimagesize($reducedFilepath);
        $mimeType = image_type_to_mime_type($size[2]);

        /* serve image to browser */
        header('Content-Type:' . $mimeType);
        readfile($reducedFilepath);

        return true;
    }
    /* makeAndSendImageForPath() */


    /**
     * @param string $ID
     *
     * @return object|false
     *
     *      CBImage model
     */
    static function makeModelForID(string $ID) {
        $IDAsSQL = CBHex160::toSQL($ID);
        $extension = CBDB::SQLToValue(
            "SELECT `extension` FROM `CBImages` WHERE `ID` = {$IDAsSQL}"
        );

        if ($extension === false) {
            return false;
        }

        $originalImageFilepath = CBDataStore::flexpath(
            $ID,
            "original.{$extension}",
            cbsitedir()
        );

        $size = CBImage::getimagesize($originalImageFilepath);

        if ($size === false) {
            return false;
        }

        return (object)[
            'className' => 'CBImage',
            'extension' => $extension,
            'filename' => 'original',
            'height' => $size[1],
            'ID' => $ID,
            'width' => $size[0],
        ];
    }
    /* makeModelForID() */


    /**
     * Creates a reduced image for an operation only if the reduced image
     * doesn't already exist.
     *
     * @param string $ID
     *  The image ID
     * @param string $extension
     *  The image extension
     * @param string $operation
     *  The reduction operation, example: "rs200clc200"
     *
     * @return object
     *
     *      CBImage model
     */
    static function reduceImage($ID, $extension, $operation) {
        $sourceFilepath = CBDataStore::flexpath(
            $ID,
            "original.{$extension}",
            cbsitedir()
        );

        $destinationFilepath = CBDataStore::flexpath(
            $ID,
            "{$operation}.{$extension}",
            cbsitedir()
        );

        if (!is_file($destinationFilepath)) {
            $size = CBImage::getimagesize($sourceFilepath);
            $projection = CBProjection::withSize($size[0], $size[1]);
            $projection = CBProjection::applyOpString($projection, $operation);

            CBImages::reduceImageFile(
                $sourceFilepath,
                $destinationFilepath,
                $projection
            );
        }

        $size = CBImage::getimagesize($destinationFilepath);

        return (object)[
            'className' => 'CBImage',
            'extension' => $extension,
            'filename' => $operation,
            'height' => $size[1],
            'ID' => $ID,
            'width' => $size[0],
        ];
    }
    /* reduceImage() */


    /**
     * This function can be used to simply reduce an image file that may or may
     * not be a CBImage. It is used by the other functions to reduce CBImages.
     *
     * @return void
     */
    static function reduceImageFile(
        $sourceFilepath,
        $destinationFilepath,
        $projection,
        $args = []
    ): void {
        $quality = null;
        extract($args, EXTR_IF_EXISTS);

        ini_set('memory_limit', '256M');

        $size = CBImage::getimagesize($sourceFilepath);

        if (CBProjection::isNoOpForSize($projection, $size[0], $size[1])) {
            copy($sourceFilepath, $destinationFilepath);
            return;
        }

        $src = $projection->source;
        $dst = $projection->destination;
        $output = imagecreatetruecolor($dst->width, $dst->height);

        switch ($size[2]) {
            case IMAGETYPE_GIF:
                $input = imagecreatefromgif($sourceFilepath);

                break;

            case IMAGETYPE_JPEG:
                $input = imagecreatefromjpeg($sourceFilepath);
                $exif = CBImage::exif_read_data($sourceFilepath);

                $orientation =
                empty($exif['Orientation']) ?
                1 :
                $exif['Orientation'];

                if ($orientation == 3) {
                    $input = imagerotate($input, 180, 0);
                } else if ($orientation == 6) {
                    $input = imagerotate($input, -90, 0);
                } else if ($orientation == 8) {
                    $input = imagerotate($input, 90, 0);
                }

                break;

            case IMAGETYPE_PNG:
                $input = imagecreatefrompng($sourceFilepath);

                imagealphablending($output, false);
                imagesavealpha($output, true);

                $transparent = imagecolorallocatealpha(
                    $output,
                    255,
                    255,
                    255,
                    127
                );

                imagefilledrectangle(
                    $output,
                    0,
                    0,
                    $dst->width,
                    $dst->height,
                    $transparent
                );

                break;

            default:
                throw new RuntimeException(
                    "The image type for the file \"{$sourceFilepath}\" is " .
                    "not supported."
                );

                break;
        }

        imagecopyresampled(
            $output, $input,
            $dst->x,     $dst->y,      $src->x,     $src->y,
            $dst->width, $dst->height, $src->width, $src->height
        );

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
    /* reduceImageFile() */


    /**
     * This function is called by CBImage::CBModels_willSave() and shouldn't
     * be called otherwise. Saving an image model will call this function.
     *
     *      CBModels::save([<imageSpec])
     *
     * @return void
     */
    static function updateRow($ID, $timestamp, $extension): void {
        $extensionAsSQL = ColbyConvert::textToSQL($extension);
        $extensionAsSQL = "'{$extension}'";
        $IDAsSQL = ColbyConvert::textToSQL($ID);
        $IDAsSQL = "UNHEX('{$IDAsSQL}')";
        $timestampAsSQL = (int)$timestamp;
        $SQL = <<<EOT

            INSERT INTO `CBImages`
                (`ID`, `created`, `extension`, `modified`)
            VALUES
                (
                    {$IDAsSQL},
                    {$timestampAsSQL},
                    {$extensionAsSQL},
                    {$timestampAsSQL}
                )
            ON DUPLICATE KEY UPDATE
                `modified` = {$timestampAsSQL}

EOT;

        Colby::query($SQL);
    }
    /* updateRow() */


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
    static function uploadForAjax() {
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

        $requestedSizes =
        isset($_POST['imageSizesAsJSON']) ?
        json_decode($_POST['imageSizesAsJSON']) :
        [];

        foreach ($requestedSizes as $operation) {
            $reducedImage = CBImages::reducedImage(
                $image->ID,
                $image->extension,
                $operation
            );

            $reducedImageBasename =
            "{$reducedImage->filename}.{$reducedImage->extension}";

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
        $response->image = $image;
        $response->sizes = $sizes;

        $response->message =
        "The image with an ID of {$response->image->ID} uploaded successfully";

        $response->wasSuccessful = true;
        $response->send();
    }
    /* uploadForAjax() */


    /**
     * @return object
     */
    static function uploadForAjaxPermissions(): stdClass {
        return (object)['group' => 'Administrators'];
    }


    /**
     * NOTE: 2017_06_16 This function should be modified to call URIToCBImage()
     *
     * @param string $name
     *
     * @return object (CBImage spec)
     *
     *      {
     *          className: "CBImage"
     *          ID: ID
     *          extension: string
     *          filename: string
     *          height: int
     *          width: int
     *      }
     */
    private static function uploadImageWithName(string $name): stdClass {
        CBImages::verifyUploadedFile($name);

        $temporaryFilepath = $_FILES[$name]['tmp_name'];
        $size = CBImage::getimagesize($temporaryFilepath);

        if (
            $size === false ||
            !in_array(
                $size[2],
                [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG]
            )
        ) {
            throw new Exception(
                'The file specified is either not a an image or has a file ' .
                'format that is not supported.'
            );
        }

        Colby::query('START TRANSACTION');

        try {

            $timestamp =
            isset($_POST['timestamp']) ?
            $_POST['timestamp'] :
            time();

            $extension = image_type_to_extension(
                /* type: */ $size[2],
                /* include dot: */ false
            );

            $ID = sha1_file($temporaryFilepath);
            $filename = "original";
            $basename = "{$filename}.{$extension}";

            $permanentFilepath = CBDataStore::flexpath(
                $ID,
                $basename,
                cbsitedir()
            );

            $spec = (object)[
                'className' => 'CBImage',
                'ID' => $ID,
                'extension' => $extension,
                'filename' => $filename,
                'height' => $size[1],
                'width' => $size[0],
            ];

            CBModels::save([$spec], /* force: */ true);
            CBDataStore::makeDirectoryForID($ID);
            move_uploaded_file($temporaryFilepath, $permanentFilepath);
            Colby::query('COMMIT');

        } catch (Throwable $exception) {

            Colby::query('ROLLBACK');

            throw $exception;

        }

        return $spec;
    }
    /* uploadImageWithName() */


    /**
     * This function will return a CBImage spec for an image URI. If the URI is
     * a local image file that is not a CBImage, the image will be imported.
     *
     * @return ?model
     *
     *      If the URI does not represent a CBImage, is not local, or can't be
     *      imported for other reasons then null will be returned.
     */
    static function URIToCBImage(string $URI): ?stdClass {
        $dataStoreID = CBDataStore::URIToID($URI);

        if (CBImages::isInstance($dataStoreID)) {
            return CBImages::makeModelForID($dataStoreID);
        }

        $filepath = CBDataStore::URIToFilepath($URI);

        if (empty($filepath)) {
            return null;
        }

        $size = CBImage::getimagesize($filepath);
        $spec = null;

        if (
            $size === false ||
            !in_array(
                $size[2],
                [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG]
            )
        ) {
            throw new Exception(
                'The file specified is either not a an image or has a file ' .
                'format that is not supported.'
            );
        }

        Colby::query('START TRANSACTION');

        try {

            $timestamp = time();

            $extension = image_type_to_extension(
                /* type: */ $size[2],
                /* include dot: */ false
            );

            $ID = sha1_file($filepath);
            $filename = "original";
            $basename = "{$filename}.{$extension}";

            $destinationFilepath = CBDataStore::flexpath(
                $ID,
                $basename,
                cbsitedir()
            );

            $spec = (object)[
                'className' => 'CBImage',
                'extension' => $extension,
                'filename' => $filename,
                'height' => $size[1],
                'ID' => $ID,
                'width' => $size[0],
            ];

            CBModels::save([$spec], /* force: */ true);
            CBDataStore::makeDirectoryForID($ID);
            copy($filepath, $destinationFilepath);
            Colby::query('COMMIT');

        } catch (Throwable $exception) {

            Colby::query('ROLLBACK');

            throw $exception;

        }

        return $spec;
    }
    /* URIToCBImage() */


    /**
     * @param string $name
     *
     * @return null
     */
    static function verifyUploadedFile($name) {
        if ($_FILES[$name]['error'] != UPLOAD_ERR_OK) {
            switch ($_FILES[$name]['error']) {
                case UPLOAD_ERR_INI_SIZE:

                    $maxSize = ini_get('upload_max_filesize');

                    $message =
                    "The file uploaded exceeds the allowed upload size " .
                    "of: {$maxSize}.";

                    break;

                case UPLOAD_ERR_FORM_SIZE:

                    $maxSize = ini_get('post_max_size');

                    $message =
                    "The file uploaded exceeds the allowed post upload " .
                    "size of: {$maxSize}.";

                    break;

                case UPLOAD_ERR_PARTIAL:
                case UPLOAD_ERR_NO_FILE:
                case UPLOAD_ERR_NO_TMP_DIR:
                case UPLOAD_ERR_CANT_WRITE:
                case UPLOAD_ERR_EXTENSION:
                default:

                    $message =
                    "File upload error code: {$_FILES[$name]['error']}";
            }

            throw new RuntimeException($message);
        }
    }
    /* verifyUploadedFile() */
}
