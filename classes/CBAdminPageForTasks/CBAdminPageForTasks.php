<?php

final class CBAdminPageForTasks {

    /**
     * @return [string]
     */
    static function adminPageMenuNamePath() {
        return ['general', 'tasks'];
    }

    /**
     * @return stdClass
     */
    static function adminPagePermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @return null
     */
    static function adminPageRenderContent() {
        CBHTMLOutput::setTitleHTML('Tasks');
        CBHTMLOutput::setDescriptionHTML('View tasks');
    }

    /**
     * @return null
     *
     *      Ajax: {
     *          recent: [object]
     *          upcoming: [object]
     *      }
     */
    static function fetchIssuesForAjax() {
        $response = new CBAjaxResponse();

        $SQL = 'SELECT `output` FROM CBTasks2 WHERE `completed` IS NOT NULL AND `severity` < 8 ORDER BY `severity`';
        $response->issues = CBDB::SQLToArray($SQL, ['valueIsJSON' => true]);

        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return object
     */
    static function fetchIssuesForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @return null
     *
     *      Ajax: {
     *          recent: [object]
     *          upcoming: [object]
     *      }
     */
    static function fetchStatusForAjax() {
        $response = new CBAjaxResponse();

        $response->countOfAvailableTasks = CBTasks2::countOfAvailableTasks();
        $response->countOfScheduledTasks = CBTasks2::countOfScheduledTasks();
        $response->countOfTasksCompletedInTheLastMinute = CBTasks2::countOfTasksCompletedSince(time() - 60);
        $response->countOfTasksCompletedInTheLastHour = CBTasks2::countOfTasksCompletedSince(time() - (60 * 60));
        $response->countOfTasksCompletedInTheLast24Hours = CBTasks2::countOfTasksCompletedSince(time() - (60 * 60 * 24));

        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return object
     */
    static function fetchStatusForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_CSSURLs() {
        return [Colby::flexpath(__CLASS__, 'css', cbsysurl())];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs() {
        return [Colby::flexpath(__CLASS__, 'js', cbsysurl())];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames() {
        return ['CBUIExpander'];
    }

    /**
     * @return null
     */
    static function scheduleATaskForAjax() {
        $response = new CBAjaxResponse();

        $SQL = <<<EOT

            SELECT      LOWER(HEX(`archiveID`))
            FROM        `ColbyPages`
            ORDER BY    RAND()
            LIMIT       1

EOT;

        $ID = CBDB::SQLToValue($SQL);

        CBTasks2::updateTask('CBPageVerificationTask', $ID, null, null, time() + 5);

        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return object
     */
    static function scheduleATaskForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }
}
