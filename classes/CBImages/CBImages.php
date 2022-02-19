<?php

/**
 * @deprecated 2020_11_15
 *
 *      The table named CBImages is most likely not necessary as any use could
 *      be replaced with a query using the CBModels and CBModelVersions tables.
 *
 *      Slowly remove uses of the CBImages table and update this comment if any
 *      required uses of the table exist. If they do, rename the table to
 *      something like CBImageTable so uses can be found more easily.
 */
final class
CBImages {

    /* -- CBAjax interfaces -- */



    /**
     * @return object
     *
     *      Returns a CBImage model.
     */
    static function
    CBAjax_upload(
    ): stdClass
    {
        return CBImages::uploadImageWithName(
            'file'
        );
    }
    /* CBAjax_upload() */



    /**
     * @return string
     */
    static function
    CBAjax_upload_getUserGroupClassName(
    ): string
    {
        return 'CBAdministratorsUserGroup';
    }
    /* CBAjax_upload_getUserGroupClassName() */



    /* -- CBInstall interfaces -- */



    /**
     * @return void
     */
    static function
    CBInstall_install(
    ): void
    {
        $SQL = <<<EOT

            CREATE TABLE IF NOT EXISTS
            CBImages

            (
                ID
                BINARY(20) NOT NULL,

                created
                BIGINT NOT NULL,

                extension
                VARCHAR(10) NOT NULL,

                modified
                BIGINT NOT NULL,

                PRIMARY KEY (
                    ID
                )
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_520_ci

        EOT;

        Colby::query(
            $SQL
        );
    }
    /* CBInstall_install() */



    /* -- functions -- */



    /**
     * This function is called by CBImage::CBModels_willDelete() and shouldn't
     * be called otherwise. To delete an image call
     *
     *      CBModels::deleteByID(<imageModelCBID>)
     *
     * @param CBID $imageModelCBID
     *
     * @return void
     */
    static function
    deleteByID(
        string $imageModelCBID
    ): void
    {
        $imageModelCBIDAsSQL = CBID::toSQL(
            $imageModelCBID
        );

        $SQL = <<<EOT

            DELETE FROM
            CBImages

            WHERE
            ID = {$imageModelCBIDAsSQL}

        EOT;

        Colby::query(
            $SQL
        );
    }
    /* deleteByID() */



    /**
     * @param CBID $imageModelCBID
     *
     * @return string|false
     *
     *      The original image filepath or false if the image doesn't exist.
     */
    static function
    IDToOriginalFilepath(
        $imageModelCBID
    ) /* : mixed */
    {
        $globPattern = CBDataStore::filepath(
            [
                'ID' => $imageModelCBID,
                'filename' => 'original.*'
            ]
        );

        $filepaths = glob(
            $globPattern
        );

        if (
            count($filepaths) > 1
        ) {
            CBErrorHandler::report(
                new CBExceptionWithValue(
                    CBConvert::stringToCleanLine(<<<EOT

                        CBImage models should have only one original image
                        file. The CBImage with the model CBID ${imageModelCBID}
                        has more than one.

                    EOT),
                    $filepaths,
                    '6884537431f0037947d27ed05fae36a3db20bcc9'
                )
            );
        }

        if (
            empty($filepaths)
        ) {
            return false;
        } else {
            return $filepaths[0];
        }
    }
    /* IDToOriginalFilepath() */



    /**
     * This function is called by ColbyRequest before declaring a request a 404
     * error. It will produce generated image sizes for CBImages. The next
     * request for that URL will just return the image file that's been saved
     * to disk.
     *
     * @param string $requestedImageURLPath
     *
     *      Example:
     *      /data/28/12/580870551ac6833f1ea589a9490d37d48302/rw400.png
     *
     *      This parameter may be any path, the function determines where it can
     *      react to the path.
     *
     * @return bool
     *
     *      Returns true if an image was generated and sent; otherwise false.
     */
    static function
    makeAndSendImageForPath(
        $requestedImageURLPath
    ): bool
    {
        if (
            !preg_match(
                '%^/data/([0-9a-f]{2})/([0-9a-f]{2})/([0-9a-f]{36})/([^/]+)$%',
                $requestedImageURLPath,
                $matches
            )
        ) {
            return false;
        }

        $requestedImageModelCBID = "{$matches[1]}{$matches[2]}{$matches[3]}";
        $requestedImageBasename = $matches[4];

        $pathinfo = pathinfo(
            $requestedImageBasename
        );

        $requestedImageResizeOperation = $pathinfo['filename'];
        $requestedImageExtension = $pathinfo['extension'];

        if (
            !in_array(
                $requestedImageResizeOperation,
                CBSitePreferences::onDemandImageResizeOperations()
            )
        ) {
            return false;
        }

        /**
         * @TODO 2022_02_19
         *
         *      The following check needs to take place with the original image
         *      extension, not the requested extension. But we can just use
         *      CBImages::IDToOriginalFilepath() to do this check.
         */

        /**
         * If there is no original image file, we can't generate a reduced or
         * converted image file.
         */

        if (
            !file_exists(
                CBDataStore::flexpath(
                    $requestedImageModelCBID,
                    "original.{$requestedImageExtension}",
                    cbsitedir()
                )
            )
        ) {
            return false;
        }

        CBImages::reduceImage(
            $requestedImageModelCBID,
            $requestedImageExtension,
            $requestedImageResizeOperation
        );

        $reducedImageFilepath = (
            cbsitedir() .
            $requestedImageURLPath
        );

        $size = CBImage::getimagesize(
            $reducedImageFilepath
        );

        $mimeType = image_type_to_mime_type(
            $size[2]
        );

        /* serve image to browser */
        header(
            'Content-Type:' .
            $mimeType
        );

        readfile(
            $reducedImageFilepath
        );

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
        $IDAsSQL = CBID::toSQL($ID);
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
    static function
    reduceImage(
        $ID,
        $extension,
        $operation
    ) {
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

        if (
            !is_file($destinationFilepath)
        ) {
            $size = CBImage::getimagesize(
                $sourceFilepath
            );

            $projection = CBProjection::withSize(
                $size[0],
                $size[1]
            );

            $projection = CBProjection::applyOpString(
                $projection,
                $operation
            );

            CBImages::reduceImageFile(
                $sourceFilepath,
                $destinationFilepath,
                $projection
            );
        }

        $size = CBImage::getimagesize(
            $destinationFilepath
        );

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
        $extensionAsSQL = CBDB::escapeString($extension);
        $extensionAsSQL = "'{$extension}'";
        $IDAsSQL = CBDB::escapeString($ID);
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
    private static function
    uploadImageWithName(
        string $name
    ): stdClass {
        CBImages::verifyUploadedFile(
            $name
        );

        $temporaryFilepath = $_FILES[$name]['tmp_name'];

        $size = CBImage::getimagesize(
            $temporaryFilepath
        );

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

        $timestamp = (
            isset($_POST['timestamp']) ?
            $_POST['timestamp'] :
            time()
        );

        $extension = image_type_to_extension(
            /* type: */ $size[2],
            /* include dot: */ false
        );

        $ID = sha1_file(
            $temporaryFilepath
        );

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

        CBDB::transaction(
            function () use (
                $spec
            ) {
                CBModels::save(
                    $spec,
                    /* force: */ true
                );
            }
        );

        CBDataStore::makeDirectoryForID(
            $ID
        );

        move_uploaded_file(
            $temporaryFilepath,
            $permanentFilepath
        );

        return $spec;
    }
    /* uploadImageWithName() */



    /**
     * References to images should be stored as CBImage models not URLs. If the
     * caller has only a URL, they can pass it to this function to convert it to
     * an CBImage model to use at that moment or hopefully instead to upgrade
     * the source to store the CBImage model instead of the image URL.
     *
     * @return object|null
     *
     *      This function will return a CBImage model for an image URL. If the
     *      URL is a local image file that doesn't yet have a CBImage model, one
     *      will be created.
     *
     *      If the URL does not represent an image, is not local, or can't be
     *      imported for other reasons then null will be returned.
     */
    static function
    URIToCBImage(
        string $imageURI
    ): ?stdClass {
        $imageCBID = CBDataStore::URLToCBID(
            $imageURI
        );

        if (!empty($imageCBID)) {
            $imageModel = CBModels::fetchModelByIDNullable(
                $imageCBID
            );

            if (
                $imageModel !== null &&
                $imageModel->className === 'CBImage'
            ) {
                return $imageModel;
            }
        }


        /**
         * @NOTE 2020_11_15
         *
         *      On this day the logic of this function is being clarified.
         *
         *      Because Colby didn't always save models for images, if there is
         *      no model, this function will check for historical reasons
         *      whether there is an actual image file in the data store. If
         *      there is an image file, a CBImage model will be created, saved,
         *      and returned.
         *
         *      At this moment I don't know when or if this logic can be removed
         *      or if it should be moved somewhere else or to new functions.
         */

        $originalImageFilepath = CBImages::IDToOriginalFilepath(
            $imageCBID
        );

        if (empty($originalImageFilepath)) {
            $importedImageFilepath = CBDataStore::URIToFilepath(
                $imageURI
            );
        } else {
            $importedImageFilepath = $originalImageFilepath;
        }

        if (empty($importedImageFilepath)) {
            return null;
        }

        $size = CBImage::getimagesize(
            $importedImageFilepath
        );

        if ($size === false) {
            $imageHasValidExtension = false;
        } else {
            $imageHasValidExtension = in_array(
                $size[2],
                [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG]
            );
        }

        if (
            $size === false ||
            !$imageHasValidExtension
        ) {
            $imageURLAsCBMessage = CBMessage::stringToMessage(
                $imageURI
            );

            $importedImageFilepathAsCBMessage = CBMessage::stringToMessage(
                $importedImageFilepath
            );

            CBLog::log(
                (object)[
                    'message' => <<<EOT

                        The URL "{$imageURLAsCBMessage}" does not have a CBImage
                        model and the original image file
                        "{$importedImageFilepathAsCBMessage}" was found in the
                        data store but this file is not a valid image file.

                    EOT,
                    'modelID' => $imageCBID,
                    'severity' => 3,
                    'sourceClassName' => __CLASS__,
                    'sourceID' => 'dc09fcf204e9f0c1c741198fd9cfe30e1822ebcb',
                ]
            );

            return null;
        }

        $extension = image_type_to_extension(
            /* type: */ $size[2],
            /* include dot: */ false
        );

        $ID = sha1_file(
            $importedImageFilepath
        );

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

        CBDB::transaction(
            function () use (
                $spec
            ) {
                /**
                 * @NOTE 2020_11_15
                 *
                 *      We force this save because we may be resaving the model
                 *      for the original image. This code could probably be
                 *      changed to better handle this without resaving the
                 *      model.
                 */

                CBModels::save(
                    $spec,
                    /* force: */ true
                );
            }
        );

        CBDataStore::makeDirectoryForID(
            $ID
        );

        copy(
            $importedImageFilepath,
            $destinationFilepath
        );

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
