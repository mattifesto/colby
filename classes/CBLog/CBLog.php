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



    /* -- CBAjax interfaces -- -- -- -- -- */



    /**
     * @param object $args
     *
     *      See CBLog::entries()
     *
     * @return [object]
     */
    static function CBAjax_fetchEntries($args): array {
        return CBLog::entries($args);
    }



    /**
     * @return string
     */
    static function CBAjax_fetchEntries_getUserGroupClassName(
    ): string {
        return 'CBAdministratorsUserGroup';
    }



    /**
     * @param object $args
     *
     *      {
     *          processID: ?ID
     *      }
     *
     * @return int
     */
    static function CBAjax_fetchMostRecentSerial(
        stdClass $args
    ): int {
        return CBLog::fetchMostRecentSerial(
            CBModel::valueAsID(
                $args,
                'processID'
            )
        );
    }



    /**
     * @return string
     */
    static function CBAjax_fetchMostRecentSerial_getUserGroupClassName(
    ): string {
        return 'CBAdministratorsUserGroup';
    }



    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v657.js', cbsysurl()),
        ];
    }



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(
    ): array {
        return [
            'CBAjax',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */



    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * This interface is implemented so that other classes can say they require
     * the CBLog class because they use CBLog during install. This class does
     * not do any of its own installation, but does require that CBLogTable do
     * its installation before it can be used.
     *
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBLogTable'
        ];
    }



    /* -- functions -- -- -- -- -- */



    /**
     * @deprecated use CBLog::log()
     */
    static function addMessage($category, $severity, $message) {
        CBLog::log(
            (object)[
                'sourceClassName' => $category,
                'message' => $message,
                'severity' => $severity,
            ]
        );
    }
    /* addMessage() */



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
            CBConvert::traceToString($backtrace)
        );

        $message .= <<<EOT

            --- dl CBBackgroundOffsetColor
                --- dt
                Who called CBLog::log()?
                ---

                --- dd
                    --- pre\n{$backtraceAsJSONAsMessage}
                    ---
                ---
            ---

        EOT;

        return $message;
    }
    /* appendBacktrace() */



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
    /* buffer() */



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
    /* bufferContents() */



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
    /* bufferEndClean() */



    /**
     * @return void
     */
    static function bufferEndFlush(): void {
        if (empty(CBLog::$bufferStack)) {
            throw new Exception(
                'There is no log entry buffer.'
            );
        } else {
            $count = count(CBLog::$bufferStack);
            $buffer = array_pop(CBLog::$bufferStack);

            if ($count > 1) {
                $lowerBuffer = array_pop(CBLog::$bufferStack);

                $mergedBuffer = array_merge(
                    $lowerBuffer,
                    $buffer
                );

                array_push(
                    CBLog::$bufferStack,
                    $mergedBuffer
                );
            } else {
                array_walk(
                    $buffer,
                    function ($entry) {
                        CBLog::logForReals($entry);
                    }
                );
            }
        }
    }
    /* bufferEndFlush() */



    /**
     * @return void
     */
    static function bufferStart(): void {
        array_push(CBLog::$bufferStack, []);
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
     *
     * @NOTE 2021_02_14
     *
     *      There is now a CBLogEntry model class. Properties on a log entry
     *      should be read and written using the accessors on this class.
     *
     *      This function should potentially be moved to another function that
     *      accepts a CBLogEntry parameter.
     */
    static function entries($args = null) {
        $whereAsSQL = [];

        $afterSerial = CBModel::valueAsInt(
            $args,
            'afterSerial'
        );

        if ($afterSerial !== null) {
            $whereAsSQL[] = "`serial` > {$afterSerial}";
        }

        $afterTimestamp = CBModel::valueAsInt(
            $args,
            'afterTimestamp',
        );

        if ($afterTimestamp !== null) {
            $whereAsSQL[] = "`timestamp` > {$afterTimestamp}";
        }

        /* modelID */

        $modelID = CBModel::valueAsID($args, 'modelID');

        if (!empty($modelID)) {
            $modelIDAsSQL = CBID::toSQL($modelID);
            array_push($whereAsSQL, "modelID = {$modelIDAsSQL}");
        }

        /* sourceClassName */

        $sourceClassName = CBModel::valueToString(
            $args,
            'sourceClassName'
        );

        if (empty($sourceClassName)) {
            $sourceClassName = CBModel::valueToString(
                $args,
                'className'
            );
        }

        if (!empty($sourceClassName)) {
            $sourceClassNameAsSQL = CBDB::stringToSQL($sourceClassName);
            $whereAsSQL[] = "`sourceClassName` = {$sourceClassNameAsSQL}";
        }

        /* sourceID */

        $sourceID = CBModel::valueAsID($args, 'sourceID');

        if (!empty($sourceID)) {
            $sourceIDAsSQL = CBID::toSQL($sourceID);
            $whereAsSQL[] = "sourceID = {$sourceIDAsSQL}";
        }

        $lowestSeverity = CBModel::valueAsInt(
            $args,
            'lowestSeverity'
        );

        if ($lowestSeverity !== null) {
            $whereAsSQL[] = "`severity` <= {$lowestSeverity}";
        }

        $processID = CBModel::valueAsID(
            $args,
            'processID'
        );

        if ($processID !== null) {
            $processIDAsSQL = CBID::toSQL($processID);
            $whereAsSQL[] = "`processID` = {$processIDAsSQL}";
        }

        if (empty($whereAsSQL)) {
            $whereAsSQL = '';
        } else {
            $whereAsSQL = 'WHERE ' . implode(' AND ', $whereAsSQL);
        }

        $mostRecentDescending = CBModel::valueToBool(
            $args,
            'mostRecentDescending'
        );

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
    /* entries() */



    /**
     * @param ?ID $processID
     *
     * @return int
     */
    static function fetchMostRecentSerial(?string $processID = null): int {
        $where = '';

        if ($processID !== null) {
            $processIDAsSQL = CBID::toSQL($processID);
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
    /* fetchMostRecentSerial() */



    /**
     * Create a log entry.
     *
     * See CBLog::install() for detailed descriptions of the CBLog table
     * columns.
     *
     * @param object $args
     *
     *      {
     *          message: string (CBMessage)
     *
     *              The message must have a length greater than 0.
     *
     *              @TODO 2019_12_29
     *
     *                  change this to 'cbmessage'
     *
     *          modelID: ?ID
     *
     *              If the log entry is related to a specific model, pass that
     *              model's ID in this argument.
     *
     *          severity: ?int
     *
     *              The severity of the log entry. Entries with a severity less
     *              than 4 will also be sent to error_log(). This value will be
     *              clamped between 0 and 7.
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
        $preparedEntry = CBLog::prepareEntry($args);

        if (empty(CBLog::$bufferStack)) {
            CBLog::logForReals(
                $preparedEntry
            );
        } else {
            $buffer = array_pop(CBLog::$bufferStack);

            array_push(
                $buffer,
                $preparedEntry
            );

            array_push(
                CBLog::$bufferStack,
                $buffer
            );
        }
    }
    /* log() */



    /**
     * @param object $args
     *
     *      This function now only accepts a prepared entry as its parameter.
     *
     * @return void
     *
     * @NOTE 2021_02_14
     *
     *      This function can probably be changed to only accept official
     *      CBLogEntry specs or models as its parameter.
     */
    private static function logForReals(
        stdClass $args
    ): void {
        /* sourceClassName */

        $sourceClassName = CBModel::valueToString(
            $args,
            'sourceClassName'
        );

        $sourceClassNameAsSQL = CBDB::stringToSQL(
            $sourceClassName
        );


        /* sourceID */

        $sourceCBID = CBLogEntry::getSourceCBID(
            $args
        );

        $sourceCBIDAsSQL = (
            $sourceCBID === null ?
            'NULL' :
            CBID::toSQL($sourceCBID)
        );


        /* severity */

        $severityAsSQL = CBModel::valueAsInt(
            $args,
            'severity'
        ) ?? 0;


        /* modelID */

        $modelID = CBModel::valueAsCBID(
            $args,
            'modelID'
        );

        $modelIDAsSQL =
        CBID::valueIsCBID($modelID) ?
        CBID::toSQL($modelID) :
        'NULL';


        /* message */

        $message = CBModel::valueToString(
            $args,
            'message'
        );

        $message = CBLog::appendBacktrace($message);
        $messageAsSQL = CBDB::stringToSQL($message);


        /* process ID */

        $processID = CBModel::valueAsCBID(
            $args,
            'processID'
        );

        $processIDAsSQL =
        CBID::valueIsCBID($processID) ?
        CBID::toSQL($processID) :
        'NULL';


        /* timestamp */

        $timestampAsSQL = CBModel::valueAsInt(
            $args,
            'timestamp'
        );


        /* SQL */

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
                {$sourceCBIDAsSQL},
                {$timestampAsSQL}
            )

        EOT;

        Colby::query($SQL);
    }
    /* logForReals() */



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

        CBLog::log(
            (object)[
                'message' => $message,
                'severity' => 7,
                'sourceClassName' => __CLASS__,
                'sourceID' => '0206ded5aab567162f0e2651b492091181d5a377',
            ]
        );
    }
    /* removeExpiredEntries() */



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
    /* severityToDescription() */



    /**
     * This function takes the original arguments to CBLog::log() and prepares
     * an official log entry model with them.
     *
     * This function may make additional log entries to point out issues with a
     * log entry submitted to CBLog::log()
     *
     * @param object $args
     *
     * @return object
     *
     * @NOTE 2021_02_14
     *
     *      There is now a CBLogEntry model class. Properties on a log entry
     *      should be read and written using the accessors on this class.
     */
    private static function prepareEntry(
        stdClass $args
    ): stdClass {
        $entrySourceClassName = CBModel::valueToString(
            $args,
            'sourceClassName'
        );

        if (empty($entrySourceClassName)) {
            $entrySourceClassName = CBModel::valueToString(
                $args,
                'className' /* deprecated */
            );
        }

        $entryMessage = CBModel::valueToString(
            $args,
            'message'
        );

        if (empty($entryClassName) || empty($entryMessage)) {
            $entryAsCBMessage = CBMessageMarkup::stringToMessage(
                CBConvert::valueToPrettyJSON($args)
            );

            $stackTraceAsCBMessage = CBMessageMarkup::stringToMessage(
                CBConvert::traceToString(debug_backtrace())
            );

            if (empty($entrySourceClassName)) {
                $cbmessage = <<<EOT

                    CBLog_warning_noClassName: A log entry was submitted that
                    does not have a source class name.

                    --- pre\n{$entryAsCBMessage}
                    ---

                    --- pre\n{$stackTraceAsCBMessage}
                    ---

                EOT;

                CBLog::log(
                    (object)[
                        'sourceClassName' => __CLASS__,
                        'message' => $cbmessage,
                        'severity' => 4,
                    ]
                );
            }

            if (empty($entryMessage)) {
                $cbmessage = <<<EOT

                    CBLog_warning_noMessage: A log entry was submitted that does
                    not have a message.

                    --- pre\n{$entryAsCBMessage}
                    ---

                    --- pre\n{$stackTraceAsCBMessage}
                    ---

                EOT;

                CBLog::log(
                    (object)[
                        'sourceClassName' => __CLASS__,
                        'message' => $cbmessage,
                        'severity' => 4,
                    ]
                );
            }
        }


        /* severity */

        $defaultSeverity = 6; // informational

        $severity = CBModel::valueAsInt(
            $args,
            'severity'
        ) ?? 6;

        if ($severity < 0) {
            $severity = 0;
        } else if ($severity > 7) {
            $severity = 7;
        }


        /* sourceID */

        $sourceID = CBModel::valueAsCBID(
            $args,
            'sourceID'
        );


        /* modelID */

        $modelID = CBModel::valueAsCBID(
            $args,
            'modelID'
        );

        if ($modelID === null) {
            /* deprecated */
            $modelID = CBModel::valueAsCBID(
                $args,
                'ID'
            );
        }

        if ($modelID === null) {
            $modelID = CBID::peek();
        }


        /* processID */

        $processID = CBProcess::ID();


        /* timestamp */

        $timestamp = time();


        /* prepared entry */

        $preparedEntry = (object)[
            'className' => 'CBLogEntry',

            'message' => $entryMessage,

            'modelID' => $modelID,

            'processID' => $processID,

            'severity' => $severity,

            'sourceClassName' => $entrySourceClassName,

            'timestamp' => $timestamp,
        ];

        CBLogEntry::setSourceCBID(
            $preparedEntry,
            $sourceID
        );

        return $preparedEntry;
    }
    /* prepareEntry() */

}
