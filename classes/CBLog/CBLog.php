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
            'className' => $category,
            'message' => $message,
            'severity' => $severity,
        ]);
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
     *          className: string?
     *
     *              If specified, will only fetch log entries with the specified
     *              class name.
     *
     *          lowestSeverity: int?
     *
     *              If specified, will only fetch log entries with the specified
     *              severity or greater severity. Note: Severity becomes greater
     *              as the severity integer gets lower.
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

        $className = CBModel::valueToString($args, 'className');

        if (!empty($className)) {
            $classNameAsSQL = CBDB::stringToSQL($className);
            $whereAsSQL[] = "`className` = {$classNameAsSQL}";
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

            SELECT  className,
                    LOWER(HEX(ID)) AS ID,
                    message,
                    LOWER(HEX(processID)) as processID,
                    serial,
                    severity,
                    timestamp
            FROM    CBLog
            {$whereAsSQL}
            ORDER BY serial {$descAsSQL}
            LIMIT 500

EOT;

        return CBDB::SQLToObjects($SQL);
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
     *          className: string
     *
     *              deprecated: use sourceClassName
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
     *
     *          sourceClassName: string
     *
     *              The name of the class that created the log entry. This
     *              is usually the class calling CBlog::log().
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

        $sourcClassNameAsSQL = CBDB::stringToSQL($sourceClassName);

        /* severity */

        $severity = CBModel::valueAsInt($args, 'severity');

        if (empty($severity)) {
            $severity = 6; /* informational */
        }

        $severityAsSQL = (int)$severity;

        /* ID */

        $ID = CBModel::valueAsID($args, 'ID');

        if (empty($ID)) {
            $ID = CBID::peek();
        }

        $IDAsSQL = ($ID === null) ? 'NULL' : CBHex160::toSQL($ID);

        /* message */

        $message = CBModel::valueToString($args, 'message');
        $messageAsSQL = CBDB::stringToSQL($message);

        /* process ID */

        $processID = CBProcess::ID();
        $processIDAsSQL = ($processID === null) ? 'NULL' : CBHex160::toSQL($processID);

        /* timestamp */

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
                {$sourcClassNameAsSQL},
                {$IDAsSQL},
                {$processIDAsSQL},
                {$messageAsSQL},
                {$severityAsSQL},
                {$timestampAsSQL}
            )

EOT;

        Colby::query($SQL);
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
