<?php

final class CBImageVerificationTask {

    /**
     * @return null
     */
    static function CBAjax_startForAllImages() {
        CBImageVerificationTask::startForAllImages();
    }

    /**
     * @return string
     */
    static function CBAjax_startForAllImages_group() {
        return 'Developers';
    }

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        CBImageVerificationTask::startForAllImages();
    }

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return ['CBImages', 'CBLog', 'CBModels', 'CBPages', 'CBTasks2'];
    }

    /**
     * @param hex160 $ID
     *
     * @return null
     */
    static function CBTasks2_run($ID) {
        $severity = 7;
        $messages = [
            "Image {$ID} verified",
            "(inspect > (a /admin/?c=CBModelInspector&ID={$ID}))",
        ];
        $IDAsSQL = CBHex160::toSQL($ID);
        $rowExtension = CBDB::SQLToValue("SELECT `extension` FROM `CBImages` WHERE `ID` = {$IDAsSQL}");

        if ($rowExtension === false) {
            $severity = min(3, $severity);
            $messages[] = 'The `CBImages` row for this image no longer exists.';
        }

        $originalFilenames = glob(CBDataStore::flexpath($ID, 'original.*', cbsitedir()));

        if (($count = count($originalFilenames)) === 1) {
            $pathinfo = pathinfo($originalFilenames[0]);

            if ($rowExtension && ($pathinfo['extension'] !== $rowExtension)) {
                $severity = min(3, $severity);
                $messages[] = "The extension from the CBImages table ({$rowExtension}) does not match the extension of the original filename ({$pathinfo['extension']}).";
            }
        } else {
            $severity = min(3, $severity);
            $messages[] = "The number of original image files is {$count}. There should be one original file.";
        }

        $spec = CBModels::fetchSpecByID($ID);

        if ($spec === false) {
            $severity = min(6, $severity);
            $imagesize = CBImage::getimagesize($originalFilenames[0]);
            $spec = (object)[
                'ID' => $ID,
                'className' => 'CBImage',
                'filename' => 'original',
                'height' => $imagesize[1],
                'extension' => $rowExtension,
                'width' => $imagesize[0],
            ];

            try {
                CBDB::transaction(function () use ($spec) {
                    CBModels::save([$spec]);
                });

                $message = 'The model was saved for the first time.';
                $messages[] = $message;
            } catch (Throwable $throwable) {
                $errorMessage = CBConvert::throwableToMessage($throwable);
                $stackTrace = CBConvert::throwabletoStackTrace($throwable);
                $severity = min(3, $severity);
                $messages[] = <<<EOT

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

        CBLog::log((object)[
            'className' => __CLASS__,
            'ID' => $ID,
            'message' => implode("\n\n", $messages),
            'severity' => $severity,
        ]);
    }

    /**
     * Start or restart the image verification task for all existing images.
     */
    static function startForAllImages() {
        $SQL = <<<EOT

            SELECT LOWER(HEX(CBImages.ID)) as ID
            FROM CBImages

EOT;

        $IDs = CBDB::SQLToArray($SQL);

        CBTasks2::restart(__CLASS__, $IDs, 101);
    }
}
