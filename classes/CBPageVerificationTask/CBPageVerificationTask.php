<?php

/**
 * This task checks a page for errors and warnings.
 */
final class CBPageVerificationTask {

    /**
     * @return object
     */
    static function CBTask2ExecuteTask($ID) {
        $severity = 8;
        $linkURI = '';
        $linkText = '';

        if (!CBPageVerificationTask::fetchPageDoesExist($ID)) {
            $messages[] = '(5) This page no longer exists';
            $severity = min(5, $severity);
            goto done;
        }

        $spec = CBModels::fetchSpecByID($ID);

        if ($spec === false) {
            $messages[] = '(4) This page has no spec';
            $severity = min(4, $severity);
            goto done;
        }

        $versionCount = CBPageVerificationTask::fetchCountOfVersions($ID);

        if ($versionCount > 20) {
            $messages[] = "(4) This page has a high number, {$versionCount}, of retained versions";
            $severity = min(4, $severity);
        }

        $className = CBModel::value($spec, 'className');

        if (empty($className)) {
            $messages[] = '(3) The spec has no className';
            $severity = min(3, $severity);
            goto done;
        }

        if ($className === 'CBViewPage') {
            $linkURI = "/admin/pages/edit/?data-store-id={$ID}";
            $linkText = 'edit page';
        }

        if (empty($messages)) {
            $messages[] = "Page successfully verified";
        }

        done:

        return (object)[
            'message' => implode(' | ', $messages),
            'severity' => $severity,
            'linkURI' => $linkURI,
            'linkText' => $linkText
        ];
    }

    /**
     * @param hex160 $ID
     *
     * @return int
     */
    static function fetchCountOfVersions($ID) {
        $IDAsSQL = CBHex160::toSQL($ID);
        return CBDB::SQLToValue("SELECT COUNT(*) FROM `CBModelVersions` WHERE `ID` = {$IDAsSQL}");
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
