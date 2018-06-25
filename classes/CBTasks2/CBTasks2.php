<?php

final class CBTasks2 {

    /**
     * Priority values are between 0 and 255 with lower numbers representing
     * higher priority.
     */
    const defaultPriority = 100;

    /**
     * @param object $args
     *
     *      {
     *          processID: hex160?
     *      }
     *
     *  @return [int => object]
     *
     *      {
     *          state: int
     *          count: int
     *      }
     */
    static function CBAjax_fetchStatus($args) {
        return CBTasks2::fetchStatus($args);
    }

    /**
     * @return string
     */
    static function CBAjax_fetchStatus_group() {
        return 'Administrators';
    }

    /**
     * This ajax function will run the next ready task if one exists. If one
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
     *          taskWasRun: bool
     *      }
     */
    static function CBAjax_runNextTask($args) {
        $processID = CBModel::value($args, 'processID', null, 'CBConvert::valueAsHex160');
        $taskWasRun = CBTasks2::runNextTask((object)[
            'processID' => $processID,
        ]);

        if (!$taskWasRun) {
            CBTasks2::wakeScheduledTasks();
        }

        return (object)[
            'taskWasRun' => $taskWasRun,
        ];
    }

    /**
     * @return string
     */
    static function CBAjax_runNextTask_group() {
        return 'Public';
    }

    /**
     * @param object $args
     *
     *      {
     *          processID: hex160?
     *      }
     *
     *  @return [int => object]
     *
     *      {
     *          state: int
     *          count: int
     *      }
     */
    static function fetchStatus($args) {
        $processID = CBModel::value($args, 'processID', null, 'CBConvert::valueAsHex160');

        if ($processID !== null) {
            $processIDAsSQL = CBHex160::toSQL($processID);
            $where = "WHERE `processID` = {$processIDAsSQL}";
        } else {
            $where = '';
        }

        $SQL = <<<EOT

            SELECT      `state`, COUNT(*) AS `count`
            FROM        `CBTasks2`
            {$where}
            GROUP BY    `state`

EOT;

        $states = CBDB::SQLToObjects($SQL, ['keyField' => 'state']);
        $scheduled = isset($states[0]) ? intval($states[0]->count) : 0;
        $ready = isset($states[1]) ? intval($states[1]->count) : 0;
        $running = isset($states[2]) ? intval($states[2]->count) : 0;
        $complete = isset($states[3]) ? intval($states[3]->count) : 0;
        $failed = isset($states[4]) ? intval($states[4]->count) : 0;

        return (object)[
            'scheduled' => $scheduled,
            'ready' => $ready,
            'running' => $running,
            'complete' => $complete,
            'failed' => $failed,
            'total' => $scheduled + $ready + $running + $complete + $failed,
        ];
    }

