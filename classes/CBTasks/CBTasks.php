<?php

final class CBTasks {

    /**
     * @return null
     */
    public static function doTask() {
        Colby::query('START TRANSACTION');

        $SQL = <<<EOT

            SELECT `ID`, `className`, `function`, `argsAsJSON`
            FROM `CBTasks`
            WHERE `started` IS NULL
            ORDER BY `started`, `priority`, `ID`
            LIMIT 1
            FOR UPDATE

EOT;

        $task = CBDB::SQLToObject($SQL);

        if ($task === false) {
            Colby::query('ROLLBACK');
            return;
        } else {
            $IDAsSQL = CBHex160::toSQL($task->ID);
            $time = time();
            Colby::query("UPDATE `CBTasks` SET `started` = {$time} WHERE `ID` = {$IDAsSQL}");
            Colby::query('COMMIT');
        }

        // TODO: Add more detailed error handling.

        if (is_callable($function = "{$task->className}::{$task->function}")) {
            if (empty($task->argsAsJSON)) {
                $args = null;
            } else {
                $args = json_decode($task->argsAsJSON);
            }
            call_user_func($function, $args);
        } else {
            throw new Exception("The function {$function}() requested by task {$ID} is not callable.");
        }

        Colby::query("DELETE FROM `CBTasks` WHERE ID = {$IDAsSQL}");
    }

    /**
     * @return null
     */
    public static function doTaskForAjax() {
        $response = new CBAjaxResponse();

        CBTasks::doTask();

        $priority = CBDB::SQLToValue('SELECT MIN(`priority`) FROM `CBTasks` WHERE `started` IS NOT NULL');

        if ($priority === null) {
            // don't set a timeout because there are no tasks
        } else if ($priority < 0) {
            $response->timeout = 0;
        } else if ($priority < 100) {
            $response->timeout = 5;
        } else {
            $response->timeout = 10;
        }

        $response->succeeded = true;
        $response->send();
    }

    /**
     * @return stdClass
     */
    public static function doTaskForAjaxPermissions() {
        return (object)['group' => 'Public'];
    }

    /**
     * @return null
     */
    public static function install() {
        $SQL = <<<EOT

        CREATE TABLE IF NOT EXISTS `CBTasks` (
            `ID` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            `className` VARCHAR(80),
            `function` VARCHAR(80),
            `argsAsJSON` TEXT,
            `priority` BIGINT NOT NULL DEFAULT 0,
            `started` BIGINT,
            PRIMARY KEY (`ID`),
            KEY `className_started` (`className`, `started`),
            KEY `started_priority_ID` (`started`, `priority`, `ID`)
        )
        ENGINE=InnoDB
        DEFAULT CHARSET=utf8
        COLLATE=utf8_unicode_ci

EOT;

        Colby::query($SQL);
    }
}
