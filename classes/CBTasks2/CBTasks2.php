<?php

final class
CBTasks2
{
    /**
     * Priority values are between 0 and 255 with lower numbers representing
     * higher priority.
     */
    const defaultPriority = 100;

    const highPriority = 10;


    /* -- CBAjax interfaces -- -- -- -- -- */



    /**
     * @param object $args
     *
     *      {
     *          processID: ?CBID
     *      }
     *
     *  @return object
     */
    static function CBAjax_fetchStatus($args): stdClass {
        return CBTasks2::fetchStatus($args);
    }



    /**
     * @return string
     */
    static function CBAjax_fetchStatus_getUserGroupClassName(): string {
        return 'CBAdministratorsUserGroup';
    }



    /**
     * This ajax function will run the next ready task if one exists. If one
     * doesn't exist it will attempt to wake any scheduled tasks.
     *
     * @param object $args
     *
     *      {
     *          processID: ?CBID
     *      }
     *
     * @return object
     *
     *      {
     *          tasksRunCount: int
     *          taskWasRun: bool
     *      }
     */
    static function CBAjax_runNextTask(
        $args
    ): stdClass {
        if (CBSiteIsConfigured === false) {
            return (object)[
                'tasksRunCount' => 0,
                'taskWasRun' => false,
            ];
        }

        $processID = CBModel::valueAsID($args, 'processID');
        $tasksRunCount = 0;
        $expirationTimestamp = microtime(true) + 1;

        do {
            $wasRun = CBTasks2::runNextTask(
                (object)[
                    'processID' => $processID,
                ]
            );

            if ($wasRun) {
                $tasksRunCount += 1;

                if (CBHTMLOutput::getIsActive()) {
                    CBLog::log(
                        (object)[
                            'message' =>
                            'CBHTMLOutput is active after running a task.',
                            'severity' => 3,
                            'sourceClassName' => __CLASS__,
                            'sourceID' =>
                            '3db6df0668e64d398d7536c0dd6a0b148b4760ca',
                        ]
                    );

                    break;
                }
            } else {
                break;
            }
        } while (microtime(true) < $expirationTimestamp);

        if ($tasksRunCount === 0) {
            CBTasks2::wakeScheduledTasks();
        }

        return (object)[
            'tasksRunCount' => $tasksRunCount,
            'taskWasRun' => $tasksRunCount > 0,
        ];
    }
    /* CBAjax_runNextTask() */



    /**
     * @return string
     */
    static function CBAjax_runNextTask_getUserGroupClassName(): string {
        return 'CBPublicUserGroup';
    }



    /**
     * @param object $args
     *
     *      {
     *          CBID: CBID
     *          className: string
     *      }
     *
     * @return bool
     */
    static function CBAjax_runSpecificTask(
        stdClass $args
    ): bool {
        $className = CBModel::valueToString(
            $args,
            'className'
        );

        $CBID = CBModel::valueAsCBID(
            $args,
            'CBID'
        );

        if ($CBID === null) {
            throw new CBExceptionWithValue(
                'The CBID property value of args is not valid.',
                $args,
                '8e479aa2e3164322207dca881a8508e046ed8c2a'
            );
        }

        return CBTasks2::runSpecificTask(
            $className,
            $CBID
        );
    }
    /* CBAjax_runSpecificTask() */



    /**
     * @return string
     */
    static function CBAjax_runSpecificTask_getUserGroupClassName(): string {
        return 'CBDevelopersUserGroup';
    }



    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * Table columns:
     *
     *      className
     *
     *          The class name of the class that runs the task. This class must
     *          implement the CBTasks2_run() interface.
     *
     *      ID
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
     *      priority
     *
     *          0-255 with lower numbers representing higher priority.
     *
     *          default: 100
     *
     *      state
     *
     *          0: scheduled
     *          1: ready
     *          2: running
     *          3: complete
     *          4: failed
     *
     *      timestamp
     *
     *          state 0: time running scheduled to start
     *          state 1: time made ready to start
     *          state 2: time running started
     *          state 3: time running finished
     *          state 4: time failure occurred
     *
     *      processID
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
     *      starterID
     *
     *          Used by the this class when running tasks.
     *
     * Table indexes:
     *
     *      className, ID
     *
     *          Primary key. Only one task can exist per className and ID. If
     *          the task needs to be run again, restart it.
     *
     *      state, priority
     *
     *          Used to find the next of all tasks to run.
     *
     *      processID, state, priority
     *
     *          Used to find the next of the tasks in a process to run.
     *
     *      state, timestamp
     *
     *          Used to find scheduled tasks that can be moved to the ready
     *          state.
     *
     *      starterID
     *
     *          Used by the class when running tasks.
     *
     * @return void
     */
    static function CBInstall_install(): void {
        $defaultPriority = CBTasks2::defaultPriority;

        $SQL = <<<EOT

            CREATE TABLE IF NOT EXISTS CBTasks2 (
                className
                    VARCHAR(80) NOT NULL,
                ID
                    BINARY(20) NOT NULL,
                priority
                    TINYINT UNSIGNED NOT NULL DEFAULT {$defaultPriority},
                state
                    TINYINT UNSIGNED NOT NULL,
                timestamp
                    BIGINT NOT NULL,
                processID
                    BINARY(20),
                starterID
                    BINARY(20),

                PRIMARY KEY (className, ID),

                KEY state_priority
                    (state, priority),
                KEY processID_state_priority
                    (processID, state, priority),
                KEY state_timestamp
                    (state, timestamp),
                KEY starterID
                    (starterID)
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_520_ci

        EOT;

        Colby::query($SQL);
    }
    /* CBInstall_install() */



    /**
     * When a task is run a log entry is made so CBLog is required for this
     * class to be fully installed.
     *
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBLog',
        ];
    }



    /* -- functions -- -- -- -- -- */



    /**
     * This function may be deprecated in the future for a more generic function
     * that can fetch various lists of tasks.
     *
     * @return [object]
     *
     *      {
     *          CBID: string
     *          className: string
     *          priority: int
     *          timestamp: int
     *      }
     */
    static function fetchFailedTasks(): array {
        $failedState = 4;

        $SQL = <<<EOT

            SELECT      LOWER(HEX(ID)) AS CBID,
                        className,
                        priority,
                        timestamp

            FROM        CBTasks2

            WHERE       state = {$failedState}

            ORDER BY    timestamp DESC

        EOT;

        $failedTasks = CBDB::SQLToObjects($SQL);

        return $failedTasks;
    }
    /* fetchFailedTasks() */



    /**
     * @return [object]
     */
    static function
    fetchScheduledTasks(
    ): array
    {
        $scheduledState = 0;

        $SQL =
        <<<EOT

            SELECT

            LOWER(HEX(ID)) AS
            CBTasks2_fetchScheduledTasks_targetModelCBID,

            className AS
            CBTasks2_fetchScheduledTasks_taskClassName,

            priority AS
            CBTasks2_fetchScheduledTasks_taskPriority,

            timestamp AS
            CBTasks2_fetchScheduledTasks_timestamp

            FROM
            CBTasks2

            WHERE
            state =
            {$scheduledState}

            ORDER BY
            timestamp ASC,
            priority ASC

        EOT;

        $scheduledTasks =
        CBDB::SQLToObjects(
            $SQL
        );

        return $scheduledTasks;
    }
    // fetchScheduledTasks()



    /**
     * @param object $args
     *
     *      {
     *          processID: ?CBID
     *      }
     *
     *  @return object
     *
     *      {
     *          complete: int
     *          failed: int
     *          maintenanceStatus: string
     *          ready: int
     *          running: int
     *          scheduled: int
     *          total: int
     *      }
     */
    static function fetchStatus($args): stdClass {
        $processID = CBModel::valueAsID($args, 'processID');

        if ($processID !== null) {
            $processIDAsSQL = CBID::toSQL($processID);
            $where = "WHERE processID = {$processIDAsSQL}";
        } else {
            $where = '';
        }

        $SQL = <<<EOT

            SELECT      state,
                        COUNT(*) AS count

            FROM        CBTasks2

            {$where}

            GROUP BY    state

        EOT;

        $states = CBDB::SQLToObjects($SQL, ['keyField' => 'state']);
        $scheduled = isset($states[0]) ? intval($states[0]->count) : 0;
        $ready = isset($states[1]) ? intval($states[1]->count) : 0;
        $running = isset($states[2]) ? intval($states[2]->count) : 0;
        $complete = isset($states[3]) ? intval($states[3]->count) : 0;
        $failed = isset($states[4]) ? intval($states[4]->count) : 0;

        return (object)[
            'complete' => $complete,
            'failed' => $failed,
            'maintenanceIsLocked' => CBMaintenance::isLocked(),
            'ready' => $ready,
            'running' => $running,
            'scheduled' => $scheduled,
            'total' => $scheduled + $ready + $running + $complete + $failed,
        ];
    }
    /* fetchStatus() */



    /**
     * @param string $className
     * @param [CBID]|CBID $IDs
     *
     * @return void
     */
    static function
    remove(
        $className,
        $IDs
    ): void {
        if (!is_array($IDs)) {
            $IDs = [$IDs];
        }

        $classNameAsSQL = CBDB::stringToSQL($className);
        $IDsAsSQL = CBID::toSQL($IDs);

        $SQL = <<<EOT

            DELETE
            FROM    CBTasks2
            WHERE   className = {$classNameAsSQL} AND
                    ID IN ($IDsAsSQL)

        EOT;

        Colby::query($SQL);
    }
    /* remove() */



    /**
     * This is the best function to call if you want to make sure a task is run
     * in the near future.
     *
     * @param string $className
     * @param [CBID]|CBID $IDs
     * @param ?int $priority
     * @param int $delayInSeconds
     *
     * @return void
     */
    static function
    restart(
        string $className,
        $IDs,
        ?int $priority = null,
        int $delayInSeconds = 0
    ): void {
        if (!is_array($IDs)) {
            $IDs = [$IDs];
        }

        if ($delayInSeconds < 0) {
            $delayInSeconds = 0;
        }

        $scheduled = time() + $delayInSeconds;
        $processID = null;

        CBTasks2::updateTasks(
            $className,
            $IDs,
            $processID,
            $priority,
            $scheduled
        );
    }
    /* restart() */



    /**
     * @param object $args
     *
     *      {
     *          processID: ?CBID
     *      }
     *
     * @return bool
     *
     *      Returns true if a task is run; otherwise false.
     */
    static function runNextTask($args): bool {
        $processID = CBModel::valueAsID($args, 'processID');

        /**
         * If no process ID is specified, we will only run a task if the
         * maintenance lock is not held.
         */
        if (empty($processID)) {
            if (CBMaintenance::isLocked()) {
                return false;
            }

            $andProcessID = '';
        } else {
            $processIDAsSQL = CBID::toSQL($processID);
            $andProcessID = "AND processID = {$processIDAsSQL}";
        }

        $timestamp = time();
        $starterID = CBID::generateRandomCBID();
        $starterIDAsSQL = CBID::toSQL($starterID);

        $SQL = <<<EOT

            UPDATE      CBTasks2

            SET         state = 2,
                        timestamp = {$timestamp},
                        starterID = {$starterIDAsSQL}

            WHERE       state = 1
                        {$andProcessID}

            ORDER BY    priority

            LIMIT       1

        EOT;

        Colby::query($SQL, /* retryOnDeadlock */ true);

        if (Colby::mysqli()->affected_rows === 1) {
            return CBTasks2::runTaskForStarter($starterID);
        } else {
            return false;
        }
    }
    /* runNextTask() */



    /**
     * This function will run the specified task before the function returns.
     *
     * @param string $className
     * @param CBID $ID
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
    static function
    runSpecificTask(
        string $className,
        string $ID
    ): bool
    {
        $classNameAsSQL =
        CBDB::stringToSQL(
            $className
        );

        $IDAsSQL =
        CBID::toSQL(
            $ID
        );

        $timestamp =
        time();

        $state_running =
        2;

        $starterID =
        CBID::generateRandomCBID();

        $starterIDAsSQL =
        CBID::toSQL(
            $starterID
        );

        /**
         * If the task is in the CBTasks2 table in a non-running state, place it
         * in a running state with the generated starter ID.
         */

        $SQL =
        <<<EOT

            UPDATE
            CBTasks2

            SET
            starterID =
            {$starterIDAsSQL},

            state =
            {$state_running},

            timestamp =
            {$timestamp}

            WHERE
            className =
            $classNameAsSQL AND

            ID =
            {$IDAsSQL} AND

            state !=
            {$state_running}

        EOT;

        Colby::query(
            $SQL,
            true /* retryOnDeadlock */
        );

        /**
         * If the task was not in the CBTasks2 table in a non-running state,
         * attempt to insert it with a running state and the generated starter
         * ID.
         */

        if (
            Colby::mysqli()->affected_rows !==
            1
        ) {
            $SQL =
            <<<EOT

                INSERT INTO
                CBTasks2

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

            try
            {
                Colby::query(
                    $SQL,
                    true /* retryOnDeadlock */
                );
            }

            catch (
                Throwable $throwable
            ) {
                if (
                    Colby::mysqli()->errno ===
                    1062
                ) {

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
                }

                else
                {
                    throw $throwable;
                }
            }
        }

        $taskWasRun =
        CBTasks2::runTaskForStarter(
            $starterID
        );

        return $taskWasRun;
    }
    /* runSpecificTask() */



    /**
     * @NOTE 2022_09_23_1663971461
     *
     *      This is the only function that will actually run a task. Since this
     *      function is private, the public functions that will run tasks are
     *      runSpecificTask() and runNextTask().
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
     *      The principle is that this function will not hide exceptions. If an
     *      exception occurs this function will make sure the system has
     *      recovered from it and then throw that exception.
     *
     * @param CBID $starterID
     *
     *      A task must be assigned a unique starter CBID before this function
     *      is called.
     *
     *      There are situations in which a task can be pulled away from a
     *      starter and in those cases the task will not be run by that starter
     *      and this function will return false.
     *
     *      The situations are rare race conditions and potentially a very
     *      closely timed calls to runNextTask() and runSpecificTask().
     *
     * @return bool
     *
     *      Returns true if the task is run; otherwise false.
     */
    private static function
    runTaskForStarter(
        $starterID
    ): bool {
        $starterIDAsSQL = CBID::toSQL($starterID);

        try {
            $SQL = <<<EOT

                SELECT  className,
                        LOWER(HEX(ID)) AS ID,
                        LOWER(HEX(processID)) as processID

                FROM    CBTasks2

                WHERE   starterID = {$starterIDAsSQL}

            EOT;

            $task = CBDB::SQLToObjectNullable($SQL);

            /**
             * If someone deleted or manually ran the task return false.
             */

            if ($task === null) {
                return false;
            }

            if ($task->processID !== null) {
                CBProcess::setID($task->processID);
            }

            CBID::push($task->ID);


            if (
                is_callable(
                    $function = "{$task->className}::CBTasks2_run"
                )
            ) {
                $taskReturnValue = call_user_func($function, $task->ID);
            }

            /* deprecated */
            else if (
                is_callable(
                    $function = "{$task->className}::CBTasks2_Execute"
                )
            ) {
                $taskReturnValue = call_user_func($function, $task->ID);
            }

            else {
                throw new Exception(
                    "The CBTasks2_run() interface has not been " .
                    "implemented by the {$task->className} class " .
                    "preventing execution of the task for ID {$task->ID}"
                );
            }


            /**
             * The task function can request that the task be rerun by returning
             * an integer value for the "sheduled" property.
             */
            $requestedScheduled = CBModel::valueAsInt(
                $taskReturnValue,
                'scheduled'
            );

            /**
             * The task function can request that the task be set to a specific
             * priority by returning an integer value for the "priority"
             * property.
             */
            $requestedPriority = CBModel::valueAsInt(
                $taskReturnValue,
                'priority'
            );

            // Log a debug level entry that a task has run.

            CBLog::log(
                (object)[
                    'message' => (
                        "CBTasks2 ran {$task->className} for ID {$task->ID}"
                    ),
                    'severity' => 7,
                    'sourceClassName' => __CLASS__,
                    'sourceID' => '79ea4dab030cff53f132622f6309bc44b552908a',
                ]
            );

            $newState = 3; /* complete */
        } catch (Throwable $throwable) {

            /**
             * @NOTE 2019_07_19
             *
             *      This call to report() was recently added because although
             *      this function rethrows the exception sometimes that
             *      exception is not caught and exceptions were being
             *      effectively hidden.
             *
             *      This is a bigger code organization issue in this file and
             *      should be resolved in the future by doing an exploration of
             *      how task exceptions are and should be handled.
             *
             *      This is also a continuation of the issue of moveing away
             *      from exception handlers and toward effectively placed
             *      try-catch blocks throughout the system.
             *
             * @NOTE 2019_07_27
             *
             *      This is not the appropriate place to report this exception.
             *      An try-catch block has been added to CBAjax which will
             *      report. If exceptions are being hidden in other places there
             *      is probably a try-catch block needed elsewhere.
             *
             *      CBErrorHandler::report($throwable);
             */

            /**
             * We'll rethrow this throwable at the end of the function after we
             * get the CBTasks2 table fixed up.
             */
            $firstThrowable = $throwable;

            $requestedScheduled = null;
            $newState = 4; /* failed */
        }

        try {
            $now = time();

            /**
             * Tasks are returned to the default priority after running unless
             * the task function requests otherwise.
             */
            $newPriority = $requestedPriority ?? CBTasks2::defaultPriority;

            if ($requestedScheduled === null) {
                // $newState will already be set to 3 (completed) or 4 (failed)
                $newTimestamp = $now;
            } else if ($requestedScheduled <= $now) {
                $newState = 1; /* ready */
                $newTimestamp = $now;
            } else {
                $newState = 0; /* scheduled */
                $newTimestamp = $requestedScheduled;
            }

            $classNameAsSQL = CBDB::stringToSQL($task->className);
            $IDAsSQL = CBID::toSQL($task->ID);

            /**
             * @NOTE 2018.08.09
             *
             *      This query specifies a starterID column value in the where
             *      clause even though this table's primary key is className and
             *      ID which have also been specified. This would indicate this
             *      was done to make sure this query only completed if the
             *      startedID is still the same at this point.
             *
             *      I'm not sure it's possible for the starterID to have
             *      changed, but after investigation uncovers that is possible,
             *      add a comment here describing the scenarios.
             */
            $SQL = <<<EOT

                UPDATE  CBTasks2

                SET     priority = {$newPriority},
                        state = {$newState},
                        timestamp = {$newTimestamp}

                WHERE   className = {$classNameAsSQL} AND
                        ID = {$IDAsSQL} AND
                        starterID = {$starterIDAsSQL}

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
    /* runTaskForStarter() */



    /**
     * This updates a task for a wide number of reasons, not all of which
     * involve running or scheduling the task. If you want to make sure a task
     * will run and don't have a need for specific scheduling, use
     * CBTasks2::restart() instead.
     *
     * @return void
     */
    static function
    updateTask(
        $className,
        $ID,
        $processID = null,
        $priority = null,
        $scheduled = null
    ): void {
        CBTasks2::updateTasks(
            $className,
            [$ID],
            $processID,
            $priority,
            $scheduled
        );
    }
    /* updateTask() */



    /**
     * This function is use to both create and update a task.
     *
     * @param string $className
     * @param CBID $ID
     * @param CBID $processID (@deprecated use setID() on CBProcess)
     * @param ?int $priority
     *
     *      If not specified, the default value for this argument is
     *      CBTasks2::defaultPriority.
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
    static function
    updateTasks(
        string $className,
        array $IDs,
        ?string $processID = null,
        ?int $priority = null,
        ?int $scheduled = null
    ): void {
        if (empty($IDs)) {
            return;
        }

        $now = time();

        $classNameAsSQL = CBDB::stringToSQL(
            $className
        );

        $IDsAsSQL = array_map(
            'CBID::toSQL',
            $IDs
        );

        $updates = [];

        if (
            !CBID::valueIsCBID($processID)
        ) {
            $processID = CBProcess::ID();
        } else {
            $processID = null;
        }

        if (
            $processID !== null
        ) {
            $processIDAsSQL = CBID::toSQL(
                $processID
            );

            $updates[] = "processID = {$processIDAsSQL}";
        } else {
            $processIDAsSQL = 'NULL';
        }

        if ($priority !== null) {
            $updates[] = "priority = {$priority}";
        } else {
            $priority = CBTasks2::defaultPriority;
        }

        if ($scheduled !== null) {
            if ($scheduled <= time()) {
                $state = 1; /* ready */
                $updates[] = "timestamp = {$now}";
            } else {
                $state = 0; /* scheduled */
                $updates[] = "timestamp = {$scheduled}";
            }
            $updates[] = "state = {$state}";
        }

        if (empty($updates)) {
            /* placeholder update for INSERT ON DUPLICATE KEY UPDATE */
            $updates[] = 'priority = priority';
        }

        $updates = implode(
            ', ',
            $updates
        );

        $values = [];

        foreach ($IDsAsSQL as $IDAsSQL) {
            $values[] = (
                "(" .
                "{$classNameAsSQL}, " .
                "{$IDAsSQL}, " .
                "{$priority}, " .
                "1, " .
                "{$now}, " .
                "{$processIDAsSQL}" .
                ")"
            );
        }

        $values = implode(
            ', ',
            $values
        );


        /**
         * INSERT ON DUPLICATE KEY UPDATE can be used here because CBTasks2
         * has only one unique key. If this changes a temporary table must be
         * used.
         */

        $SQL = <<<EOT

            INSERT INTO CBTasks2

            (
                className,
                ID,
                priority,
                state,
                timestamp,
                processID
            )

            VALUES {$values}

            ON DUPLICATE KEY UPDATE {$updates}

        EOT;

        Colby::query(
            $SQL,
            true /* retryOnDeadlock */
        );
    }
    /* updateTasks() */



    /**
     * @return void
     */
    static function wakeScheduledTasks(): void {
        $now = time();

        $SQL = <<<EOT

            UPDATE  CBTasks2

            SET     state = 1,
                    timestamp = {$now}

            WHERE   state = 0 AND
                    timestamp < {$now}

        EOT;

        Colby::query($SQL, /* retryOnDeadlock */ true);
    }
    /* wakeScheduledTasks() */

}
