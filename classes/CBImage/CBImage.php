<?php

final class
CBImage
{
    // -- CBAdmin_CBDocumentationForClass interfaces



    /**
     * @return void
     */
    static function
    CBAdmin_CBDocumentationForClass_render(
    )
    : void
    {
        include_once(
            __DIR__ . '/CBImage_Documentation.php'
        );

        CBImage_Documentation::render();
    }
    /* CBAdmin_CBDocumentationForClass_render() */



    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array
    {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.61.4.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_requiredClassNames(
    ): array
    {
        return [
            'CBConvert',
            'CBDataStore',
            'CBException',
            'CBModel',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBModel interfaces -- -- -- -- -- */



    /**
     * @param object $spec
     *
     * @return object
     */
    static function
    CBModel_build(
        stdClass $spec
    ): stdClass
    {
        $filename = CBModel::valueToString(
            $spec,
            'filename'
        );

        if (
            $filename === ''
        ) {
            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    This spec can't be built because it has an invalid
                    "filename" property value.

                EOT),
                $spec,
                '2bc70fc87dd47cb4707395b4af6225b5cf2f0acd'
            );
        }

        $height = CBModel::valueAsInt(
            $spec,
            'height'
        );

        if (
            $height === null ||
            $height < 1
        ) {
            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    This spec can't be built because it has an invalid
                    "height" property value.

                EOT),
                $spec,
                'c6b95f62d68542095335ef2175689dc1cb4f2b90'
            );
        }

        $imageCBID = CBModel::valueAsCBID(
            $spec,
            'ID'
        );

        if (
            $imageCBID === null
        ) {
            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    This spec can't be built because it has an invalid
                    "ID" property value.

                EOT),
                $spec,
                'ed759d15d41064ae2a3659639298383e6c429b9c'
            );
        }

        $originalWidth = CBImage::getOriginalWidth(
            $spec
        );

        if (
            $originalWidth === null ||
            $originalWidth < 1
        ) {
            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    This spec can't be built because it has an invalid original
                    width.

                EOT),
                $spec,
                'c2bedac1eb80124931939a28d82b60e570658c5d'
            );
        }

        $imageModel = (object)[
            'filename' =>
            $filename,

            'ID' =>
            $imageCBID,
        ];

        CBImage::setExtension(
            $imageModel,
            CBImage::getExtension(
                $spec
            )
        );

        CBImage::setOriginalHeight(
            $imageModel,
            $height
        );

        CBImage::setOriginalWidth(
            $imageModel,
            $originalWidth
        );

        return $imageModel;
    }
    /* CBModel_build() */



    /**
     * @param object $spec
     *
     * @return object
     */
    static function
    CBModel_upgrade(
        stdClass $spec
    ): stdClass
    {
        if (
            empty($spec->filename) &&
            !empty($spec->base)
        ) {
            $spec->filename = $spec->base;

            unset(
                $spec->base
            );
        }

        return $spec;
    }
    /* CBModel_upgrade() */



    /**
     * Deleting images is a process that should rarely happen. Images should be
     * kept forever even if they are no longer used. However there are various
     * development and administrative reasons to delete images.
     *
     * @param [CBID] $IDs
     *
     * @return void
     */
    static function
    CBModels_willDelete(
        array $IDs
    ): void
    {
        foreach (
            $IDs as $ID
        ) {
            CBImages::deleteByID(
                $ID
            );
        }
    }
    /* CBModels_willDelete() */



    /**
     * @param [object] $models
     *
     * @return null
     */
    static function
    CBModels_willSave(
        array $models
    ) {
        foreach (
            $models as $model
        ) {
            CBImages::updateRow(
                $model->ID,
                time(),
                $model->extension
            );
        }
    }
    /* CBModels_willSave() */



    /* -- accessors -- */



    /**
     * @param object $imageModel
     *
     * @return string
     */
    static function
    getExtension(
        stdClass $imageModel
    ): string
    {
        return
        CBModel::valueToString(
            $imageModel,
            'extension'
        );
    }
    // getExtension()



    /**
     * @param object $imageModel
     * @param string $newExtension
     *
     * @return void
     */
    static function
    setExtension(
        stdClass $imageModel,
        string $newExtension
    ): void
    {
        $isNewExensionAllowed =
        in_array(
            $newExtension,
            CBImage::getAllowedImageExtensions()
        );

        if (
            $isNewExensionAllowed !== true
        ) {
            throw new CBExceptionWithValue(
                CBConvert::stringToCleanLine(<<<EOT

                    The image extension "${newExtension}" is not allowed and
                    can't be set as the extension property value on this image
                    model.

                EOT),
                $imageModel,
                '068a2e5026d748280a93275244cead131935da7b'
            );
        }

        $imageModel->extension = $newExtension;
    }
    // setExtension()



    /**
     * @param object $imageModel
     *
     * @return int|null
     */
    static function
    getOriginalHeight(
        stdClass $imageModel
    ): ?int
    {
        return CBModel::valueAsInt(
            $imageModel,
            'height'
        );
    }
    /* getOriginalHeight() */



    /**
     * @param object $imageModel
     * @param int $originalWidth
     *
     * @return void
     */
    static function
    setOriginalHeight(
        stdClass $imageModel,
        int $originalHeight
    ): void
    {
        $imageModel->height =
        $originalHeight;
    }
    /* setOriginalHeight() */



    /**
     * @param object $imageModel
     *
     * @return int|null
     */
    static function
    getOriginalWidth(
        stdClass $imageModel
    ): ?int
    {
        return CBModel::valueAsInt(
            $imageModel,
            'width'
        );
    }
    /* getOriginalWidth() */



    /**
     * @param object $imageModel
     * @param int $originalWidth
     *
     * @return void
     */
    static function
    setOriginalWidth(
        stdClass $imageModel,
        int $originalWidth
    ): void
    {
        $imageModel->width = $originalWidth;
    }
    /* setOriginalWidth() */



    /* -- functions -- */



    /**
     * @param model $image
     * @param string $filename
     *
     *      If not specified, the filename from the $image model will be used.
     *
     *      Examples: 'original', 'rw320', 'rw1280'
     *
     * @param string $flexdir
     *
     *      cbsitedir() or cbsiteurl()
     *
     * @return ?string
     *
     *      This function will return null if the required properties are not
     *      available.
     */
    static function
    asFlexpath(
        stdClass $image,
        string $filename = '',
        string $flexdir = '',
        string $requestedExtension = '',
    ): ?string
    {
        $ID = CBModel::valueAsID(
            $image,
            'ID'
        );

        if (
            empty($ID)
        ) {
            return null;
        }

        $extension = CBConvert::valueAsName(
            $requestedExtension
        );

        if (
            $extension === null
        ) {
            $extension = CBModel::valueToString(
                $image,
                'extension'
            );
        }

        if (
            empty($extension)
        ) {
            return null;
        }

        if (
            empty($filename)
        ) {
            $filename = CBModel::valueToString(
                $image,
                'filename'
            );

            if (empty($filename)) {
                return null;
            }
        }

        return CBDataStore::flexpath(
            $ID,
            "{$filename}.{$extension}",
            $flexdir
        );
    }
    /* asFlexpath() */



    /**
     * @param float $maximumDisplayWidthInCSSPixels
     * @param float $maximumDisplayHeightInCSSPixels
     * @param float $aspectRatioWidth
     * @param float $aspectRatioHeight
     *
     * @return float
     */
    static function
    calculateFloatingPointMaximumImageStyleWidthInCSSPixels(
        float $maximumDisplayWidthInCSSPixels,
        float $maximumDisplayHeightInCSSPixels,
        float $aspectRatioWidth,
        float $aspectRatioHeight
    ): float
    {
        /**
         * If the image were displayed at its maximum height, what would the
         * width be.
         */

        $widthInCSSPixelsAtMaximumHeight =
        $maximumDisplayHeightInCSSPixels *
        (
            $aspectRatioWidth /
            $aspectRatioHeight
        );

        $maximumImageStyleWidthInCSSPixels =
        min(
            $widthInCSSPixelsAtMaximumHeight,
            $maximumDisplayWidthInCSSPixels
        );

        return $maximumImageStyleWidthInCSSPixels;
    }
    // calculateFloatingPointMaximumImageStyleWidthInCSSPixels()



    /**
     * @NOTE 2022_07_11
     *
     *      This function may be deprecated in the future because it returns an
     *      integer. Modern browsers can handle fractional pixels and in certain
     *      cases a fractional return value avoids visual issues related to the
     *      image not taking up the full height that is should.
     *
     *      Use calculateFloatingPointMaximumImageStyleWidthInCSSPixels() first
     *      and if that doesn't work for some reason, document the reason here
     *      which will be a reason not to deprecate this function.
     *
     *      If no reasons for this function are found, it will eventually be
     *      deprecated.
     *
     * @param int $maximumDisplayWidthInCSSPixels
     * @param int $maximumDisplayHeightInCSSPixels
     * @param int aspectRatioWidth
     * @param int aspectRatioHeight
     *
     * @return int
     */
    static function
    calculateMaximumImageStyleWidthInCSSPixels(
        int $maximumDisplayWidthInCSSPixels,
        int $maximumDisplayHeightInCSSPixels,
        int $aspectRatioWidth,
        int $aspectRatioHeight
    ): int
    {
        /**
         * If the image were displayed at its maximum height, what would the
         * width be.
         */

        $widthInCSSPixelsAtMaximumHeight =
        $maximumDisplayHeightInCSSPixels *
        (
            $aspectRatioWidth /
            $aspectRatioHeight
        );

        $maximumImageStyleWidthInCSSPixels =
        min(
            $widthInCSSPixelsAtMaximumHeight,
            $maximumDisplayWidthInCSSPixels
        );

        return $maximumImageStyleWidthInCSSPixels;
    }
    // calculateMaximumImageStyleWidthInCSSPixels()



    /**
     * Use the function instead of exif_read_data() because exif_read_data()
     * throws errors for many image files.
     *
     * @param string $filepath
     *
     * @return [string => mixed]|false
     */
    static function
    exif_read_data(
        $filepath
    ) // -> array|false
    {
        try {
            return exif_read_data(
                $filepath
            );
        } catch (
            Throwable $throwable
        ) {
            return false;
        }
    }
    /* exif_read_data() */



    /**
     * Early CBImage specs did not have a className so this special function is
     * designed to ensure a spec does have a className and then upgrade the
     * spec.
     *
     * @param object $spec
     *
     * @return object
     */
    static function
    fixAndUpgrade(
        stdClass $spec
    ): stdClass
    {
        if (
            empty($spec->className)
        ) {
            $spec = CBModel::clone(
                $spec
            );

            $spec->className = 'CBImage';
        }

        return CBModel::upgrade(
            $spec
        );
    }
    /* fixAndUpgrade() */



    /**
     * @return [string]
     */
    static function
    getAllowedImageExtensions(
    ): array
    {
        return [
            'gif',
            'jpeg',
            'png',
            'webp',
        ];
    }
    // getAllowedImageExtensions()



    /**
     * Use this function instead of getimagesize() because it properly returns
     * width and height for images that are rotated via the Orientation EXIF
     * property.
     *
     * @return [int => mixed]|false
     */
    static function
    getimagesize(
        string $filepath
    ) // -> array|false
    {
        try
        {
            $data = getimagesize(
                $filepath
            );
        }

        catch (
            Throwable $throwable
        ) {
            CBErrorHandler::report(
                $throwable
            );

            return false;
        }

        if (
            !is_array($data)
        ) {
            return false;
        }

        if (
            $data[2] == IMG_JPEG
        ) {
            $exif = CBImage::exif_read_data(
                $filepath
            );

            $orientation = (
                empty(
                    $exif['Orientation']
                ) ?
                1 :
                $exif['Orientation']
            );

            if (
                $orientation == 6 ||
                $orientation == 9
            ) {
                $width = $data[0];      // store width
                $data[0] = $data[1];    // set width to height
                $data[1] = $width;      // set height to width
                $data[3] = "width=\"{$data[0]}\" height=\"{$data[1]}\"";
            }
        }

        return $data;
    }
    /* getimagesize() */



    /**
     * @param object|string $imageModelOrURLArgument
     * @param string $imageResizeOperation
     * @param int $maximumBoxWidthInCSSPixels
     * @param int $maximumBoxHeightInCSSPixels
     * @param string $alternativeText
     *
     * @return void
     */
    static function
    renderPictureElementWithImageInsideAspectRatioBox(
        /* object|string| */ $imageModelOrURLArgument,
        string $imageResizeOperation,
        int $maximumBoxWidthInCSSPixels,
        int $maximumBoxHeightInCSSPixels,
        string $alternativeText = ''
    ): void
    {
        echo
        CBConvert::stringToCleanLine(<<<EOT

            <picture

                class =
                "CBImage_renderPictureElementWithImageInsideAspectRatioBox"

            >

        EOT);

        $imageModel =
        CBConvert::valueAsObject(
            $imageModelOrURLArgument
        );

        if (
            $imageModel !==
            null
        ) {
            $originalImageExtension =
            CBImage::getExtension(
                $imageModel
            );

            if (
                $originalImageExtension !== 'webp'
            ) {
                $webpImageURL =
                CBImage::asFlexpath(
                    $imageModel,
                    $imageResizeOperation,
                    cbsiteurl(),
                    'webp'
                );

                echo
                CBConvert::stringToCleanLine(<<<EOT

                    <source
                    srcset="${webpImageURL}"
                    type="image/webp"
                    >

                EOT);
            }

            $imageURL =
            CBImage::asFlexpath(
                $imageModel,
                $imageResizeOperation,
                cbsiteurl()
            );
        }
        // if

        else
        {
            $imageURL =
            CBConvert::valueToString(
                $imageModelOrURLArgument
            );
        }
        // else



        $alternativeTextAsHTML =
        cbhtml(
            $alternativeText
        );

        echo
        CBConvert::stringToCleanLine(<<<EOT

            <img
                src =
                "${imageURL}"

                alt =
                "${alternativeTextAsHTML}"

                style=
                "
                    aspect-ratio:
                    ${maximumBoxWidthInCSSPixels}
                    /
                    ${maximumBoxHeightInCSSPixels};

                    display:
                    block;

                    height:
                    auto;

                    max-width:
                    100%;

                    object-fit:
                    contain;

                    width:
                    ${maximumBoxWidthInCSSPixels}px;
                "
            >

        EOT);


        echo
        "</picture>";
    }
    // renderPictureElementWithImageInsideAspectRatioBox()



    /**
     * @param object $imageModel
     * @param string $imageResizeOperation
     * @param int $maximumDisplayWidthInCSSPixels
     * @param int $maximumDisplayHeightInCSSPixels
     * @param string $alternativeText
     *
     * @return void
     */
    static function
    renderPictureElementWithMaximumDisplayWidthAndHeight(
        stdClass $imageModel,
        string  $imageResizeOperation,
        int $maximumDisplayWidthInCSSPixels,
        int $maximumDisplayHeightInCSSPixels,
        string $alternativeText = ''
    ): void
    {
        echo
        CBConvert::stringToCleanLine(<<<EOT

            <picture

                class =
                "CBImage_renderPictureElementWithMaximumDisplayWidthAndHeight"

            >

        EOT);

        $originalImageExtension =
        CBImage::getExtension(
            $imageModel
        );

        if (
            $originalImageExtension !== 'webp'
        ) {
            $webpImageURL =
            CBImage::asFlexpath(
                $imageModel,
                $imageResizeOperation,
                cbsiteurl(),
                'webp'
            );

            echo
            CBConvert::stringToCleanLine(<<<EOT

                <source
                srcset="${webpImageURL}"
                type="image/webp"
                >

            EOT);
        }

        $imageURL =
        CBImage::asFlexpath(
            $imageModel,
            $imageResizeOperation,
            cbsiteurl()
        );

        $intrinsicImageWidth = CBImage::getOriginalWidth(
            $imageModel
        );

        $intrinsicImageHeight = CBImage::getOriginalHeight(
            $imageModel
        );

        $alternativeTextAsHTML =
        cbhtml(
            $alternativeText
        );

        $maximumImageStyleWidthInCSSPixels =
        CBImage::calculateFloatingPointMaximumImageStyleWidthInCSSPixels(
            $maximumDisplayWidthInCSSPixels,
            $maximumDisplayHeightInCSSPixels,
            $intrinsicImageWidth,
            $intrinsicImageHeight
        );

        echo
        CBConvert::stringToCleanLine(<<<EOT

            <img

                src =
                "${imageURL}"

                width =
                "${intrinsicImageWidth}"

                height =
                "${intrinsicImageHeight}"

                alt =
                "${alternativeTextAsHTML}"

                style=
                "
                    display:
                    block;

                    height:
                    auto;

                    max-width:
                    100%;

                    width:
                    ${maximumImageStyleWidthInCSSPixels}px;
                "
            >

        EOT);


        echo
        "</picture>";
    }
    // renderPictureElementWithMaximumDisplayWidthAndHeight()



    /**
     * @deprecated 2022_05_28
     *
     *      I can't find a caller of this function and do not know why it
     *      exists. It's basically the same as:
     *
     *      CBImage::renderPictureElementWithMaximumDisplayWidthAndHeight()
     *
     *      but won't create a responsive image.
     *
     *      I think it's probably a left over function from when the CBImage
     *      rendering functions were added which was a tumultuous time for the
     *      code in this class.
     *
     *      If you find a purpose for this, document it here. Otherwise delete
     *      this function eventually.
     *
     * @param object $imageModel
     * @param string $imageResizeOperation
     * @param int $imageWidth
     * @param int $imageHeight
     * @param string $alternativeText
     *
     * @return void
     */
    static function
    renderPictureElementWithSize(
        stdClass $imageModel,
        string $imageResizeOperation,
        int $imageWidth,
        int $imageHeight,
        string $alternativeText = ''
    ): void
    {
        echo
        CBConvert::stringToCleanLine(<<<EOT

            <picture

                class =
                "CBImage_renderPictureElementWithSize"

            >

        EOT);

        $originalImageExtension =
        CBImage::getExtension(
            $imageModel
        );

        if (
            $originalImageExtension !== 'webp'
        ) {
            $webpImageURL =
            CBImage::asFlexpath(
                $imageModel,
                $imageResizeOperation,
                cbsiteurl(),
                'webp'
            );

            echo
            CBConvert::stringToCleanLine(<<<EOT

                <source
                srcset="${webpImageURL}"
                type="image/webp"
                >

            EOT);
        }

        $imageURL =
        CBImage::asFlexpath(
            $imageModel,
            $imageResizeOperation,
            cbsiteurl()
        );

        $alternativeTextAsHTML =
        cbhtml(
            $alternativeText
        );

        echo
        CBConvert::stringToCleanLine(<<<EOT

            <img

                src =
                "${imageURL}"

                width =
                "${imageWidth}"

                height =
                "${imageHeight}"

                alt =
                "${alternativeTextAsHTML}"

            >

        EOT);


        echo
        "</picture>";
    }
    // renderPictureElementWithSize()



    /**
     * This function is similar to the CBModel::value... functions.
     *
     * @TODO 2018_03_01
     *
     *      1. Rename to valueAsFlexpath and return ?string instead of false
     *      2. Rename $operation parameter to $filename
     *      3. Use CBImage::asFlexpath()
     *
     * @param object $model
     * @param string $keyPath
     * @param string? $operation
     * @param string? $flexdir
     *
     * @return string|false
     */
    static function
    valueToFlexpath(
        stdClass $model,
        $keyPath,
        $operation = null,
        $flexdir = null
    ) // -> string|false
    {
        $ID = CBModel::value(
            $model,
            "{$keyPath}.ID"
        );

        if (
            !CBID::valueIsCBID($ID)
        ) {
            return false;
        }

        $extension = CBModel::value(
            $model,
            "{$keyPath}.extension"
        );

        if (
            empty($extension)
        ) {
            return false;
        }

        if (
            $operation
        ) {
            $filename = $operation;
        }

        else {
            $filename = CBModel::value(
                $model,
                "{$keyPath}.filename"
            );

            if (
                empty($filename)
            ) {
                $filename = CBModel::value(
                    $model,
                    "{$keyPath}.base"
                ); // deprecated

                if (
                    empty($filename)
                ) {
                    return false;
                }
            }
        }

        return CBDataStore::flexpath(
            $ID,
            "{$filename}.{$extension}",
            $flexdir
        );
    }
    /* valueToFlexpath() */

}
