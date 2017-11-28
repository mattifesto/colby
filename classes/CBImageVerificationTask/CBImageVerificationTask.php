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
     * @return null
     */
    static function CBAjax_startForNewImages() {
        CBImageVerificationTask::startForNewImages();
    }

    /**
     * @return string
     */
    static function CBAjax_startForNewImages_group() {
        return 'Developers';
    }

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        CBImageVerificationTask::startForNewImages();
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
        $severity = 8;
        $messages = ['Image Verification Task'];
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
            if ($severity === 8) {
                $imagesize = CBImage::getimagesize($originalFilenames[0]);
                $spec = (object)[
                    'ID' => $ID,
                    'className' => 'CBImage',
                    'filename' => 'original',
                    'height' => $imagesize[1],
                    'extension' => $rowExtension,
                    'width' => $imagesize[0],
                ];

                CBDB::transaction(function () use ($spec) {
                    CBModels::save([$spec]);
                });

                $message = 'The model was saved for the first time.';
                $messages[] = $message;
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
        $now = time();
        $SQL = <<<EOT

            INSERT IGNORE INTO `CBTasks2`
            (`className`, `ID`, `state`, `timestamp`)
            SELECT 'CBImageVerificationTask', `i`.`ID`, 1, {$now}
            FROM `CBImages` AS `i`
            LEFT JOIN `CBTasks2` as `t`
                ON `i`.`ID` = `t`.`ID` AND `t`.`className` = 'CBImageVerificationTask'
            ON DUPLICATE KEY UPDATE
                `state` = 1,
                `timestamp` = {$now}

EOT;

        Colby::query($SQL);

        $SQL = <<<EOT

            DELETE      `CBTasks2`
            FROM        `CBTasks2`
            LEFT JOIN   `CBImages`
            ON          `CBTasks2`.`ID` = `CBImages`.`ID`
            WHERE       `CBTasks2`.`className` = 'CBImageVerificationTask' AND
                        `CBImages`.`ID` IS NULL

EOT;

        Colby::query($SQL);
    }

    /**
     * Start the image verification task for new pages.
     */
    static function startForNewImages() {
        $now = time();
        $SQL = <<<EOT

            INSERT INTO `CBTasks2`
            (`className`, `ID`, `state`, `timestamp`)
            SELECT 'CBImageVerificationTask', `i`.`ID`, 1, {$now}
            FROM `CBImages` AS `i`
            LEFT JOIN `CBTasks2` as `t`
                ON `i`.`ID` = `t`.`ID` AND `t`.`className` = 'CBImageVerificationTask'
            WHERE `t`.`ID` IS NULL

EOT;

        Colby::query($SQL);
    }
}
