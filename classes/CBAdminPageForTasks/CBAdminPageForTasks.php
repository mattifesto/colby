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
     * @return [object]
     */
    static function CBAjax_fetchIssues() {
        $SQL = <<<EOT

            SELECT      `output`
            FROM        `CBTasks2`
            WHERE       `completed` IS NOT NULL AND
                        `severity` < 8
            ORDER BY    `severity`

EOT;

        return CBDB::SQLToArray($SQL, ['valueIsJSON' => true]);
    }

    /**
     * @return string
     */
    static function CBAjax_fetchIssues_group() {
        return 'Administrators';
    }

    /**
     * @return  {
     *              countOfAvailableTasks: int
     *              countOfScheduledTasks: int
     *              countOfTasksCompletedInTheLastMinute: int
     *              countOfTasksCompletedInTheLastHour: int
     *              countOfTasksCompletedInTheLast24Hours: int
     *          }
     */
    static function CBAjax_fetchStatus() {
        return (object)[
            'countOfAvailableTasks' => CBTasks2::countOfAvailableTasks(),
            'countOfScheduledTasks' => CBTasks2::countOfScheduledTasks(),
            'countOfTasksCompletedInTheLastMinute' => CBTasks2::countOfTasksCompletedSince(time() - 60),
            'countOfTasksCompletedInTheLastHour' => CBTasks2::countOfTasksCompletedSince(time() - (60 * 60)),
            'countOfTasksCompletedInTheLast24Hours' => CBTasks2::countOfTasksCompletedSince(time() - (60 * 60 * 24)),
        ];
    }

    /**
     * @return string
     */
    static function CBAjax_fetchStatus_group() {
        return 'Administrators';
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
