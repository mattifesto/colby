<?php

final class CBTasks2 {

    /* Priority values are between 0 and 255 with lower numbers representing
     * higher priority.
     */
    const defaultPriority = 100;

    /* A severity value between 0 and 7 means that an issue was noted during
     * execution with the indicated RFC3164 level of severity. Values 8-255 mean
     * that no issue occurred and all have equal meaning.
     */
    const defaultSeverity = 8;

    /**
     * @return int
     */
    static function countOfAvailableTasks() {
        $SQL = <<<EOT

            SELECT  COUNT(*)
            FROM    `CBTasks2`
            WHERE   `started` IS NULL

EOT;

        return CBDB::SQLToValue($SQL);
    }

    /**
     * @return int
     */
    static function countOfScheduledTasks() {
        $SQL = <<<EOT

            SELECT  COUNT(*)
            FROM    `CBTasks2`
            WHERE   `scheduled` IS NOT NULL AND
                    `started` IS NOT NULL

EOT;

        return CBDB::SQLToValue($SQL);
    }

    /**
     * @return int
     */
    static function countOfTasksCompletedSince($timestamp) {
        $timestampAsSQL = intval($timestamp);
        $SQL = <<<EOT

            SELECT  COUNT(*)
            FROM    `CBTasks2`
            WHERE   `completed` IS NOT NULL AND
                    `completed` > {$timestampAsSQL}

EOT;

        return CBDB::SQLToValue($SQL);
    }

