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

    private static $groupIDStack = [];

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
            FROM    `CBLog`
            WHERE   `category` = 'CBTasks2_TaskCompleted' AND
                    `timestamp` > {$timestampAsSQL}

EOT;

        return CBDB::SQLToValue($SQL);
    }

    /**
     *  @NOTE The `IDForGroup` is used to associate related tasks with each
     *        other.
     *
     *      Issue: This column is not part of the primary key and it's possible
     *      to reassign the group of a task that was part of another group.
     *
     * @NOTE The `output` column should hold JSON.
     *
     * @NOTE Task States:
     *
     *      Available: `started` IS NULL
     *      Scheduled: `scheduled` IS NOT NULL AND `started` IS NOT NULL
     *      Completed: `completed` IS NOT NULL
     *
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
     * This ajax function will dispatch the next task if one exists. If one
     * doesn't exist it will attempt to wake any scheduled tasks.
     *
     * @return  {
     *              taskWasDispatched: bool
     *          }
     */
    static function CBAjax_dispatchNextTask() {
        $taskWasDispatched = CBTasks2::dispatchNextTask();

        if (!$taskWasDispatched) {
            CBTasks2::wakeScheduledTasks();
        }

        return (object)[
            'taskWasDispatched' => $taskWasDispatched,
        ];
    }

    /**
     * @return string
     */
    static function CBAjax_dispatchNextTask_group() {
        return 'Public';
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
            $message = CBModel::value($status, 'message', '', 'strval');
            $hint = CBModel::value($status, 'hint', '', 'strval');
            if ($hint) { $hint = " ({$hint})"; }
            $output->message = "{$task->className} Completed{$hint}\n{$task->ID}\n\n{$message}";
            $output->scheduled = CBModel::value($status, 'scheduled', null, 'intval');
            $output->severity = CBModel::value($status, 'severity', CBTasks2::defaultSeverity, 'intval');

        } catch (Exception $exception) {

            $output->exception = Colby::exceptionStackTrace($exception);
            $message = $exception->getMessage();
            $output->message = "{$task->className} Failed\n{$task->ID}\n\n{$message}";
            $output->severity = 3;

        }

        $output->completed = time();
        $startedAsSQL = '`started`';

        if ($output->scheduled === null) {
            $scheduledAsSQL = 'NULL';
        } else if ($output->scheduled <= $output->completed) {
            $scheduledAsSQL = 'NULL';
            $startedAsSQL = 'NULL';
        } else {
            $scheduledAsSQL = $output->scheduled;
        }

        $classNameAsSQL = CBDB::stringToSQL($task->className);
        $IDAsSQL = CBHex160::toSQL($task->ID);
        $outputAsSQL = CBDB::stringToSQL(json_encode($output));

        $SQL = <<<EOT

            UPDATE  `CBTasks2`
            SET     `completed` = {$output->completed},
                    `output` = {$outputAsSQL},
                    `scheduled` = {$scheduledAsSQL},
                    `severity` = {$output->severity},
                    `started` = {$startedAsSQL}
            WHERE   `className` = {$classNameAsSQL} AND
                    `ID` = {$IDAsSQL} AND
                    `starter` = {$starterAsSQL}

EOT;

        Colby::query($SQL, /* retryOnDeadlock */ true);

        CBLog::addMessage('CBTasks2_TaskCompleted', 7, "{$output->message}", $output);

        if (Colby::mysqli()->affected_rows === 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param hex160 $groupID
     *
     * @return int
     */
    static function groupIDPush($groupID) {
        if (CBHex160::is($groupID)) {
            return array_push(CBTasks2::$groupIDStack, $groupID);
        } else {
            throw new InvalidArgumentException('$groupID');
        }
    }

    /**
     * @return hex160|null
     */
    static function groupIDPop() {
        array_pop(CBTasks2::$groupIDStack);
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
     * See updateTasks
     */
    static function updateTask($className, $ID, $IDForGroup = null, $priority = null, $scheduled = null) {
        return CBTasks2::updateTasks($className, [$ID], $IDForGroup, $priority, $scheduled);
    }

    /**
     * This function is use to both create and update a task.
     *
     * @param string $className
     *
     *      This is the class name of the class that implements the
     *      CBTasks2_Execute() function which will be called to perform the
     *      task.
     *
     * @param hex160 $ID
     *
     *      This is an $ID that is associated with the data of the task. A task
     *      that operated on a page would use a page ID. A task that operated on
     *      a model would use a model ID. A task that operates as a singleton
     *      would usually use CBHex160::zero() for this value.
     *
     * @param hex160 $IDForGroup
     *
     *      This is used to identify a group of related tasks. For instance when
     *      performing a spreadsheet import, all rows of the spreadsheet would
     *      be turned into tasks and all of those tasks and generated subtasks
     *      would be given the same $IDForGroup. This allows you to track the
     *      status of the spreadsheet import or cancel it if necessary.
     *
     *      When this is specified, it will usually be a random ID.
     *
     * @param int $priority
     *
     *      Priority values are between 0 and 255 with lower numbers
     *      representing higher priority. The default priority is 100.
     *
     * @param int (timestamp) $scheduled
     *
     *      If a value is provided that's less than or equal to time() then the
     *      task will be made available. This is how you restart a task. If the
     *      value is greater than time() the task will be scheduled.
     *
     *      A null value will make a new task available and will have no effect
     *      on an existing task's availability.
     *
     * @return null
     */
    static function updateTasks($className, array $IDs, $IDForGroup = null, $priority = null, $scheduled = null) {
        $classNameAsSQL = CBDB::stringToSQL($className);
        $IDsAsSQL = array_map('CBHex160::toSQL', $IDs);
        $updates = [];

        if (($value = $IDForGroup) || ($value = end(CBTasks2::$groupIDStack))) {
            $IDForGroupAsSQL = CBHex160::toSQL($value);
            $updates[] = "`IDForGroup` = {$IDForGroupAsSQL}";
        } else {
            $IDForGroupAsSQL = 'NULL';
        }

        if ($priority !== null) {
            $priorityAsSQL = intval($priority);
            $updates[] = "`priority` = {$priorityAsSQL}";
        } else {
            $priorityAsSQL = CBTasks2::defaultPriority;
        }

        if ($scheduled !== null) {
            $scheduledAsSQL = intval($scheduled);

            if ($scheduledAsSQL <= time()) {
                /* remove scheduled and make the task available */
                $updates[] = "`scheduled` = NULL";
                $updates[] = "`started` = NULL";
            } else {
                /* replace scheduled and make the task unavailable */
                $updates[] = "`scheduled` = {$scheduledAsSQL}";
                $updates[] = "`started` = 0";
            }
        } else {
            $scheduledAsSQL = 'NULL';
        }

        if (empty($updates)) {
            /* placeholder update for INSERT ON DUPLICATE KEY UPDATE */
            $updates[] = '`priority` = `priority`';
        }

        $updates = implode(',', $updates);
        $values = [];

        foreach ($IDsAsSQL as $IDAsSQL) {
            $values[] = "({$classNameAsSQL}, {$IDAsSQL}, {$IDForGroupAsSQL}, NULL, {$priorityAsSQL}, {$scheduledAsSQL})";
        }

        $values = implode(',', $values);

        /**
         * INSERT ON DUPLICATE KEY UPDATE can be used here because `CBTasks2`
         * has only one unique key. If this changes a temporary table must be
         * used.
         */

        $SQL = <<<EOT

            INSERT INTO `CBTasks2`
            (`className`, `ID`, `IDForGroup`, `output`, `priority`, `scheduled`)
            VALUES
            {$values}
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
}
