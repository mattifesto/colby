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
                `ID` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `category` VARCHAR(80) NOT NULL,
                `message` TEXT NOT NULL,
                `modelAsJSON` LONGTEXT,
                `severity` TINYINT NOT NULL,
                `timestamp` BIGINT NOT NULL,
                PRIMARY KEY (`ID`),
                KEY `timestamp` (`timestamp`),
                KEY `category_timestamp` (`category`, `timestamp`),
                KEY `severity_timestamp` (`severity`, `timestamp`)
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_520_ci

EOT;

        Colby::query($SQL);

        /**
         * @NOTE Upgrade 2017.07.01
         *
         *      Add the `modelAsJSON` column
         */

        if (!CBDBA::tableHasColumnNamed('CBLog', 'modelAsJSON')) {
            $SQL = <<<EOT

                ALTER TABLE `CBLog`
                ADD COLUMN  `modelAsJSON` LONGTEXT
                AFTER       `message`

EOT;

            Colby::query($SQL);
        }
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
     * @param object? $model (The exact properties are under consideration)
     *
     *      {
     *          exceptionStackTrace: string
     *          text: string
     *      }
     */
    static function addMessage($category, $severity, $message, stdClass $model = null) {
        try {
            if ($severity < 4) {
                try {
                    error_log(__METHOD__ . ', source: ' . $category . ', ' . $message);
                } catch (Exception $ignoredException) {
                    // We can't do much if error_log() fails.
                }
            }

            $categoryAsSQL = CBDB::stringToSQL($category);
            $messageAsSQL = CBDB::stringToSQL($message);
            $severityAsSQL = (int)$severity;
            $timestampAsSQL = time();

            if ($model) {
                $modelAsJSONAsSQL = CBDB::stringToSQL(json_encode($model));
            } else {
                $modelAsJSONAsSQL = 'NULL';
            }

            $SQL = <<<EOT

                INSERT INTO `CBLog` (
                    `category`,
                    `message`,
                    `modelAsJSON`,
                    `severity`,
                    `timestamp`
                ) VALUES (
                    {$categoryAsSQL},
                    {$messageAsSQL},
                    {$modelAsJSONAsSQL},
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
     * @param int $args->sinceTimestamp
     * @param int $args->minSeverity
     *
     * @return  {
     *              category: string
     *              message: string
     *              model: object
     *              severity: int
     *              timestamp: int
     *          }
     */
    static function entries($args = null) {
        $whereAsSQL = [];

        if (isset($args->sinceTimestamp)) {
            $sinceTimestampAsSQL = (int)$args->sinceTimestamp;
            $whereAsSQL[] = "`timestamp` > {$sinceTimestampAsSQL}";
        }

        if (isset($args->minSeverity)) {
            $minSeverityAsSQL = (int)$args->minSeverity;
            $whereAsSQL[] = "`severity` <= {$minSeverityAsSQL}";
        } else if (isset($args->category)) {

        }

        if (empty($whereAsSQL)) {
            $whereAsSQL = '';
        } else {
            $whereAsSQL = 'WHERE ' . implode(' AND ', $whereAsSQL);
        }

        $SQL = <<<EOT

            SELECT `category`, `message`, `modelAsJSON` AS `model`, `severity`, `timestamp`
            FROM `CBLog`
            {$whereAsSQL}
            ORDER BY `ID` DESC
            LIMIT 500

EOT;

        $entries = CBDB::SQLToObjects($SQL);

        foreach ($entries as $entry) {
            if (!empty($entry->model)) {
                $entry->model = json_decode($entry->model);
            }
        }

        return $entries;
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
