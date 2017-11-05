<?php

final class CBLog {

    /* RFC3164 */
    static $severityDescriptions = [
        'Emergency',        // 0
        'Alert',            // 1
        'Critical',         // 2
        'Error',            // 3
        'Warning',          // 4
        'Notice',           // 5
        'Informational',    // 6
        'Debug',            // 7
    ];

    /**
     * @deprecated use CBLog::log()
     */
    static function addMessage($category, $severity, $message) {
        CBLog::log((object)[
            'className' => $category,
            'message' => $message,
            'severity' => $severity,
        ]);
    }

    /**
     * Table columns:
     *
     *      `className`
     *
     *      The name of the class that created the log entry. This may be the
     *      class name of a task that is currently executing.
     *
     *      `ID`
     *
     *      One of:
     *          - The ID of a specific model associated with the log entry.
     *          - The ID of the task that created the log entry.
     *          - NULL if neither of the above is applicable.
     *
     *      `processID`
     *
     *      This is automatically set if a process ID has been set when the log
     *      entry is created.
     *
     *      `message`
     *
     *      The message of the log entry. The very first line should provide a
     *      short understandable summary of the entire log message.
     *
     *          Bad first lines:
     *          <empty>
     *          Done
     *          1/15
     *
     *          Good first lines:
     *          The page "My Blog Post" was checked for deprecated views.
     *          The data directory "data/a5/23" was checked for files.
     *          The log table was cleared of 35 very old entries.
     *
     *      `serial`
     *
     *      An automatically incremented number to establish an exact order of
     *      log entries.
     *
     *      `severity`
     *
     *      A number 0-7 indicating the severity of the log message.
     *
     *          RFC3164 Severity Codes
     *          https://tools.ietf.org/html/rfc3164
     *
     *          0  Emergency: system is unusable
     *          1  Alert: action must be taken immediately
     *          2  Critical: critical conditions
     *          3  Error: error conditions
     *          4  Warning: warning conditions
     *          5  Notice: normal but significant condition
     *          6  Informational: informational messages
     *          7  Debug: debug-level messages
     *
     *      `timestamp`
     *
     *      The timestamp the log entry was created.
     *
     * @return null
     */
    static function install() {
        $SQL = <<<EOT

            CREATE TABLE IF NOT EXISTS `CBLog` (
                `className` VARCHAR(80) NOT NULL,
                `ID`        BINARY(20),
                `processID` BINARY(20),
                `message`   TEXT NOT NULL,
                `serial`    SERIAL,
                `severity`  TINYINT NOT NULL,
                `timestamp` BIGINT NOT NULL,
                KEY `className_serial` (`className`, `serial`),
                KEY `className_ID_serial` (`className`, `ID`, `serial`),
                KEY `processID_serial` (`processID`, `serial`),
                KEY `severity_serial` (`severity`, `serial`)
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
     *      See CBLog::entries()
     *
     * @return [object]
     */
    static function CBAjax_fetchEntries($args) {
        return CBLog::entries($args);
    }

    /**
     * @return string
     */
    static function CBAjax_fetchEntries_group() {
        return 'Administrators';
    }

    /**
     * @param object $args
     *
     *      {
     *          afterSerial: int?
     *
     *              If specified, will only fetch log entries with a serial
     *              greater than afterSerial. This allows callers to request
     *              only new entries since the last time they asked.
     *
     *          afterTimestamp: int?
     *
     *              If specified, will only fetch log entires with a timestamp
     *              greater than afterTimestamp. This allows callers to request
     *              only new entries since the last tiime they asked.
     *
     *          mostRecentDescending: bool?
     *
     *          processID: hex160?
     *
     *              If specified, will only fetch log entries made for this
     *              process ID.
     *      }
     *
     * @return [object]
     *
     *      {
     *          className: string
     *          ID: hex160?
     *          message: string
     *          serial: int
     *          severity: int
     *          timestamp: int
     *      }
     */
    static function entries($args = null) {
        $whereAsSQL = [];

        $afterSerial = CBModel::value($args, 'afterSerial', null, 'CBConvert::valueAsInt');

        if ($afterSerial !== null) {
            $whereAsSQL[] = "`serial` > {$afterSerial}";
        }

        $afterTimestamp = CBModel::value($args, 'afterTimestamp', null, 'CBConvert::valueAsInt');

        if ($afterTimestamp !== null) {
            $whereAsSQL[] = "`timestamp` > {$afterTimestamp}";
        }

        $processID = CBModel::valueAsID($args, 'processID');

        if ($processID !== null) {
            $processIDAsSQL = CBHex160::toSQL($processID);
            $whereAsSQL[] = "`processID` = {$processIDAsSQL}";
        }

        if (empty($whereAsSQL)) {
            $whereAsSQL = '';
        } else {
            $whereAsSQL = 'WHERE ' . implode(' AND ', $whereAsSQL);
        }

        $mostRecentDescending = CBModel::value($args, 'mostRecentDescending', false, 'boolval');
        $descAsSQL = $mostRecentDescending ? 'DESC' : '';

        $SQL = <<<EOT

            SELECT  `className`,
                    LOWER(HEX(`ID`)) AS `ID`,
                    `message`,
                    `serial`,
                    `severity`,
                    `timestamp`
            FROM    `CBLog`
            {$whereAsSQL}
            ORDER BY `serial` {$descAsSQL}
            LIMIT 500

EOT;

        return CBDB::SQLToObjects($SQL);
    }

    /**
     * Create a log entry.
     *
     * This function will never throw an exception so it can be called without
     * fear of recursive exceptions.
     *
     * See CBLog::install() for detailed descriptions of the CBLog table
     * columns.
     *
     * @param object $args
     *
     *      {
     *          className: string
     *
     *              The name of the class most associated with the message. This
     *              is often the class calling the function, but sometimes
     *              classes or functions log messages related to something that
     *              occurred regarding another class.
     *
     *          ID: hex160?
     *
     *              The ID of a specific model or the ID of a task or null.
     *
     *          message: string
     *
     *              The message must have a length greater than 0.
     *
     *          severity: int?
     *
     *              The severity of the log entry. Entries with a severity less
     *              than 4 will also be sent to error_log().
     *
     *              default: 6 (Informational)
     *      }
     *
     * @return null
     */
    static function log($args) {
        $className = CBModel::value($args, 'className', '', 'trim');
        $ID = CBModel::value($args, 'ID', null, 'CBConvert::valueAsHex160');
        $message = CBModel::value($args, 'message', '', 'trim');
        $severity = CBModel::value($args, 'severity', 6, 'CBConvert::valueAsInt');

        /**
         * If an empty message or className was specified, transition the the
         * message to an error message.
         */
        if (empty($message) || empty($className)) {
            $severity = min($severity, 3);
            $notes = [];

            if (empty($className)) {
                $className = __CLASS__;
                $notes[] = "An invalid `className` argument was specified for this log entry.";
            }

            if (empty($message)) {
                $notes[] = "An invalid `message` argument was specified for this log entry.";
            }

            $notes[] = CBConvert::traceToString(debug_backtrace());

            $message = implode("\n\n", $notes);
        }

        try {
            if ($severity < 4) {
                try {
                    error_log(__METHOD__ . "() for className: {$className} ID: {$ID} message: {$message}");
                } catch (Exception $ignoredException) {
                    // We can't do much if error_log() fails.
                }
            }

            $classNameAsSQL = CBDB::stringToSQL($className);
            $IDAsSQL = ($ID === null) ? 'NULL' : CBHex160::toSQL($ID);
            $processID = CBProcess::ID();
            $processIDAsSQL = ($processID === null) ? 'NULL' : CBHex160::toSQL($processID);
            $messageAsSQL = CBDB::stringToSQL($message);
            $severityAsSQL = (int)$severity;
            $timestampAsSQL = time();

            $SQL = <<<EOT

                INSERT INTO `CBLog` (
                    `className`,
                    `ID`,
                    `processID`,
                    `message`,
                    `severity`,
                    `timestamp`
                ) VALUES (
                    {$classNameAsSQL},
                    {$IDAsSQL},
                    {$processIDAsSQL},
                    {$messageAsSQL},
                    {$severityAsSQL},
                    {$timestampAsSQL}
                )

EOT;

            Colby::query($SQL);
        } catch (Exception $innerException) {
            try {
                $message = CBConvert::throwableToMessage($innerException);
                error_log(__METHOD__ . " inner exception: {$message}");
            } catch (Exception $ignoredException) {
                error_log(__METHOD__ . ' ignored exception');
            }
        }
    }

    /**
     * @return int
     *
     *      Returns the number of entries removed.
     */
    static function removeExpiredEntries() {
        $timestamp = time() - (60 * 60 * 24 * 30 /* 30 days */);
        $SQL = <<<EOT

            DELETE FROM `CBLog`
            WHERE timestamp < {$timestamp}

EOT;

        Colby::query($SQL);

        $count = Colby::mysqli()->affected_rows;

        CBLog::log((object)[
            'className' => __CLASS__,
            'message' => "CBLog::removeExpiredEntries() removed {$count} entries from the CBLog table.",
            'severity' => 7,
        ]);

        return $count;
    }

    /**
     * @param int $severity
     *
     * @return string
     */
    static function severityToDescription($severity) {
        if (isset(CBLog::$severityDescriptions[$severity])) {
            return CBLog::$severityDescriptions[$severity];
        } else {
            return "Unknown severity code: {$severity}";
        }
    }
}
