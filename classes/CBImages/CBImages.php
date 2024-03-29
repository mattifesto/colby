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
     * @param string $absoluteImageFilePathArgument
     *
     *      The function assumes this argument is a valid image file path. If it
     *      is not, and exception will be thrown.
     *
     * @return stdClass
     *
     *      This function will return a CBImage model that it either fetched or
     *      created for the image.
     */
    static function
    absoluteImageFilePathToImageModel(
        string $absoluteImageFilePathArgument
    ): stdClass
    {
        /**
         * Step 1:
         *
         *      Determine if this file has already been imported. If it has just
         *      return the existing image model.
         */

        $imageModelCBID =
        sha1_file(
            $absoluteImageFilePathArgument
        );

        $potentialImageModel =
        CBModels::fetchModelByCBID(
            $imageModelCBID
        );

        if (
            $potentialImageModel !== null
        ) {
            $potentialImageModelClassName =
            CBModel::getClassName(
                $potentialImageModel
            );

            if (
                $potentialImageModelClassName === 'CBImage'
            ) {
                return $potentialImageModel;
            }

            $exceptionMessage =
            CBConvert::stringToCleanLine(<<<EOT

                There exists a model with the CBID "{$imageModelCBID}", which
                would be the CBID for the image with the absolute image file
                path "{$absoluteImageFilePathArgument}" but the existing model
                is not a CBImage model. This is an odd situation that has a very
                low likelyhood of ever happening and requires investigation.

            EOT);

            throw new CBExceptionWithValue(
                $exceptionMessage,
                $absoluteImageFilePathArgument,
                'e715c2b35734b1dd0c562dd78e9f6630982ba5e5'
            );
        }



        /**
         * Step 2:
         *
         *      Atempt to import the image.
         */
        $imageSizeData =
        CBImage::getimagesize(
            $absoluteImageFilePathArgument
        );

        if (
            $imageSizeData === false
        ) {
            $exceptionMessage =
            CBConvert::stringToCleanLine(<<<EOT

                CBImage::getimagesize() returned false when called with the
                absolute image file path "{$absoluteImageFilePathArgument}" and
                therefore this is not a valid importable image.

            EOT);

            throw new CBExceptionWithValue(
                $exceptionMessage,
                $absoluteImageFilePathArgument,
                'a39946fa8e494daa80e6a1aea993798bdd5d2934'
            );
        }

        $imageHasValidType =
        in_array(
            $imageSizeData[2],
            [IMAGETYPE_GIF, IMAGETYPE_JPEG, IMAGETYPE_PNG]
        );

        if (
            !$imageHasValidType
        ) {
            $exceptionMessage =
            CBConvert::stringToCleanLine(<<<EOT

                The absolute image file path "{$imageURLAsCBMessage}" has a type
                that is not allowed to be imported.

            EOT);

            throw new CBExceptionWithValue(
                $exceptionMessage,
                $absoluteImageFilePathArgument,
                '807a5a1e35c980c7a851979d1100f1c372d1ea2a'
            );
        }

        $extension =
        image_type_to_extension(
            /* type: */ $imageSizeData[2],
            /* include dot: */ false
        );

        $filename = "original";
        $basename = "{$filename}.{$extension}";

        $destinationFilepath = CBDataStore::flexpath(
            $imageModelCBID,
            $basename,
            cbsitedir()
        );

        $imageSpec =
        (object)
        [
            'className' => 'CBImage',
            'extension' => $extension,
            'filename' => $filename,
            'height' => $imageSizeData[1],
            'ID' => $imageModelCBID,
            'width' => $imageSizeData[0],
        ];

        CBDB::transaction(
            function () use (
                $imageSpec
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
                    $imageSpec,
                    /* force: */ true
                );
            }
        );

        CBDataStore::makeDirectoryForID(
            $imageModelCBID
        );

        copy(
            $absoluteImageFilePathArgument,
            $destinationFilepath
        );

        $imageModel =
        CBModels::fetchModelByCBID(
            $imageModelCBID
        );

        return $imageModel;
    }
    // absoluteImageFilePathToImageModel()



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
     * @param CBID $imageCBID
     *
     * @return object|null
     */
    static function
    fetchRowForImageCBID(
        $imageCBID
    ): ?stdClass
    {
        $imageCBIDAsSQL = CBID::toSQL(
            $imageCBID
        );

        $SQL =
        <<<EOT

            SELECT

            LOWER(HEX(ID))
            AS CBImages_CBID_column,

            extension
            AS CBImages_extension_column,

            created
            AS CBImages_created_column,

            modified
            AS CBImages_modified_column

            FROM
            CBImages

            WHERE
            ID = ${imageCBIDAsSQL}

        EOT;

        return
        CBDB::SQLToObjectNullable(
            $SQL
        );
    }
    // fetchRowForImageCBID()



    /**
     * If an on demand image request wants to convert the image from its
     * original type to another type, this function returns the image extensions
     * that are allowed to be the extension of the on demand image.
     *
     * @return [string]
     */
    static function
    getAllowedOnDemandConversionImageExtensions(
    ): array
    {
        return [
            'webp'
        ];
    }
    /* getAllowedOnDemandConversionImageExtensions() */



    /**
     * If an on demand image resize is requested, these are the extensions that
     * are allowed to be resized. Most notable about this function is that only
     * "jpeg" is allowed, not "jpg".
     *
     * @return [string]
     */
    static function
    getAllowedOnDemandImageExtensions(
    ): array
    {
        return [
            'gif',
            'jpeg',
            'png',
            'webp',
        ];
    }
    /* getAllowedOnDemandImageExtensions() */



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

        $requestedImageResizeOperationIsAllowed = in_array(
            $requestedImageResizeOperation,
            CBSitePreferences::onDemandImageResizeOperations()
        );

        if (
            $requestedImageResizeOperationIsAllowed !== true
        ) {
            return false;
        }



        $requestedImageExtension = $pathinfo['extension'];

        $requestedImageExtensionIsAllowed = in_array(
            $requestedImageExtension,
            CBImages::getAllowedOnDemandImageExtensions()
        );

        if (
            $requestedImageExtensionIsAllowed !== true
        ) {
            return false;
        }



        /**
         * If there is no original image file, we can't generate a reduced or
         * converted image file.
         */

        $originalImageFilepath = CBImages::IDToOriginalFilepath(
            $requestedImageModelCBID
        );

        if (
            $originalImageFilepath === false
        ) {
            return false;
        }



        /**
         *  If the image is being converted, make sure conversion is allowed.
         */

        $originalImageExtension = pathinfo(
            $originalImageFilepath,
            PATHINFO_EXTENSION
        );

        if (
            $requestedImageExtension !== $originalImageExtension
        ) {
            $conversionIsAllowed = in_array(
                $requestedImageExtension,
                CBImages::getAllowedOnDemandConversionImageExtensions()
            );

            if (
                $conversionIsAllowed !== true
            ) {
                return false;
            }
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
     * @param string $imageModelCBID
     *
     * @return object|false
     *
     *      CBImage model
     */
    static function
    makeModelForID(
        string $imageModelCBID
    ) /* : mixed */
    {
        $imageModelCBIDAsSQL = CBID::toSQL(
            $imageModelCBID
        );

        $SQL = <<<EOT

            SELECT
            extension

            FROM
            CBImages

            WHERE
            ID = {$imageModelCBIDAsSQL}

        EOT;

        $imageExtension = CBDB::SQLToValue(
            $SQL
        );

        if (
            $imageExtension === false
        ) {
            return false;
        }

        $originalImageFilepath = CBDataStore::flexpath(
            $imageModelCBID,
            "original.{$imageExtension}",
            cbsitedir()
        );

        $size = CBImage::getimagesize(
            $originalImageFilepath
        );

        if (
            $size === false
        ) {
            return false;
        }

        return (object)[
            'className' =>
            'CBImage',

            'ID' =>
            $imageModelCBID,

            'extension' =>
            $imageExtension,

            'filename' =>
            'original',

            'height' =>
            $size[1],

            'width' =>
            $size[0],
        ];
    }
    /* makeModelForID() */



    /**
     * Creates a reduced image for an operation only if the reduced image
     * doesn't already exist.
     *
     * @param string $imageModelCBID
     *
     *      The image model CBID
     *
     * @param string $requestedImageExtension
     *
     *      The destination image extension
     *
     * @param string $requestedImageResizeOperation
     *
     *      The reduction operation, example: "rs200clc200"
     *
     * @return object
     *
     *      CBImage model
     */
    static function
    reduceImage(
        $imageModelCBID,
        $requestedImageExtension,
        $requestedImageResizeOperation
    ) {
        $originalImageFilepath = CBImages::IDToOriginalFilepath(
            $imageModelCBID
        );

        $originalImageExtension = pathinfo(
            $originalImageFilepath,
            PATHINFO_EXTENSION
        );

        if (
            $originalImageExtension !== $requestedImageExtension
        ) {
            $conversionIsAllowed = in_array(
                $requestedImageExtension,
                CBImages::getAllowedOnDemandConversionImageExtensions()
            );

            if (
                $conversionIsAllowed !== true
            ) {
                throw new CBExceptionWithValue(
                    CBConvert::stringToCleanLine(<<<EOT

                        The image extension "${requestedImageExtension}" is not
                        an allowed destination image extension.

                    EOT),
                    (object)[
                        'imageModelCBID' =>
                        $imageModelCBID,

                        'requestedImageExtension' =>
                        $requestedImageExtension,

                        'requestedImageResizeOperation' =>
                        $requestedImageResizeOperation
                    ],
                    '65ef3f2f6f214e4ac72f5567864377e50dcc9973'
                );
            }
        }

        $requestedImageFilepath = CBDataStore::flexpath(
            $imageModelCBID,
            "{$requestedImageResizeOperation}.{$requestedImageExtension}",
            cbsitedir()
        );

        if (
            !is_file($requestedImageFilepath)
        ) {
            $size = CBImage::getimagesize(
                $originalImageFilepath
            );

            $projection = CBProjection::withSize(
                $size[0],
                $size[1]
            );

            $projection = CBProjection::applyOpString(
                $projection,
                $requestedImageResizeOperation
            );

            CBImages::reduceImageFile(
                $originalImageFilepath,
                $requestedImageFilepath,
                $projection
            );
        }

        $size = CBImage::getimagesize(
            $requestedImageFilepath
        );

        return (object)[
            'className' =>
            'CBImage',

            'ID' =>
            $imageModelCBID,

            'extension' =>
            $requestedImageExtension,

            'filename' =>
            $requestedImageResizeOperation,

            'height' =>
            $size[1],

            'width' =>
            $size[0],
        ];
    }
    /* reduceImage() */



    /**
     * This function can be used to simply reduce an image file that may or may
     * not be a CBImage. It is used by the other functions to reduce CBImages.
     *
     * @return void
     */
    static function
    reduceImageFile(
        $sourceImageFilepath,
        $destinationImageFilepath,
        $projection,
        $args = []
    ): void
    {
        $destinationImageExtension = pathinfo(
            $destinationImageFilepath,
            PATHINFO_EXTENSION
        );

        $destinationImageExtensionIsAllowed = in_array(
            $destinationImageExtension,
            CBImages::getAllowedOnDemandImageExtensions()
        );

        if (
            $destinationImageExtensionIsAllowed !== true
        ) {
            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    The extenstion of the destinationImageFilepath argument
                    "${destinationImageExtension}" is not allowed.

                EOT),
                $destinationImageFilepath,
                'f0e1cec4e4e31e6fa5adc55d81aa9be628ced856'
            );
        }



        $quality = -1;

        extract(
            $args,
            EXTR_IF_EXISTS
        );

        ini_set(
            'memory_limit',
            '256M'
        );

        $size = CBImage::getimagesize(
            $sourceImageFilepath
        );

        $sourceImageWidth = $size[0];
        $sourceImageHeight = $size[1];
        $sourceImageType = $size[2];

        $sourceImageExtension = pathinfo(
            $sourceImageFilepath,
            PATHINFO_EXTENSION
        );

        if (
            $destinationImageExtension === $sourceImageExtension &&
            CBProjection::isNoOpForSize(
                $projection,
                $sourceImageWidth,
                $sourceImageHeight
            )
        ) {
            copy(
                $sourceImageFilepath,
                $destinationImageFilepath
            );

            return;
        }

        $src = $projection->source;
        $dst = $projection->destination;

        $output = imagecreatetruecolor(
            $dst->width,
            $dst->height
        );

        switch (
            $sourceImageType
        ) {
            case IMAGETYPE_GIF:
                $input = imagecreatefromgif(
                    $sourceImageFilepath
                );

                break;

            case IMAGETYPE_JPEG:
                $input = imagecreatefromjpeg(
                    $sourceImageFilepath
                );

                $exif = CBImage::exif_read_data(
                    $sourceImageFilepath
                );

                $orientation = (
                    empty($exif['Orientation']) ?
                    1 :
                    $exif['Orientation']
                );

                if (
                    $orientation == 3
                ) {
                    $input = imagerotate(
                        $input,
                        180,
                        0
                    );
                }

                else if (
                    $orientation == 6
                ) {
                    $input = imagerotate(
                        $input,
                        -90,
                        0
                    );
                }

                else if (
                    $orientation == 8
                ) {
                    $input = imagerotate(
                        $input,
                        90,
                        0
                    );
                }

                break;

            case IMAGETYPE_PNG:
                $input = imagecreatefrompng(
                    $sourceImageFilepath
                );

                imagealphablending(
                    $output,
                    false
                );

                imagesavealpha(
                    $output,
                    true
                );

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
                throw new CBExceptionWithValue(
                    CBConvert::stringToCleanLine(<<<EOT

                        The image type for the file "${sourceImageFilepath}" is
                        not supported.

                    EOT),
                    $sourceImageFilepath,
                    'adb83498239264db71247a97a83c16de79a2343b'
                );

                break;
        }

        imagecopyresampled(
            $output, $input,
            $dst->x,     $dst->y,      $src->x,     $src->y,
            $dst->width, $dst->height, $src->width, $src->height
        );

        switch (
            $destinationImageExtension
        ) {
            case 'gif':
                imagegif(
                    $output,
                    $destinationImageFilepath
                );

                break;

            case 'jpeg':
                imagejpeg(
                    $output,
                    $destinationImageFilepath,
                    $quality
                );

                break;

            case 'png':
                imagepng(
                    $output,
                    $destinationImageFilepath
                );

                break;

            case 'webp':
                imagewebp(
                    $output,
                    $destinationImageFilepath,
                    $quality
                );

                break;
        }
    }
    /* reduceImageFile() */



    /**
     * This function is called by CBImage::CBModels_willSave() and shouldn't
     * be called otherwise. Saving an image model will call this function.
     *
     *      CBModels::save(<imageSpec>);
     *
     * @return void
     */
    static function
    updateRow(
        $ID,
        $timestamp,
        $extension
    ): void
    {
        $extensionAsSQL =
        CBDB::escapeString(
            $extension
        );

        $extensionAsSQL =
        "'{$extension}'";

        $IDAsSQL =
        CBDB::escapeString(
            $ID
        );

        $IDAsSQL =
        "UNHEX('{$IDAsSQL}')";

        $timestampAsSQL =
        (int)$timestamp;

        $SQL =
        <<<EOT

            INSERT INTO
            CBImages

            (
                ID,
                created,
                extension,
                modified
            )

            VALUES

            (
                {$IDAsSQL},
                {$timestampAsSQL},
                {$extensionAsSQL},
                {$timestampAsSQL}
            )

            ON DUPLICATE KEY UPDATE
            extension = {$extensionAsSQL},
            modified = {$timestampAsSQL}

        EOT;

        Colby::query(
            $SQL
        );
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
     * @NOTE 2023-07-28
     * Matt Calkins
     *
     *      Today I had to look at this function with an editing eye and decided
     *      that it should probably be deprecated because it has a "magic"
     *      parameter $imageURI that can be just about any sort of value.
     *
     *      That means this function is resposible for at least two things,
     *      interpreting the vague idea of "any type of URI" and then finding an
     *      image related to that URI or creating one.
     *
     *      If I spend this much time with a function and am still uncertain it
     *      means the function is bad.
     *
     *      I created a new function on this class:
     *
     *          absoluteImageFilePathToImageModel()
     *
     *      This function only takes an absolute image file path and finds or
     *      creates an image model for it.
     *
     * @NOTE 2022_02_21
     *
     *      This function will import any image file located on the local disk.
     *      It will create a CBImage model and copy the file to the proper place
     *      that a CBImage file belongs. It will translate local URLs to local
     *      disk filepaths so it can import images from URLs of this website.
     *
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
    ): ?stdClass
    {
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

        $imageModel =
        CBImages::absoluteImageFilePathToImageModel(
            $importedImageFilepath
        );

        return $imageModel;
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
