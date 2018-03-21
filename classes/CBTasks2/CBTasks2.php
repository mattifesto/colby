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
     *          Class name of the class that runs the task.
     *
     *      `ID`
     *
     *          ID associated with the task or NULL
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
     *          Used to associate related tasks with each other. Use the
     *          CBProcess class to set.
     *
     *      `starterID`
     *
     *          Used by the class when running tasks.
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
     * @param int? $priority
     *
     * @return void
     */
    static function restart($className, $IDs, $priority = null): void {
        if (!is_array($IDs)) {
            $IDs = [$IDs];
        }

        CBTasks2::updateTasks($className, $IDs, null, $priority, time());
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
        $processID = CBModel::value($args, 'processID', null, 'CBConvert::valueAsHex160');

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
     * @param string $className
     * @param hex160 $ID
     *
     * @return bool
     *
     *      Returns true if the task is run; otherwise false.
     */
    static function runSpecificTask($className, $ID) {
        $classNameAsSQL = CBDB::stringToSQL($className);
        $IDAsSQL = CBHex160::toSQL($ID);
        $timestamp = time();
        $state = 2; // running
        $starterID = CBHex160::random();
        $starterIDAsSQL = CBHex160::toSQL($starterID);

        /**
         * BUG: Avoid running if task is already running.
         */

        $SQL = <<<EOT

            INSERT INTO `CBTasks2`
            (`className`, `ID`, `starterID`, `state`, `timestamp`)
            VALUES
            ({$classNameAsSQL}, {$IDAsSQL}, {$starterIDAsSQL}, {$state}, {$timestamp})
            ON DUPLICATE KEY UPDATE
                `starterID` = {$starterIDAsSQL},
                `state` = {$state},
                `timestamp` = {$timestamp}

EOT;

        Colby::query($SQL);

        if (Colby::mysqli()->affected_rows > 0) {
            return CBTasks2::runTaskForStarter($starterID);
        } else {
            return false;
        }
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
     * @param hex160 $starterID
     *
     * @return bool
     *
     *      Returns true if the task is run; otherwise false.
     */
    static function runTaskForStarter($starterID) {
        $starterIDAsSQL = CBHex160::toSQL($starterID);
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

        try {
            if (is_callable($function = "{$task->className}::CBTasks2_run")) {
                $status = call_user_func($function, $task->ID);
            } else if (is_callable($function = "{$task->className}::CBTasks2_Execute")) { /* deprecated */
                $status = call_user_func($function, $task->ID);
            } else {
                throw new Exception("The CBTasks2_run() interface has not been implemented by the {$task->className} class preventing execution of the task for ID {$task->ID}");
            }

            $scheduled = CBModel::value($status, 'scheduled', null, 'CBConvert::valueAsInt');

            // Log a debug level entry that a task has run.

            CBLog::log((object)[
                'className' => __CLASS__,
                'message' => "CBTasks2 ran {$task->className} for ID {$task->ID}",
                'severity' => 7,
            ]);

            $state = 3; /* complete */
        } catch (Throwable $throwable) {
            CBErrorHandler::report($throwable);

            $scheduled = null;
            $state = 4; /* failed */
        }

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
     *      CBTasks2_run() function which will be called to perform the
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
     *      task will be made ready. This is how you restart a task. If the
     *      value is greater than time() the task will be scheduled.
     *
     *      A null value will make a new task ready and will have no effect
     *      on an existing task's availability.
     *
     * @return void
     */
    static function updateTasks($className, array $IDs, $processID = null, $priority = null, $scheduled = null): void {
        if (empty($IDs)) {
            return;
        }

        $priority = CBConvert::valueAsInt($priority);
        $scheduled = CBConvert::valueAsInt($scheduled);
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

        Colby::query($SQL);
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

        Colby::query($SQL);
    }
}
