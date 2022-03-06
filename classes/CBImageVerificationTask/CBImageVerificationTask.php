<?php

final class
CBImageVerificationTask
{
    /* -- CBAjax interfaces -- */



    /**
     * @return void
     */
    static function
    CBAjax_startForAllImages(
    ): void
    {
        CBImageVerificationTask::startForAllImages();
    }
    // CBAjax_startForAllImages()



    /**
     * @return string
     */
    static function
    CBAjax_startForAllImages_getUserGroupClassName(
    ): string
    {
        return 'CBDevelopersUserGroup';
    }
    // CBAjax_startForAllImages_getUserGroupClassName()



    /* -- CBInstall interfaces -- */



    /**
     * @return void
     */
    static function
    CBInstall_install(
    ): void
    {
        CBImageVerificationTask::startForAllImages();
    }
    // CBInstall_install()



    /**
     * @return [string]
     */
    static function
    CBInstall_requiredClassNames(
    ): array {
        return [
            'CBImages',
            'CBLog',
            'CBModels',
            'CBPages',
            'CBTasks2',
        ];
    }
    // CBInstall_requiredClassNames()



    /* -- CBTasks2 interfaces -- */



    /**
     * @param ID $ID
     *
     * @return void
     */
    static function
    CBTasks2_run(
        string $ID
    ): void
    {
        $severity =
        7;

        $messages =
        [
            "Image {$ID} verified",
            "(inspect > (a /admin/?c=CBModelInspector&ID={$ID}))",
        ];

        $IDAsSQL =
        CBID::toSQL(
            $ID
        );

        $SQL =
        <<<EOT

            SELECT
            extension

            FROM
            CBImages

            WHERE
            ID = {$IDAsSQL}

        EOT;

        $CBImagesTableFileExtension =
        CBDB::SQLToValue(
            $SQL
        );

        if (
            $CBImagesTableFileExtension === false
        ) {
            $severity =
            min(
                3,
                $severity
            );

            $messages[] =
            'The `CBImages` row for this image no longer exists.';
        }

        $originalFilenames =
        glob(
            CBDataStore::flexpath(
                $ID,
                'original.*',
                cbsitedir()
            )
        );

        $originalFilenamesCount =
        count(
            $originalFilenames
        );

        if (
            $originalFilenamesCount === 1
        ) {
            $pathinfo =
            pathinfo(
                $originalFilenames[0]
            );

            $fileExtension =
            $pathinfo[
                'extension'
            ];

            if (
                $CBImagesTableFileExtension &&
                ($fileExtension !== $CBImagesTableFileExtension)
            ) {
                CBImageVerificationTask::reportOriginalImageFileExtensionMismatch(
                    $ID,
                    $fileExtension,
                    $CBImagesTableFileExtension
                );

                /**
                 * If the two extensions don't match an administrator should
                 * investigate and either fix the issue or manually remove the
                 * image if necessary.
                 */

                return;
            }
        }

        else if (
            $originalFilenamesCount === 0
        ) {
            CBImageVerificationTask::reportNoOriginalImageFile(
                $ID
            );

            /**
             * The presence of no original image file is an odd situation. An
             * administrator should inspect the ID and be able to manually
             * remove it if necessary.
             */

            return;
        }

        else if (
            $originalFilenamesCount > 1
        ) {
            CBImageVerificationTask::reportMultipleOriginalImageFiles(
                $ID
            );

            /**
             * The presence of multiple original image files is an odd
             * situation. An administrator should inspect the ID and be able to
             * manually remove it if necessary.
             */

            return;
        }

        $spec =
        CBModels::fetchSpecByID(
            $ID
        );

        if (
            empty($spec)
        ) {
            $severity =
            min(
                6,
                $severity
            );

            $imagesize =
            CBImage::getimagesize(
                $originalFilenames[0]
            );

            if (
                $imagesize === false
            ) {
                array_push(
                    $messages,
                    CBConvert::stringToCleanLine(<<<EOT

                        CBImage::getimagesize() returned false for the original
                        image filepath.

                    EOT)
                );

                CBImages::deleteByID(
                    $ID
                );

                CBDataStore::deleteByID(
                    $ID
                );

                goto done;
            }

            $spec =
            (object)[
                'ID' =>
                $ID,

                'className' =>
                'CBImage',

                'filename' =>
                'original',

                'height' =>
                $imagesize[1],

                'extension' =>
                $CBImagesTableFileExtension,

                'width' =>
                $imagesize[0],
            ];

            try
            {
                CBDB::transaction(
                    function () use (
                        $spec
                    ) {
                        CBModels::save(
                            $spec
                        );
                    }
                );

                $message =
                'The model was saved for the first time.';

                $messages[] =
                $message;
            }

            catch (
                Throwable $throwable
            ) {
                $errorMessage =
                CBConvert::throwableToMessage(
                    $throwable
                );

                $stackTrace =
                CBConvert::throwabletoStackTrace(
                    $throwable
                );

                $severity =
                min(
                    3,
                    $severity
                );

                $messages[] =
                <<<EOT

                    --- h1
                    CBModels::save(\$spec) Failed
                    ---

                    {$errorMessage}

                    --- pre preline
                    {$stackTrace}
                    ---

                EOT;
            }
        }

        done:

        CBLog::log(
            (object)[
                'message' =>
                implode(
                    "\n\n",
                    $messages
                ),

                'modelID' =>
                $ID,

                'severity' =>
                $severity,

                'sourceClassName' =>
                __CLASS__,

                'sourceID' =>
                'f9c392f85b3593c9df5a3fc065ea12cce789c4db',
            ]
        );
    }
    /* CBTasks2_run() */



    /* -- functions -- */



    /**
     * @param CBID $ID
     *
     * @return void
     */
    private static function
    reportMultipleOriginalImageFiles(
        string $ID
    ): void
    {
        $message =
        <<<EOT

            Multiple original image files were found for the CBImage with the ID
            ($ID(code)).

        EOT;

        CBLog::log(
            (object)[
                'message' =>
                $message,

                'modelID' =>
                $ID,

                'severity' =>
                3,

                'sourceClassName' =>
                __CLASS__,

                'sourceID' =>
                '8c15c3984a4779b8740321033e5844142edac22e',
            ]
        );
    }
    /* reportMultipleOriginalImageFiles() */



    /**
     * @param CBID $ID
     *
     * @return void
     */
    private static function
    reportNoOriginalImageFile(
        string $ID
    ): void
    {
        $message =
        <<<EOT

            There was no original image file found for the CBImage with the ID
            ($ID(code)).

            An administrator should inspect this model and delete it manually if
            the data store files available are not in working order.

        EOT;

        CBLog::log(
            (object)[
                'message' =>
                $message,

                'modelID' =>
                $ID,

                'severity' =>
                3,

                'sourceClassName' =>
                __CLASS__,

                'sourceID' =>
                '020a81f55950e037072dc1a168c6ca4db890a347',
            ]
        );
    }
    /* reportNoOriginalImageFile() */



    /**
     * @param ID $ID
     * @param string $extension
     *
     *      The extension of the original image file.
     *
     * @param string $CBImagesTableFileExtension
     *
     *      The extension stored in the CBImages table.
     *
     * @return void
     */
    private static function
    reportOriginalImageFileExtensionMismatch(
        string $ID,
        string $fileExtension,
        string $CBImagesTableFileExtension
    ): void
    {
        $fileExtensionAsMessage =
        CBMessageMarkup::stringToMessage(
            $fileExtension
        );

        $CBImagesTableFileExtensionAsMessage =
        CBMessageMarkup::stringToMessage(
            $CBImagesTableFileExtension
        );

        $message =
        <<<EOT

            The extension of the original image file,
            "{$fileExtensionAsMessage}", does no match the extension stored in
            the CBImages table, "{$CBImagesTableFileExtensionAsMessage}", for
            the CBImage with the ID ($ID (code)).

        EOT;

        CBLog::log(
            (object)[
                'message' =>
                $message,

                'modelID' =>
                $ID,

                'severity' =>
                3,

                'sourceClassName' =>
                __CLASS__,

                'sourceID' =>
                '87ab5b91d861c43c3e06ac72b9c315ad049928f0',
            ]
        );
    }
    /* reportOriginalImageFileExtensionMismatch() */



    /**
     * Start or restart the image verification task for all existing images.
     *
     * @return void
     */
    static function
    startForAllImages(
    ): void
    {
        $SQL =
        <<<EOT

            SELECT
            LOWER(HEX(ID)) as ID

            FROM
            CBImages

        EOT;

        $IDs =
        CBDB::SQLToArray(
            $SQL
        );

        CBTasks2::restart(
            __CLASS__,
            $IDs,
            101
        );
    }
    /* startForAllImages() */

}
