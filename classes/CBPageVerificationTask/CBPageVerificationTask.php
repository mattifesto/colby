<?php

/**
 * This task checks a page for errors and warnings. If it fixes an issue it will
 * log a message to the system log. If it doesn't fix it, it will add a message
 * to the return status and set the severity as appropriate.
 */
final class CBPageVerificationTask {

    /**
     * @return object
     */
    static function CBTask2ExecuteTask($ID) {
        $severity = 8;
        $links = [
            (object)[
                'URI' => "/admin/documents/view/?archive-id={$ID}",
                'text' => 'data store information',
            ],
            (object)[
                'URI' => "/admin/pages/preview/?ID={$ID}",
                'text' => 'preview',
            ],
        ];

        if (!CBPageVerificationTask::fetchPageDoesExist($ID)) {
            $messages[] = '(5) This page no longer exists';
            $severity = min(5, $severity);
            goto done;
        }

        $IDAsSQL = CBHex160::toSQL($ID);
        $className = CBDB::SQLToValue("SELECT `className` FROM `ColbyPages` WHERE `archiveID` = {$IDAsSQL}");

        if (empty($className)) {
            $messages[] = '(3) This page has no className';
            $severity = min(3, $severity);
            goto done;
        } else if ($className === 'CBViewPage') {
            $links[] = (object)[
                'URI' => "/admin/pages/edit/?data-store-id={$ID}",
                'text' => 'edit',
            ];
        }

        $spec = CBModels::fetchSpecByID($ID);

        /**
         * Update an old file based spec to a CBModels spec
         */

        if ($spec === false && $className === 'CBViewPage') {
            $spec = CBViewPage::fetchSpecByID($ID);

            if ($spec !== false) {
                // sometimes this isn't set on old specs
                $spec->className = 'CBViewPage';

                CBModels::save([$spec]);
                CBLog::addMessage(__CLASS__, 6, "The CBViewPage {$ID} was resaved because it did not yet have a model in the CBModels table.");

                $spec = CBModels::fetchSpecByID($ID);
            }
        }

        if ($spec === false) {
            $messages[] = '(4) This page has no spec in the CBModels table';
            $severity = min(4, $severity);
            goto done;
        }

        $versionCount = CBPageVerificationTask::fetchCountOfOldVersions($ID);

        if ($versionCount > 5) {
            CBModels::save([$spec]);
            CBLog::addMessage(__CLASS__, 6, "The CBViewPage {$ID} was resaved to remove its {$versionCount} old versions.");
        }

        if (empty($messages)) {
            $messages[] = "No issues";
        }

        done:

        return (object)[
            'message' => implode(' | ', $messages),
            'severity' => $severity,
            'links' => $links,
        ];
    }

    /**
     * This function counts versions older than 30 days because CBModels:save()
     * will remove those versions.
     *
     * @param hex160 $ID
     *
     * @return int
     */
    static function fetchCountOfOldVersions($ID) {
        $IDAsSQL = CBHex160::toSQL($ID);
        $timestamp = time() - (60 * 60 * 24 * 30); // 30 days ago
        $SQL = <<<EOT

            SELECT  COUNT(*)
            FROM    `CBModelVersions`
            WHERE   `ID` = {$IDAsSQL} and
                    `timestamp` < {$timestamp}

EOT;

        return CBDB::SQLToValue($SQL);
    }

    /**
     * @param hex160 $ID
     *
     * @return bool
     */
    static function fetchPageDoesExist($ID) {
        $IDAsSQL = CBHex160::toSQL($ID);
        return boolval(CBDB::SQLToValue("SELECT COUNT(*) FROM `ColbyPages` WHERE `archiveID` = {$IDAsSQL}"));
    }

    /**
     * Start or restart the page verification task for all existing pages.
     */
    static function startForAllPages() {
        $SQL = <<<EOT

            INSERT IGNORE INTO `CBTasks2`
            (`className`, `ID`)
            SELECT 'CBPageVerificationTask', p.archiveID
            FROM `ColbyPages` AS `p`
            LEFT OUTER JOIN `CBTasks2` as `t`
                ON `p`.`archiveID` = `t`.`ID` AND `t`.`className` = 'CBPageVerificationTask'
            ON DUPLICATE KEY UPDATE
                `completed` = NULL,
                `output` = NULL,
                `started` = NULL,
                `severity` = 8

EOT;

        Colby::query($SQL);
    }

    /**
     * @return null
     */
    static function startForAllPagesForAjax() {
        $response = new CBAjaxResponse();

        CBPageVerificationTask::startForAllPages();

        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return object
     */
    static function startForAllPagesForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * Start or restart the page verification task for new pages.
     */
    static function startForNewPages() {
        $SQL = <<<EOT

            INSERT INTO `CBTasks2`
            (`className`, `ID`)
            SELECT 'CBPageVerificationTask', p.archiveID
            FROM `ColbyPages` AS `p`
            LEFT OUTER JOIN `CBTasks2` as `t`
                ON `p`.`archiveID` = `t`.`ID` AND `t`.`className` = 'CBPageVerificationTask'
            WHERE `t`.`ID` IS NULL

EOT;

        Colby::query($SQL);
    }

    /**
     * @return null
     */
    static function startForNewPagesForAjax() {
        $response = new CBAjaxResponse();

        CBPageVerificationTask::startForNewPages();

        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return object
     */
    static function startForNewPagesForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }

}
