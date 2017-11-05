<?php

final class CBTasks2AdminPage {

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
        return ['CBUI'];
    }

    /**
     * @return null
     */
    static function CBAjax_scheduleATask() {
        $SQL = <<<EOT

            SELECT      LOWER(HEX(`archiveID`))
            FROM        `ColbyPages`
            ORDER BY    RAND()
            LIMIT       1

EOT;

        $ID = CBDB::SQLToValue($SQL);

        CBTasks2::updateTask('CBPageVerificationTask', $ID, null, null, time() + 5);
    }

    /**
     * @return string
     */
    static function CBAjax_scheduleATask_group() {
        return 'Administrators';
    }
}
