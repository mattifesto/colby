<?php

final class CBArtwork {

    /* -- CBHTMLOutput interfaces -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v632.js', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
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
    static function CBModel_build(
        stdClass $artworkSpec
    ): stdClass {
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

        if ($imageSpec !== null) {
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
     * @param object $artworkModel
     *
     * @return stdClass|null
     */
    static function getImageModel(
        stdClass $artworkModel
    ): ?stdClass {
        return CBModel::valueAsModel(
            $artworkModel,
            'image'
        );
    }
    /* getImageModel() */



    /**
     * @param object $artworkModel
     *
     * @return string
     *
     *      Returns an empty string if no URL is available.
     */
    static function getThumbnailImageURL(
        $artworkModel
    ): string {
        $imageURL = '';

        $image = CBModel::valueAsModel(
            $artworkModel,
            'image'
        );

        if ($image !== null) {
            $imageURL = CBImage::asFlexpath(
                $image,
                'rl320',
                cbsiteurl()
            );

            if ($imageURL !== '') {
                return $imageURL;
            }
        }

        $imageURL = CBModel::valueToString(
            $artworkModel,
            'thumbnailImageURL'
        );

        if ($imageURL !== '') {
            return $imageURL;
        }

        $imageURL = CBModel::valueToString(
            $artworkModel,
            'mediumImageURL'
        );

        if ($imageURL !== '') {
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