    /**
     * @return null
     */
    static function install() {
        $defaultPriority = CBTasks2::defaultPriority;
        $defaultSeverity = CBTasks2::defaultSeverity;
        $SQL = <<<EOT

            CREATE TABLE IF NOT EXISTS `CBTasks2` (
                `className` VARCHAR(80) NOT NULL,
                `ID` BINARY(20) NOT NULL,
                `IDForGroup` BINARY(20),

                `priority` TINYINT UNSIGNED NOT NULL DEFAULT {$defaultPriority},
                `scheduled` BIGINT,

                `started` BIGINT,
                `starter` BINARY(20),
                `completed` BIGINT,

                `output` LONGTEXT,
                `severity` TINYINT UNSIGNED NOT NULL DEFAULT {$defaultSeverity},

                PRIMARY KEY (`className`, `ID`),

                KEY `started_priority` (`started`, `priority`),
                KEY `group_started_priority` (`IDForGroup`, `started`, `priority`),

                KEY `scheduled` (`scheduled`),

                KEY `completed` (`completed`),
                KEY `group_completed` (`IDForGroup`, `completed`)
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8
            COLLATE=utf8_unicode_ci

EOT;

        Colby::query($SQL);
    }

    /**
     * @return bool
     *      Returns true if a task is completed; otherwise false.
     */
    static function dispatchNextTask() {
        $started = time();
        $starter = CBHex160::random();
        $starterAsSQL = CBHex160::toSQL($starter);
        $SQL = <<<EOT

            UPDATE      `CBTasks2`
            SET         `started` = {$started}, `starter` = {$starterAsSQL}
            WHERE       `started` IS NULL
            ORDER BY    `priority`
            LIMIT       1

EOT;

        Colby::query($SQL, /* retryOnDeadlock */ true);

        if (Colby::mysqli()->affected_rows === 1) {
            return CBTasks2::executeTaskForStarter($starter);
        } else {
            return false;
        }
    }

    /**
     * @return null
     *
     *      Ajax: {
     *          bool taskWasDispatched
     *      }
     */
    static function dispatchNextTaskForAjax() {
        $response = new CBAjaxResponse();
        $response->taskWasDispatched = CBTasks2::dispatchNextTask();
        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return object
     */
    static function dispatchNextTaskForAjaxPermissions() {
        return (object)['group' => 'Public'];
    }

    /**
     * @param string $className
     * @param hex160 $ID
     *
     * @return bool
     *      Returns true if the task is completed; otherwise false.
     */
    static function dispatchTask($className, $ID) {
        $classNameAsSQL = CBDB::stringToSQL($className);
        $IDAsSQL = CBHex160::toSQL($ID);
        $started = time();
        $starter = CBHex160::random();
        $starterAsSQL = CBHex160::toSQL($starter);
        $SQL = <<<EOT

            INSERT INTO `CBTasks2`
            (`className`, `ID`, `started`, `starter`)
            VALUES
            ({$classNameAsSQL}, {$IDAsSQL}, {$started}, {$starterAsSQL})
            ON DUPLICATE KEY UPDATE
                `started`   = {$started},
                `starter`   = {$starterAsSQL},
                `output`    = NULL,
                `completed` = NULL

EOT;

        Colby::query($SQL);

        if (Colby::mysqli()->affected_rows > 0) {
            return CBTasks2::executeTaskForStarter($starter);
        } else {
            return false;
        }
    }

    /**
     * Executes a task started by the provided starter. There are situations in
     * which a task can be pulled away from a starter and in those cases the
     * task will not be completed by that starter and this function will return
     * false.
     *
     * The situations are rare race conditions and potentially a very closely
     * timed calls to dispatchNextTask() and dispatchTask().
     *
     * @param hex160 $starter
     *
     * @return bool
     *      Returns true if the task is completed; otherwise false.
     */
    static function executeTaskForStarter($starter) {
        $starterAsSQL = CBHex160::toSQL($starter);
        $SQL = <<<EOT

            SELECT  `className`, LOWER(HEX(`ID`)) AS `ID`, `priority`, `started`
            FROM    `CBTasks2`
            WHERE   `starter` = {$starterAsSQL}

EOT;

        $task = CBDB::SQLToObject($SQL, /* retryOnDeadlock */ true);

        /**
         * If someone deleted or manually dispatched the task return false.
         */

        if ($task === false) {
            return false;
        }

        $output = (object)[
            'className' => 'CBTask2Output',
            'ID' => CBTasks2::modelID($task->className, $task->ID),
            'taskClassName' => $task->className,
            'taskID' => $task->ID,
            'priority' => $task->priority,
            'scheduled' => null,
            'started' => $task->started,
        ];

        // Task execution
        //
        // Any errors occuring in this phase will be considered a failure of the
        // selected task. The task will be marked as completed no matter what
        // happens. Sudden database failures may still ghost the task.

        try {

            if (is_callable($function = "{$task->className}::CBTasks2_Execute")) {
                $status = call_user_func($function, $task->ID);
            } else {
                throw new Exception("The function {$function}() requested by task ({$task->className}, {$task->ID}) is not callable.");
            }

            $output->links = CBModel::valueAsArray($status, 'links');
            $output->message = CBModel::value($status, 'message', 'Completed', 'strval');
            $output->scheduled = CBModel::value($status, 'scheduled', null, 'intval');
            $output->severity = CBModel::value($status, 'severity', CBTasks2::defaultSeverity, 'intval');


        } catch (Exception $exception) {

            $output->exception = Colby::exceptionStackTrace($exception);
            $output->message = $exception->getMessage();
            $output->severity = 3;

        }

        if ($output->scheduled > $output->started) {
            $scheduledAsSQL = $output->scheduled;
        } else {
            $scheduledAsSQL = 'NULL';
        }

        $classNameAsSQL = CBDB::stringToSQL($task->className);
        $IDAsSQL = CBHex160::toSQL($task->ID);
        $output->completed = time();
        $outputAsSQL = CBDB::stringToSQL(json_encode($output));

        $SQL = <<<EOT

            UPDATE  `CBTasks2`
            SET     `completed` = {$output->completed},
                    `output` = {$outputAsSQL},
                    `scheduled` = {$scheduledAsSQL},
                    `severity` = {$output->severity}
            WHERE   `className` = {$classNameAsSQL} AND
                    `ID` = {$IDAsSQL} AND
                    `starter` = {$starterAsSQL}

EOT;

        Colby::query($SQL, /* retryOnDeadlock */ true);

        if (Colby::mysqli()->affected_rows === 1) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * @param string $className
     * @param hex160 $ID
     *
     * @return hex160
     */
    static function modelID($className, $ID) {
        return sha1("CBTasks2 task with className: {$className} and ID: {$ID}");
    }

    /**
     * Creates or updates a task.
     *
     * @return null
     */
    static function updateTask($className, $ID, $IDForGroup = null, $priority = null, $scheduled = null) {
        $classNameAsSQL = CBDB::stringToSQL($className);
        $IDAsSQL = CBHex160::toSQL($ID);
        $IDForGroupAsSQL = empty($IDForGroup) ? 'NULL' : CBHex160::toSQL($IDForGroup);
        $priorityAsSQL = empty($priority) ? CBTasks2::defaultPriority : intval($priority);
        $scheduledAsSQL = empty($scheduled) ? 'NULL' : intval($scheduled);
        $updates = [];

        if (!empty($IDForGroup)) {
            $updates[] = "`IDForGroup` = {$IDForGroupAsSQL}";
        }

        if (!empty($priority)) {
            $updates[] = "`priority` = {$priorityAsSQL}";
        }

        if (!empty($scheduled)) {

            /**
             * A scheduled task can also be completed. In the reports it will
             * count as both scheduled and completed.
             */

            $updates[] = "`scheduled` = {$scheduledAsSQL}";
            $updates[] = "`started` = 0";
        }

        if (empty($updates)) {
            $updates[] = '`priority` = `priority`';
        }

        $updates = implode(',', $updates);
        $SQL = <<<EOT

            INSERT INTO `CBTasks2`
            (`className`, `ID`, `IDForGroup`, `output`, `priority`, `scheduled`)
            VALUES
            ({$classNameAsSQL}, {$IDAsSQL}, {$IDForGroupAsSQL}, NULL, {$priorityAsSQL}, {$scheduledAsSQL})
            ON DUPLICATE KEY UPDATE
            {$updates}

EOT;

        Colby::query($SQL);
    }

    /**
     * @return null
     */
    static function wakeScheduledTasks() {
        $now = time();
        $SQL = <<<EOT

            UPDATE  `CBTasks2`
            SET     `started` = NULL,
                    `scheduled` = NULL
            WHERE   `scheduled` IS NOT NULL AND
                    `scheduled` < {$now} AND
                    `started` IS NOT NULL

EOT;

        Colby::query($SQL);
    }

    /**
     * @return null
     */
    static function wakeScheduledTasksForAjax() {
        $response = new CBAjaxResponse();

        CBTasks2::wakeScheduledTasks();

        $response->wasSuccessful = true;
        $response->send();
    }

    /**
     * @return object
     */
    static function wakeScheduledTasksForAjaxPermissions() {
        return (object)['group' => 'Administrators'];
    }
}
