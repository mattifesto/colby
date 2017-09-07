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
     * @param hex160 $ID
     *
     * @return  {
     *              message: string
     *              severity: int
     *          }
     */
    static function CBTasks2_Execute($ID) {
        $severity = 8;
        $title = __CLASS__ . " ($ID)";
        $messages = [];
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
                $imagesize = getimagesize($originalFilenames[0]);
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

                $message = "A model was saved for the first time for CBImage ({$ID})";
                $messages[] = $message;
                CBLog::addMessage(__CLASS__, 6, $message);
            }
        }

        array_unshift($messages, $title);

        return (object)[
            'message' => implode("\n\n", $messages),
            'severity' => $severity,
        ];
    }

    /**
     * Start or restart the image verification task for all existing images.
     */
    static function startForAllImages() {
        $SQL = <<<EOT

            INSERT IGNORE INTO `CBTasks2`
            (`className`, `ID`)
            SELECT 'CBImageVerificationTask', `i`.`ID`
            FROM `CBImages` AS `i`
            LEFT JOIN `CBTasks2` as `t`
                ON `i`.`ID` = `t`.`ID` AND `t`.`className` = 'CBImageVerificationTask'
            ON DUPLICATE KEY UPDATE
                `completed` = NULL,
                `output` = NULL,
                `started` = NULL,
                `severity` = 8

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
        $SQL = <<<EOT

            INSERT INTO `CBTasks2`
            (`className`, `ID`)
            SELECT 'CBImageVerificationTask', `i`.`ID`
            FROM `CBImages` AS `i`
            LEFT JOIN `CBTasks2` as `t`
                ON `i`.`ID` = `t`.`ID` AND `t`.`className` = 'CBImageVerificationTask'
            WHERE `t`.`ID` IS NULL

EOT;

        Colby::query($SQL);
    }
}
