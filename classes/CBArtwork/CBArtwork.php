<?php

/**
 * @NOTE 2022_10_09_1665326440
 *
 *      This class is problematic. The original intention of the class was never
 *      documented. Furthermore, I'm pretty sure this class was already
 *      partially deprecated without adding documentation.
 *
 *      Possible Purpose 1:
 *
 *          This class could represent either a local image or an image
 *          represented by a few remote image URLs representing image versions
 *          of various sizes.
 *
 *          Issue: We are past the point where image URLs can reasonably
 *          represent images and we don't have any known use cases for this that
 *          aren't moving to CBImage models. If a use case is found, document
 *          it; othewise deprecate this class.
 *
 *      Possible Purpose 2:
 *
 *          While writing this documentation, there existed a one sentence
 *          documentation file for this class that suggested it should represent
 *          the combination of an image, its alternative text, and its caption.
 *
 *          Issue: This class has been around a while and it has never held
 *          alternative text or a caption. If this is the purpose, I would think
 *          there would be more references to it. I removed that documentaton
 *          file.
 */
final class
CBArtwork
{
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
                'v675.60.js',
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
    ): array {
        return [
            'CBImage',
            'CBModel',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBModel interfaces -- */



    /**
     * @param object $artworkSpec
     *
     * @return object
     */
    static function
    CBModel_build(
        stdClass $artworkSpec
    ): stdClass
    {
        $imageSpec = CBModel::valueAsModel(
            $artworkSpec,
            'image',
            [
                'CBImage',
            ]
        );

        $imageModel = null;
        $thumbnailImageURL = null;
        $mediumImageURL = null;
        $largeImageURL = null;

        if (
            $imageSpec !== null
        ) {
            $imageModel = CBModel::build(
                $imageSpec
            );
        } else {
            $thumbnailImageURL = trim(
                CBModel::valueToString(
                    $artworkSpec,
                    'thumbnailImageURL'
                )
            );

            $mediumImageURL = trim(
                CBModel::valueToString(
                    $artworkSpec,
                    'mediumImageURL'
                )
            );

            $largeImageURL = trim(
                CBModel::valueToString(
                    $artworkSpec,
                    'largeImageURL'
                )
            );
        }

        return (object)[
            'image' => $imageModel,
            'thumbnailImageURL' => $thumbnailImageURL,
            'mediumImageURL' => $mediumImageURL,
            'largeImageURL' => $largeImageURL,
        ];
    }
    /* CBModel_build() */



    /* -- functions -- */



    /**
     * @param object $imageSpec
     *
     * @return object
     */
    static function
    fromImageSpec(
        stdClass $imageSpec
    ): stdClass
    {
        $artworkSpec = CBModel::createSpec(
            'CBArtwork'
        );

        CBArtwork::setImageModel(
            $artworkSpec,
            $imageSpec
        );

        return $artworkSpec;
    }
    /* fromImageSpec() */



    /**
     * @param object $artworkModel
     *
     * @return object|null
     */
    static function
    getImageModel(
        stdClass $artworkModel
    ): ?stdClass
    {
        return CBModel::valueAsModel(
            $artworkModel,
            'image'
        );
    }
    /* getImageModel() */



    /**
     * @param object $artworkModel
     * @param object $imageModel
     *
     * @return void
     */
    static function
    setImageModel(
        stdClass $artworkModel,
        stdClass $imageModel
    ): void
    {
        $artworkModel->image = $imageModel;
    }
    /* setImageModel() */



    /**
     * @param object $artworkModel
     *
     * @return string
     *
     *      Returns an empty string if no URL is available.
     */
    static function
    getThumbnailImageURL(
        $artworkModel
    ): string
    {
        $imageURL = '';

        $image = CBModel::valueAsModel(
            $artworkModel,
            'image'
        );

        if (
            $image !== null
        ) {
            $imageURL = CBImage::asFlexpath(
                $image,
                'rl320',
                cbsiteurl()
            );

            if (
                $imageURL !== ''
            ) {
                return $imageURL;
            }
        }

        $imageURL = CBModel::valueToString(
            $artworkModel,
            'thumbnailImageURL'
        );

        if (
            $imageURL !== ''
        ) {
            return $imageURL;
        }

        $imageURL = CBModel::valueToString(
            $artworkModel,
            'mediumImageURL'
        );

        if (
            $imageURL !== ''
        ) {
            return $imageURL;
        }

        $imageURL = CBModel::valueToString(
            $artworkModel,
            'largeImageURL'
        );

        return $imageURL;
    }
    /* getThumbnailImageURL() */

}
