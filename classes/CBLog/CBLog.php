<?php

final class CBLog {

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
     * @return null
     */
    static function install() {
        $SQL = <<<EOT

            CREATE TABLE IF NOT EXISTS `CBLog` (
                `category` VARCHAR(80) NOT NULL,
                `processID` BINARY(20),
                `message` TEXT NOT NULL,
                `serial` SERIAL,
                `severity` TINYINT NOT NULL,
                `timestamp` BIGINT NOT NULL,
                KEY `category_serial` (`category`, `serial`),
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
     * Adds a message to the CBLog table.
     *
     * This function will never throw an exception so it can be called without
     * fear of recursive exceptions.
     *
     * @param string $category
     * @param string $severity
     *
     *  RFC3164 Severity Codes
     *  https://tools.ietf.org/html/rfc3164
     *
     *  0  Emergency: system is unusable
     *  1  Alert: action must be taken immediately
     *  2  Critical: critical conditions
     *  3  Error: error conditions
     *  4  Warning: warning conditions
     *  5  Notice: normal but significant condition
     *  6  Informational: informational messages
     *  7  Debug: debug-level messages
     *
     *  If the severity is less than 4, the message will also be sent to
     *  error_log().
     *
     * @param string $message
     */
    static function addMessage($category, $severity, $message) {
        try {
            if ($severity < 4) {
                try {
                    error_log(__METHOD__ . ', source: ' . $category . ', ' . $message);
                } catch (Exception $ignoredException) {
                    // We can't do much if error_log() fails.
                }
            }

            $categoryAsSQL = CBDB::stringToSQL($category);
            $processID = CBProcess::ID();
            $processIDAsSQL = ($processID === null) ? 'NULL' : CBHex160::toSQL($processID);
            $messageAsSQL = CBDB::stringToSQL($message);
            $severityAsSQL = (int)$severity;
            $timestampAsSQL = time();

            $SQL = <<<EOT

                INSERT INTO `CBLog` (
                    `category`,
                    `processID`,
                    `message`,
                    `severity`,
                    `timestamp`
                ) VALUES (
                    {$categoryAsSQL},
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
     *          category: string
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

            SELECT `category`, `message`, `serial`, `severity`, `timestamp`
            FROM `CBLog`
            {$whereAsSQL}
            ORDER BY `serial` {$descAsSQL}
            LIMIT 500

EOT;

        return CBDB::SQLToObjects($SQL);
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

        CBLog::addMessage(__METHOD__, 6, "Removed {$count} expired entries from the CBLog table.");

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
