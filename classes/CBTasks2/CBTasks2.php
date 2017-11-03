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
     * @param object $args
     *
     *      {
     *          processID: hex160?
     *      }
     *
     * @return int
     */
    static function countOfAvailableTasks($args = null) {
        $processID = CBModel::value($args, 'processID', null, 'CBConvert::valueAsHex160');

        if (empty($processID)) {
            $andProcessID = '';
        } else {
            $processIDAsSQL = CBHex160::toSQL($processID);
            $andProcessID = "AND `processID` = {$processIDAsSQL}";
        }

        $SQL = <<<EOT

            SELECT  COUNT(*)
            FROM    `CBTasks2`
            WHERE   `started` IS NULL
                    {$andProcessID}

EOT;

        return CBDB::SQLToValue($SQL);
    }

    /**
     * Returns a count of completed tasks in the CBTasks2 table.
     *
     * @param object $args
     *
     *      {
     *          afterTimestamp: int?
     *          processID: hex160?
     *      }
     *
     * @return int (is it an int or a string?)
     */
    static function countOfCompletedTasks($args = null) {
        $processID = CBModel::value($args, 'processID', null, 'CBConvert::valueAsHex160');

        if (empty($processID)) {
            $andProcessID = '';
        } else {
            $processIDAsSQL = CBHex160::toSQL($processID);
            $andProcessID = "AND `processID` = {$processIDAsSQL}";
        }

        $SQL = <<<EOT

            SELECT  COUNT(*)
            FROM    `CBTasks2`
            WHERE   `completed` IS NOT NULL
                    {$andProcessID}

EOT;

        return CBDB::SQLToValue($SQL);
    }

    /**
     * Returns a count of scheduled tasks in the CBTasks2 table.
     *
     * @param object $args
     *
     *      {
     *          processID: hex160?
     *      }
     *
     * @return int (is it an int or a string?)
     */
    static function countOfScheduledTasks($args = null) {
        $processID = CBModel::value($args, 'processID', null, 'CBConvert::valueAsHex160');

        if (empty($processID)) {
            $andProcessID = '';
        } else {
            $processIDAsSQL = CBHex160::toSQL($processID);
            $andProcessID = "AND `processID` = {$processIDAsSQL}";
        }

        $SQL = <<<EOT

            SELECT  COUNT(*)
            FROM    `CBTasks2`
            WHERE   `scheduled` IS NOT NULL AND
                    `started` IS NOT NULL
                    {$andProcessID}

EOT;

        return CBDB::SQLToValue($SQL);
    }

    /**
     * Returns a count of tasks in the CBTasks2 table.
     *
     * @param object $args
     *
     *      {
     *          processID: hex160?
     *      }
     *
     * @return int (is it an int or a string?)
     */
    static function countOfTasks($args = null) {
        $processID = CBModel::value($args, 'processID', null, 'CBConvert::valueAsHex160');

        if (empty($processID)) {
            $andProcessID = '';
        } else {
            $processIDAsSQL = CBHex160::toSQL($processID);
            $andProcessID = "AND `processID` = {$processIDAsSQL}";
        }

        $SQL = <<<EOT

            SELECT  COUNT(*)
            FROM    `CBTasks2`
            WHERE   TRUE
                    {$andProcessID}

EOT;

        return CBDB::SQLToValue($SQL);
    }

    /**
     * @NOTE This function counts tasks completed in log history, not completed
     * tasks in the CBTasks2 table.
     *
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
     * @param object $args
     *
     *      {
     *          processID: hex160?
     *      }
     *
     *  @return object
     *
     *      {
     *          available: int
     *          completed: int
     *          scheduled: int
     *          total: int
     *      }
     */
    static function fetchStatus($args) {
        $processID = CBModel::value($args, 'processID', null, 'CBConvert::valueAsHex160');

        $fnArgs = (object)[
            'processID' => $processID,
        ];

        return (object)[
            'avalailable' => CBTasks2::countOfAvailableTasks($fnArgs),
            'completed' => CBTasks2::countOfCompletedTasks($fnArgs),
            'scheduled' => CBTasks2::countOfScheduledTasks($fnArgs),
            'total' => CBTasks2::countOfTasks($fnArgs),
        ];
    }

    /**
     * @param object $args
     *
     *      {
     *          processID: hex160?
     *      }
     *
     *  @return object
     *
     *      {
     *          available: int
     *          completed: int
     *          scheduled: int
     *          total: int
     *      }
     */
    static function CBAjax_fetchStatus($args) {
        $processID = CBModel::value($args, 'processID', null, 'CBConvert::valueAsHex160');

        return CBTasks2::fetchStatus((object)[
            'processID' => $processID,
        ]);
    }

    /**
     * @return string
     */
    static function CBAjax_fetchStatus_group() {
        return 'Administrators';
    }

    /**
     *  The `processID` column is used to associate related tasks with each
     *  other.
     *
     *  The `output` column should hold JSON.
     *
     *  Task States:
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
                `processID` BINARY(20),

                `priority` TINYINT UNSIGNED NOT NULL DEFAULT {$defaultPriority},
                `scheduled` BIGINT,

                `started` BIGINT,
                `starter` BINARY(20),
                `completed` BIGINT,

                `output` LONGTEXT,
                `severity` TINYINT UNSIGNED NOT NULL DEFAULT {$defaultSeverity},

                PRIMARY KEY (`className`, `ID`),

                KEY `started_priority` (`started`, `priority`),
                KEY `processID_started_priority` (`processID`, `started`, `priority`),
                KEY `scheduled` (`scheduled`),

                KEY `completed` (`completed`),
                KEY `processID_completed` (`processID`, `completed`)
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_520_ci

EOT;

        Colby::query($SQL);
    }

    /**
     * @param object $args
     *
     *      {
     *          processID: hex160?
     *      }
     *
     * @return bool
     *
     *      Returns true if a task is completed; otherwise false.
     */
    static function dispatchNextTask($args) {
        $processID = CBModel::value($args, 'processID', null, 'CBConvert::valueAsHex160');

        if (!empty($processID)) {
            $processIDAsSQL = CBHex160::toSQL($processID);
            $andProcessID = "AND `processID` = {$processIDAsSQL}";
        } else {
            $andProcessID = '';
        }

        $started = time();
        $starter = CBHex160::random();
        $starterAsSQL = CBHex160::toSQL($starter);
        $SQL = <<<EOT

            UPDATE      `CBTasks2`
            SET         `started` = {$started}, `starter` = {$starterAsSQL}
            WHERE       `started` IS NULL
                        {$andProcessID}
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
     * @param object $args
     *
     *      {
     *          processID: hex160?
     *      }
     *
     * @return object
     *
     *      {
     *          taskWasDispatched: bool
     *      }
     */
    static function CBAjax_dispatchNextTask($args) {
        $processID = CBModel::value($args, 'processID', null, 'CBConvert::valueAsHex160');
        $taskWasDispatched = CBTasks2::dispatchNextTask((object)[
            'processID' => $processID,
        ]);

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

            SELECT  `className`,
                    LOWER(HEX(`ID`)) AS `ID`,
                    LOWER(HEX(`processID`)) as `processID`,
                    `priority`,
                    `started`
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

        if ($task->processID !== null) {
            CBProcess::setID($task->processID);
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

            if (is_callable($function = "{$task->className}::CBTasks2_execute")) {
                $status = call_user_func($function, $task->ID);
            } else if (is_callable($function = "{$task->className}::CBTasks2_Execute")) { /* deprecated */
                $status = call_user_func($function, $task->ID);
            } else {
                throw new Exception("The function {$task->className}::CBTasks2_execute() requested by task ({$task->className}, {$task->ID}) is not callable.");
            }

            $output->links = CBModel::valueAsArray($status, 'links');
            $output->message = CBModel::value($status, 'message', '', 'strval');
            $output->scheduled = CBModel::value($status, 'scheduled', null, 'intval');
            $output->severity = CBModel::value($status, 'severity', CBTasks2::defaultSeverity, 'intval');

        } catch (Throwable $throwable) {

            $output->exception = Colby::exceptionStackTrace($throwable);
            $output->message = CBConvert::throwableToMessage($throwable);
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

        $affectedRows = Colby::mysqli()->affected_rows;

        CBLog::addMessage("CBTasks2_TaskCompleted_{$task->className}", 7, "{$output->message}", $output);

        if ($task->processID !== null) {
            CBProcess::clearID();
        }

        if ($affectedRows === 1) {
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
     * See updateTasks
     */
    static function updateTask($className, $ID, $processID = null, $priority = null, $scheduled = null) {
        return CBTasks2::updateTasks($className, [$ID], $processID, $priority, $scheduled);
    }

    /**
     * This function is use to both create and update a task.
     *
     * @param string $className
     *
     *      This is the class name of the class that implements the
     *      CBTasks2_execute() function which will be called to perform the
     *      task.
     *
     * @param hex160 $ID
     *
     *      This is an $ID that is associated with the data of the task. A task
     *      that operated on a page would use a page ID. A task that operated on
     *      a model would use a model ID. A task that operates as a singleton
     *      would usually use CBHex160::zero() for this value.
     *
     * @param hex160 $processID
     *
     *      This is used to identify a process of related tasks. For instance
     *      when performing a spreadsheet import, all rows of the spreadsheet
     *      would be turned into tasks and all of those tasks and generated
     *      subtasks would be given the same process ID. This allows you to
     *      track the status of the spreadsheet import or cancel it if
     *      necessary.
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
    static function updateTasks($className, array $IDs, $processID = null, $priority = null, $scheduled = null) {
        $classNameAsSQL = CBDB::stringToSQL($className);
        $IDsAsSQL = array_map('CBHex160::toSQL', $IDs);
        $updates = [];

        if (($value = $processID) || ($value = CBProcess::ID())) {
            $processIDAsSQL = CBHex160::toSQL($value);
            $updates[] = "`processID` = {$processIDAsSQL}";
        } else {
            $processIDAsSQL = 'NULL';
        }

        if ($priority !== null) {
            $priorityAsSQL = intval($priority);
            $updates[] = "`priority` = {$priorityAsSQL}";
        } else {
            $priorityAsSQL = CBTasks2::defaultPriority;
        }

        /**
         * @NOTE 2017.11.01
         *
         *      Before today tasks were allowed to be both available and
         *      completed at the same time. This was a very clever way of saying
         *      that a task had been completed in the past, but was now either
         *      available or scheduled to be completed again in the future.
         *
         *      This created two concepts: tasks that had ever been completed,
         *      and tasks that were not available or scheduled that had been
         *      completed. This was complicated and non-obvious in situations
         *      where you needed to know the difference.
         *
         *      A change was made today so that if a task is available or
         *      scheduled it is marked as not completed. This means if a task
         *      completes and reschedules itself, it will not show as completed.
         *      This is okay because apparently the ask is actually not fully
         *      completed.
         *
         *      The truth is, some tasks run regularly but never truly complete.
         *      Other tasks run a number of times then complete.
         */

        if ($scheduled !== null) {
            $scheduledAsSQL = intval($scheduled);

            if ($scheduledAsSQL <= time()) {
                /* remove completed, scheduled, and started */
                $updates[] = "`completed` = NULL";
                $updates[] = "`scheduled` = NULL";
                $updates[] = "`started` = NULL";
            } else {
                /* remove completed, replace scheduled and make the task unavailable */
                $updates[] = "`completed` = NULL";
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
            $values[] = "({$classNameAsSQL}, {$IDAsSQL}, {$processIDAsSQL}, NULL, {$priorityAsSQL}, {$scheduledAsSQL})";
        }

        $values = implode(',', $values);

        /**
         * INSERT ON DUPLICATE KEY UPDATE can be used here because `CBTasks2`
         * has only one unique key. If this changes a temporary table must be
         * used.
         */

        $SQL = <<<EOT

            INSERT INTO `CBTasks2`
            (`className`, `ID`, `processID`, `output`, `priority`, `scheduled`)
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