    /**
     * Table columns:
     *
     *      `className`
     *
     *          The class name of the class that runs the task. This class must
     *          implement the CBTasks2_run() interface.
     *
     *      `ID`
     *
     *          This is the ID of the model that is the target of the task. For
     *          instance and "upgrade model" task would be created for multiple
     *          models.
     *
     *          For singleton tasks, the tasks should declare a unique ID that
     *          it will always use. If the task needs to store data, it can
     *          store it in a model with this ID. But a model does not need to
     *          be created.
     *
     *          This ID will be passed to the CBTasks2_run() interface.
     *
     *      `priority`
     *
     *          0-255 with lower numbers representing higher priority.
     *
     *          default: 100
     *
     *      `state`
     *
     *          0: scheduled
     *          1: ready
     *          2: running
     *          3: complete
     *          4: failed
     *
     *      `timestamp`
     *
     *          state 0: time running scheduled to start
     *          state 1: time made ready to start
     *          state 2: time running started
     *          state 3: time running finished
     *          state 4: time failure occurred
     *
     *      `processID`
     *
     *          A process ID is used to group related tasks. This allows you to
     *          track the status of a set of tasks or cancel all of the tasks if
     *          necessary.
     *
     *          For instance, when performing a model import, the import process
     *          sets a process ID using the CBProcess class. All of the tasks
     *          created then become part of that process. When a task is run
     *          that is part of a process, other tasks created during the run
     *          will also be part of that process.
     *
     *          To create a process use the CBProcess class to set the process
     *          ID to a unique random ID. Then create some tasks and nothing
     *          needs to be done to maintain the process after that.
     *
     *      `starterID`
     *
     *          Used by the this class when running tasks.
     *
     * Table indexes:
     *
     *      `className`, `ID`
     *
     *          Primary key. Only one task can exist per className and ID. If
     *          the task needs to be run again, restart it.
     *
     *      `state`, `priority`
     *
     *          Used to find the next of all tasks to run.
     *
     *      `processID`, `state`, `priority`
     *
     *          Used to find the next of the tasks in a process to run.
     *
     *      `state`, `timestamp`
     *
     *          Used to find scheduled tasks that can be moved to the ready
     *          state.
     *
     *      `starterID`
     *
     *          Used by the class when running tasks.
     *
     * @return void
     */
    static function CBInstall_install(): void {
        $defaultPriority = CBTasks2::defaultPriority;
        $SQL = <<<EOT

            CREATE TABLE IF NOT EXISTS `CBTasks2` (
                `className` VARCHAR(80) NOT NULL,
                `ID`        BINARY(20) NOT NULL,
                `priority`  TINYINT UNSIGNED NOT NULL DEFAULT {$defaultPriority},
                `state`     TINYINT UNSIGNED NOT NULL,
                `timestamp` BIGINT NOT NULL,

                `processID` BINARY(20),
                `starterID` BINARY(20),

                PRIMARY KEY (`className`, `ID`),
                KEY `state_priority` (`state`, `priority`),
                KEY `processID_state_priority` (`processID`, `state`, `priority`),
                KEY `state_timestamp` (`state`, `timestamp`),
                KEY `starterID` (`starterID`)
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_520_ci

EOT;

        Colby::query($SQL);
    }

    /**
     * When a task is run a log entry is made so CBLog is required for this
     * class to be fully installed.
     *
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return ['CBLog'];
    }

    /**
     * @param string $className
     * @param [hex160]|hex160 $IDs
     *
     * @return void
     */
    static function remove($className, $IDs): void {
        if (!is_array($IDs)) {
            $IDs = [$IDs];
        }

        $classNameAsSQL = CBDB::stringToSQL($className);
        $IDsAsSQL = CBHex160::toSQL($IDs);
        $SQL = <<<EOT

            DELETE
            FROM    CBTasks2
            WHERE   className = {$classNameAsSQL} AND
                    ID IN ($IDsAsSQL)

EOT;

        Colby::query($SQL);
    }

    /**
     * @param string $className
     * @param [hex160]|hex160 $IDs
     * @param ?int $priority
     * @param int $delayInSeconds
     *
     * @return void
     */
    static function restart(string $className, $IDs, ?int $priority = null, int $delayInSeconds = 0): void {
        if (!is_array($IDs)) {
            $IDs = [$IDs];
        }

        if ($delayInSeconds < 0) {
            $delayInSeconds = 0;
        }

        $scheduled = time() + $delayInSeconds;
        $processID = null;

        CBTasks2::updateTasks($className, $IDs, $processID, $priority, $scheduled);
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
     *      Returns true if a task is run; otherwise false.
     */
    static function runNextTask($args) {
        $processID = CBModel::valueAsID($args, 'processID');

        if (!empty($processID)) {
            $processIDAsSQL = CBHex160::toSQL($processID);
            $andProcessID = "AND `processID` = {$processIDAsSQL}";
        } else {
            $andProcessID = '';
        }

        $timestamp = time();
        $starterID = CBHex160::random();
        $starterIDAsSQL = CBHex160::toSQL($starterID);
        $SQL = <<<EOT

            UPDATE      `CBTasks2`
            SET         `state` = 2,
                        `timestamp` = {$timestamp},
                        `starterID` = {$starterIDAsSQL}
            WHERE       `state` = 1
                        {$andProcessID}
            ORDER BY    `priority`
            LIMIT       1

EOT;

        Colby::query($SQL, /* retryOnDeadlock */ true);

        if (Colby::mysqli()->affected_rows === 1) {
            return CBTasks2::runTaskForStarter($starterID);
        } else {
            return false;
        }
    }

    /**
     * This function will run the specified task before the function returns.
     *
     * @param string $className
     * @param ID $ID
     *
     * @return bool
     *
     *      Returns true if the task was run; otherwise false.
     *
     *      If the specified task is currently running this function will return
     *      false. There are many issues if the task is currently running. Is
     *      the current run enough to make the caller happy? Does the caller
     *      need a run from start to finish because they are monitoring
     *      something. If the task is running, the task can take the entire
     *      request time so waiting to run the task again is not a technically
     *      viable option, so in almost all theoretical cases the run should
     *      happen during a future request.
     *
     *      Task management using MySQL, HTTP, and PHP is meant to support light
     *      weight scenarios. The technical requirements of a computer operating
     *      system threading system would quickly push this implementation past
     *      its abilities. Tasks in Colby are not meant meet such a high level
     *      of requirements at this time.
     */
    static function runSpecificTask(string $className, string $ID): bool {
        $classNameAsSQL = CBDB::stringToSQL($className);
        $IDAsSQL = CBHex160::toSQL($ID);
        $timestamp = time();
        $state_running = 2;
        $starterID = CBHex160::random();
        $starterIDAsSQL = CBHex160::toSQL($starterID);

        /**
         * If the task is in the CBTasks2 table in a non-running state, place it
         * in a running state with the generated starter ID.
         */

        $SQL = <<<EOT

            UPDATE  CBTasks2
            SET     starterID = {$starterIDAsSQL},
                    state = {$state_running},
                    timestamp = {$timestamp}
            WHERE   className = $classNameAsSQL AND
                    ID = {$IDAsSQL} AND
                    state != {$state_running}

EOT;

        Colby::query($SQL, /* retryOnDeadlock */ true);

        /**
         * If the task was not in the CBTasks2 table in a non-running state,
         * attempt to insert it with a running state and the generated starter
         * ID.
         */

        if (Colby::mysqli()->affected_rows !== 1) {
            $SQL = <<<EOT

                INSERT INTO CBTasks2
                (
                    className,
                    ID,
                    starterID,
                    state,
                    timestamp
                )
                VALUES
                (
                    {$classNameAsSQL},
                    {$IDAsSQL},
                    {$starterIDAsSQL},
                    {$state_running},
                    {$timestamp}
                )

EOT;

            try {
                Colby::query($SQL, /* retryOnDeadlock */ true);
            } catch (Throwable $throwable) {
                if (Colby::mysqli()->errno === 1062) {

                    /**
                     * This is they MySQL error number for "duplicate entry"
                     * which means the row already exists. In the current
                     * context it means the task was either already running or
                     * that another process inserted the row since the time we
                     * ran the function's first query.
                     *
                     * Either way, the task cannot be run during this function
                     * call.
                     */

                    return false;
                } else {
                    throw $throwable;
                }
            }
        }

        return CBTasks2::runTaskForStarter($starterID);
    }

    /**
     * Runs a task started by the provided starter. There are situations in
     * which a task can be pulled away from a starter and in those cases the
     * task will not be run by that starter and this function will return
     * false.
     *
     * The situations are rare race conditions and potentially a very closely
     * timed calls to runNextTask() and runSpecificTask().
     *
     * @NOTE Exception Handling
     *
     *      It is the job of this function to, at the very least, change the
     *      state of the CBTasks2 table row to either 3 (complete) or 4 (error).
     *
     *      It will catch any exceptions that occur and hold onto them until it
     *      can at least attempt to set the state to 4 (error). If it can set
     *      the state it will the throw the caught exception. If another
     *      exception occurs it will report the first exception and throw the
     *      second.
     *
     *      The prinicple is that this function will not hide exceptions. If an
     *      exception occurs this function will make sure the system has
     *      recovered from it and then throw that exception.
     *
     * @param ID $starterID
     *
     * @return bool
     *
     *      Returns true if the task is run; otherwise false.
     */
    private static function runTaskForStarter($starterID) {
        $starterIDAsSQL = CBHex160::toSQL($starterID);

        try {
            $SQL = <<<EOT

                SELECT  `className`,
                        LOWER(HEX(`ID`)) AS `ID`,
                        LOWER(HEX(`processID`)) as `processID`
                FROM    `CBTasks2`
                WHERE   `starterID` = {$starterIDAsSQL}

EOT;

            $task = CBDB::SQLToObject($SQL, /* retryOnDeadlock */ true);

            /**
             * If someone deleted or manually ran the task return false.
             */

            if ($task === false) {
                return false;
            }

            if ($task->processID !== null) {
                CBProcess::setID($task->processID);
            }

            CBID::push($task->ID);

            if (is_callable($function = "{$task->className}::CBTasks2_run")) {
                $status = call_user_func($function, $task->ID);
            } else if (is_callable($function = "{$task->className}::CBTasks2_Execute")) { /* deprecated */
                $status = call_user_func($function, $task->ID);
            } else {
                throw new Exception("The CBTasks2_run() interface has not been implemented by the {$task->className} class preventing execution of the task for ID {$task->ID}");
            }

            $scheduled = CBModel::valueAsInt($status, 'scheduled');

            // Log a debug level entry that a task has run.

            CBLog::log((object)[
                'className' => __CLASS__,
                'message' => "CBTasks2 ran {$task->className} for ID {$task->ID}",
                'severity' => 7,
            ]);

            $state = 3; /* complete */
        } catch (Throwable $throwable) {
            /**
             * We'll rethrow this throwable at the end of the function after we
             * get the CBTasks2 table fixed up.
             */
            $firstThrowable = $throwable;

            $scheduled = null;
            $state = 4; /* failed */
        }

        try {
            $now = time();

            if ($scheduled === null) {
                // state will already be 3 or 4 depending on failure status
                $timestamp = $now;
            } else if ($scheduled <= $now) {
                $state = 1; /* ready */
                $timestamp = $now;
            } else {
                $state = 0; /* scheduled */
                $timestamp = $scheduled;
            }

            $classNameAsSQL = CBDB::stringToSQL($task->className);
            $IDAsSQL = CBHex160::toSQL($task->ID);
            $SQL = <<<EOT

                UPDATE  `CBTasks2`
                SET     `state` = {$state},
                        `timestamp` = {$timestamp}
                WHERE   `className` = {$classNameAsSQL} AND
                        `ID` = {$IDAsSQL} AND
                        `starterID` = {$starterIDAsSQL}

EOT;

            Colby::query($SQL, /* retryOnDeadlock */ true);

            $affectedRows = Colby::mysqli()->affected_rows;

            CBID::pop();

            if ($task->processID !== null) {
                CBProcess::clearID();
            }
        } catch (Throwable $throwable) {
            if (!empty($firstThrowable)) {
                CBErrorHandler::report($firstThrowable);
            }

            throw $throwable;
        }

        if (!empty($firstThrowable)) {
            throw $firstThrowable;
        } else if ($affectedRows === 1) {
            return true;
        } else {
            return false;
        }
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
     * @param hex160 $ID
     * @param hex160 $processID (@deprecated use setID() on CBProcess)
     * @param int $priority
     *
     *      See CBInstall_install() on this class for documentation on these
     *      parameters.
     *
     * @param int (timestamp) $scheduled
     *
     *      If a value is provided that's less than or equal to time() then the
     *      task will be made ready. If the value is greater than time() the
     *      task will be scheduled.
     *
     *      A null value will make a new task ready and will have no effect
     *      on an existing task's availability.
     *
     * @return void
     */
    static function updateTasks(string $className, array $IDs, ?string $processID = null, ?int $priority = null, ?int $scheduled = null): void {
        if (empty($IDs)) {
            return;
        }

        $now = time();
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
            $updates[] = "`priority` = {$priority}";
        } else {
            $priority = CBTasks2::defaultPriority;
        }

        if ($scheduled !== null) {
            if ($scheduled <= time()) {
                $state = 1; /* ready */
                $updates[] = "`timestamp` = {$now}";
            } else {
                $state = 0; /* scheduled */
                $updates[] = "`timestamp` = {$scheduled}";
            }
            $updates[] = "`state` = {$state}";
        }

        if (empty($updates)) {
            /* placeholder update for INSERT ON DUPLICATE KEY UPDATE */
            $updates[] = '`priority` = `priority`';
        }

        $updates = implode(', ', $updates);
        $values = [];

        foreach ($IDsAsSQL as $IDAsSQL) {
            $values[] = "({$classNameAsSQL}, {$IDAsSQL}, {$priority}, 1, {$now}, {$processIDAsSQL})";
        }

        $values = implode(', ', $values);

        /**
         * INSERT ON DUPLICATE KEY UPDATE can be used here because `CBTasks2`
         * has only one unique key. If this changes a temporary table must be
         * used.
         */

        $SQL = <<<EOT

            INSERT INTO `CBTasks2` (
                `className`,
                `ID`,
                `priority`,
                `state`,
                `timestamp`,
                `processID`
            ) VALUES
                {$values}
            ON DUPLICATE KEY UPDATE
                {$updates}

EOT;

        Colby::query($SQL, /* retryOnDeadlock */ true);
    }

    /**
     * @return null
     */
    static function wakeScheduledTasks() {
        $now = time();
        $SQL = <<<EOT

            UPDATE  `CBTasks2`
            SET     `state` = 1,
                    `timestamp` = {$now}
            WHERE   `state` = 0 AND
                    `timestamp` < {$now}

EOT;

        Colby::query($SQL, /* retryOnDeadlock */ true);
    }
}
