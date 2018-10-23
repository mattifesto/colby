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
     * This is a stack of log entry buffers. A log entry buffer is an array of
     * log entry objects.
     *
     * Normally, there is no log entry buffer and log entries are written
     * directly to the log. A log entry buffer is added to the stack each time
     * bufferStart() is called. All log entries are added to the log entry
     * buffer at the top of the stack and will not be added to the lower log
     * entry buffer unless bufferEndFlush() is called. No buffered log entires
     * will be written to the log unless bufferEndFlush() is called for the log
     * entry buffer at the bottom of the stack.
     */
    private static $bufferStack = [];

    /**
     * @deprecated use CBLog::log()
     */
    static function addMessage($category, $severity, $message) {
        CBLog::log((object)[
            'sourceClassName' => $category,
            'message' => $message,
            'severity' => $severity,
        ]);
    }

    /**
     * @param string $message
     *
     * @return string
     */
    private static function appendBacktrace(string $message): string {
        $backtrace = debug_backtrace();

        /**
         * Shift off the call to CBlog::appendBacktrace()
         */
        array_shift($backtrace);

        /**
         * Shift off the call to CBLog::logForReals()
         */
        array_shift($backtrace);

        /**
         * Shift off the call to CBLog::log()
         */
        array_shift($backtrace);

        $backtraceAsJSONAsMessage = CBMessageMarkup::stringToMessage(
            CBConvert::valueToPrettyJSON($backtrace)
        );

        $message .= <<<EOT

            --- dl
                --- dt
                backtrace
                ---

                --- dd
                    --- pre backtrace CBBackgroundOffsetColor\n{$backtraceAsJSONAsMessage}
                    ---
                ---
            ---

EOT;

        return $message;
    }

    /**
     * This function will run the callback buffering any log entries. If the
     * callback executes without throwing an exception, any log entries made
     * will be returned from the function and won't be flushed to the log.
     *
     * If an exception occurs, any log entries made will be flushed to the log
     * and the exception will be re-thrown.
     *
     * @return [object]
     *
     *      Returns a array of log entries.
     */
    static function buffer(callable $callback): array {
        CBLog::bufferStart();

        try {
            call_user_func($callback);

            $entries = CBLog::bufferContents();

            CBLog::bufferEndClean();
        } catch (Throwable $throwable) {
            CBLog::bufferEndFlush();

            throw $throwable;
        }

        return $entries;
    }

    /**
     * @return ?[object]
     *
     *      If log entries are currently being buffered, an array of log entries
     *      in the current buffer will be returned; otherwise null.
     */
    static function bufferContents(): ?array {
        if (empty(CBLog::$bufferStack)) {
            return null;
        } else {
            $count = count(CBLog::$bufferStack);

            return json_decode(json_encode(CBLog::$bufferStack[$count - 1]));
        }
    }

    /**
     * @return void
     */
    static function bufferEndClean(): void {
        if (empty(CBLog::$bufferStack)) {
            throw new Exception('There is no log entry buffer.');
        } else {
            array_pop(CBLog::$bufferStack);
        }
    }

    /**
     * @return void
     */
    static function bufferEndFlush(): void {
        if (empty(CBLog::$bufferStack)) {
            throw new Exception('There is no log entry buffer.');
        } else {
            $count = count(CBLog::$bufferStack);
            $buffer = array_pop(CBLog::$bufferStack);

            if ($count > 1) {
                $lowerBuffer = array_pop(CBLog::$bufferStack);
                $mergedBuffer = array_merge($lowerBuffer, $buffer);
                array_push(CBLog::$bufferStack, $mergedBuffer);
            } else {
                array_walk($buffer, function ($entry) {
                    CBLog::logForReals($entry);
                });
            }
        }
    }

    /**
     * @return void
     */
    static function bufferStart(): void {
        array_push(CBLog::$bufferStack, []);
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
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBLogTable'
        ];
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
     *              If specified, will only fetch log entries with a timestamp
     *              greater than afterTimestamp. This allows callers to request
     *              only new entries since the last tiime they asked.
     *
     *          lowestSeverity: int?
     *
     *              If specified, will only fetch log entries with the specified
     *              severity or greater severity. Note: Severity becomes greater
     *              as the severity integer gets lower.
     *
     *          modelID: ?ID
     *
     *          mostRecentDescending: bool?
     *
     *          processID: hex160?
     *
     *          className: ?string (deprecated, use sourceClassName)
     *          sourceClassName: ?string
     *
     *          sourceID: ?ID
     *
     *      }
     *
     * @return [object]
     *
     *      {
     *          message: string
     *          modelID: ?ID
     *          processID: ?ID
     *          serial: int
     *          severity: int
     *          sourceClassName: string
     *          sourceID: ?ID
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

        /* modelID */

        $modelID = CBModel::valueAsID($args, 'modelID');

        if (!empty($modelID)) {
            $modelIDAsSQL = CBHex160::toSQL($modelID);
            array_push($whereAsSQL, "modelID = {$modelIDAsSQL}");
        }

        /* sourceClassName */

        $sourceClassName = CBModel::valueToString($args, 'sourceClassName');

        if (empty($sourceClassName)) {
            $sourceClassName = CBModel::valueToString($args, 'className');
        }

        if (!empty($sourceClassName)) {
            $sourceClassNameAsSQL = CBDB::stringToSQL($sourceClassName);
            $whereAsSQL[] = "`sourceClassName` = {$sourceClassNameAsSQL}";
        }

        /* sourceID */

        $sourceID = CBModel::valueAsID($args, 'sourceID');

        if (!empty($sourceID)) {
            $sourceIDAsSQL = CBHex160::toSQL($sourceID);
            $whereAsSQL[] = "sourceID = {$sourceIDAsSQL}";
        }

        $lowestSeverity = CBModel::value($args, 'lowestSeverity', null, 'CBConvert::valueAsInt');

        if ($lowestSeverity !== null) {
            $whereAsSQL[] = "`severity` <= {$lowestSeverity}";
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

            SELECT  message,
                    LOWER(HEX(modelID)) AS modelID,
                    LOWER(HEX(processID)) as processID,
                    serial,
                    severity,
                    sourceClassName,
                    LOWER(HEX(sourceID)) as sourceID,
                    timestamp
            FROM    CBLog
            {$whereAsSQL}
            ORDER BY serial {$descAsSQL}
            LIMIT 500

EOT;

        return CBDB::SQLToObjects($SQL);
    }

    /**
     * @param ?ID $processID
     *
     * @return int
     */
    static function fetchMostRecentSerial(?string $processID = null): int {
        $where = '';

        if ($processID !== null) {
            $processIDAsSQL = CBHex160::toSQL($processID);
            $where = "WHERE processID = {$processIDAsSQL}";
        }

        $SQL = <<<EOT

            SELECT      serial
            FROM        CBLog
            {$where}
            ORDER BY    serial DESC
            LIMIT       1

EOT;

        return CBConvert::valueAsInt(
            CBDB::SQLToValue2($SQL)
        ) ?? -1;
    }

    /**
     * Create a log entry.
     *
     * See CBLog::install() for detailed descriptions of the CBLog table
     * columns.
     *
     * @param object $args
     *
     *      {
     *          message: string
     *
     *              The message must have a length greater than 0.
     *
     *          modelID: ?ID
     *
     *              If the log entry is related to a specific model, pass that
     *              model's ID in this argument.
     *
     *          severity: ?int
     *
     *              The severity of the log entry. Entries with a severity less
     *              than 4 will also be sent to error_log().
     *
     *              default: 6 (Informational)
     *
     *          sourceClassName: string
     *
     *              The name of the class that created the log entry. This
     *              is usually the class calling CBlog::log().
     *
     *          sourceID: ?ID
     *
     *              This ID is specified by a developer to provide an easy way
     *              to find the exact location in the source code that created a
     *              log entry.
     *      }
     *
     * @return void
     */
    static function log(stdClass $args): void {
        CBLog::verifyEntry($args);

        if (empty(CBLog::$bufferStack)) {
            CBLog::logForReals($args);
        } else {
            $count = count(CBLog::$bufferStack);
            array_push(CBLog::$bufferStack[$count - 1], $args);
        }
    }

    /**
     * @return void
     */
    private static function logForReals(stdClass $args): void {
        /* sourceClassName */

        $sourceClassName = CBModel::valueToString($args, 'sourceClassName');

        if (empty($sourceClassName)) {
            $sourceClassName = CBModel::valueToString($args, 'className'); /* deprecated */
        }

        $sourceClassNameAsSQL = CBDB::stringToSQL($sourceClassName);

        /* sourceID */

        $sourceID = CBModel::valueAsID($args, 'sourceID');
        $sourceIDAsSQL = $sourceID ? CBHex160::toSQL($sourceID) : 'NULL';

        /* severity */

        $severity = CBModel::valueAsInt($args, 'severity');

        if (empty($severity)) {
            $severity = 6; /* informational */
        }

        $severityAsSQL = (int)$severity;

        /* modelID */

        $modelID = CBModel::valueAsID($args, 'modelID');

        if (empty($modelID)) {
            $modelID = CBModel::valueAsID($args, 'ID'); /* deprecated */

            if (empty($modelID)) {
                $modelID = CBID::peek();
            }
        }

        $modelIDAsSQL = $modelID ? CBHex160::toSQL($modelID) : 'NULL';

        /* message */

        $message = CBModel::valueToString($args, 'message');
        $message = CBLog::appendBacktrace($message);
        $messageAsSQL = CBDB::stringToSQL($message);

        /* process ID */

        $processID = CBProcess::ID();
        $processIDAsSQL = ($processID === null) ? 'NULL' : CBHex160::toSQL($processID);

        /* timestamp */

        $timestampAsSQL = time();

        $SQL = <<<EOT

            INSERT INTO CBLog (
                message,
                modelID,
                processID,
                severity,
                sourceClassName,
                sourceID,
                timestamp
            ) VALUES (
                {$messageAsSQL},
                {$modelIDAsSQL},
                {$processIDAsSQL},
                {$severityAsSQL},
                {$sourceClassNameAsSQL},
                {$sourceIDAsSQL},
                {$timestampAsSQL}
            )

EOT;

        Colby::query($SQL);
    }

    /**
     * 10 days of log entries are kept in the CBLog table. If a situation arises
     * where that is not enough, document it here and consider adjustment
     * options.
     *
     * @return void
     */
    static function removeExpiredEntries(): void {
        $tenDays = 60 * 60 * 24 * 10;
        $timestamp = time() - $tenDays;
        $SQL = <<<EOT

            DELETE FROM CBLog
            WHERE timestamp < {$timestamp}

EOT;

        Colby::query($SQL);

        $count = Colby::mysqli()->affected_rows;
        $message = <<<EOT

            CBLog::removeExpiredEntries() removed {$count} expired entries from
            the CBLog table.

EOT;

        CBLog::log((object)[
            'message' => $message,
            'severity' => 7,
            'sourceClassName' => __CLASS__,
            'sourceID' => '0206ded5aab567162f0e2651b492091181d5a377',
        ]);
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

    /**
     * This function will make additional log entries to point out issues with a
     * log entry submitted to CBLog::log()
     *
     * @return void
     */
    private static function verifyEntry(stdClass $entry): void {
        $entrySourceClassName = CBModel::valueToString($entry, 'sourceClassName');

        if (empty($entrySourceClassName)) {
            $entrySourceClassName = CBModel::valueToString($entry, 'className'); /* deprecated */
        }

        $entryMessage = CBModel::valueToString($entry, 'message');

        if (empty($entryClassName) || empty($entryMessage)) {
            $entryAsMessage = CBMessageMarkup::stringToMessage(
                CBConvert::valueToPrettyJSON($entry)
            );

            $stackTraceAsMessage = CBMessageMarkup::stringToMessage(
                CBConvert::traceToString(debug_backtrace())
            );

            if (empty($entrySourceClassName)) {
                $message = <<<EOT

                    CBLog_warning_noClassName: A log entry was submitted that does not have a class name.

                    --- pre\n{$entryAsMessage}
                    ---

                    --- pre\n{$stackTraceAsMessage}
                    ---

EOT;

                CBLog::log((object)[
                    'sourceClassName' => __CLASS__,
                    'message' => $message,
                    'severity' => 4,
                ]);
            }

            if (empty($entryMessage)) {
                $message = <<<EOT

                    CBLog_warning_noMessage: A log entry was submitted that does not have a message.

                    --- pre\n{$entryAsMessage}
                    ---

                    --- pre\n{$stackTraceAsMessage}
                    ---

EOT;

                CBLog::log((object)[
                    'sourceClassName' => __CLASS__,
                    'message' => $message,
                    'severity' => 4,
                ]);
            }
        }
    }
}
