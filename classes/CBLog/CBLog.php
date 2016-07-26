<?php

final class CBLog {

    /**
     * @return null
     */
    public static function install() {
        $SQL = <<<EOT

            CREATE TABLE IF NOT EXISTS `CBLog` (
                `ID` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                `category` VARCHAR(80) NOT NULL,
                `message` TEXT NOT NULL,
                `severity` TINYINT NOT NULL,
                `timestamp` BIGINT NOT NULL,
                PRIMARY KEY (`ID`),
                KEY `timestamp` (`timestamp`),
                KEY `category_timestamp` (`category`, `timestamp`),
                KEY `severity_timestamp` (`severity`, `timestamp`)
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8
            COLLATE=utf8_unicode_ci

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
    public static function addMessage($category, $severity, $message) {
        try {
            if ($severity < 4) {
                try {
                    error_log($message);
                } catch (Exception $ignoredException) {
                    // We can't do much if error_log() fails.
                }
            }

            $categoryAsSQL = CBDB::stringToSQL($category);
            $messageAsSQL = CBDB::stringToSQL($message);
            $severityAsSQL = (int)$severity;
            $timestampAsSQL = time();

            $SQL = <<<EOT

                INSERT INTO `CBLog` (
                    `category`,
                    `message`,
                    `severity`,
                    `timestamp`
                ) VALUES (
                    {$categoryAsSQL},
                    {$messageAsSQL},
                    {$severityAsSQL},
                    {$timestampAsSQL}
                )

EOT;

            Colby::query($SQL);
        } catch (Exception $innerException) {
            try {
                error_log(__METHOD__ . '() inner exception: ' . $innerException->getMessage());
            } catch (Exception $ignoredException) {
                // We can't do much if error_log() fails.
            }
        }
    }

    /**
     * @param int $args->sinceTimestamp
     * @param int $args->minSeverity
     *
     * @return [stdClass]
     */
    public static function entries($args) {
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

        $whereAsSQL = implode(' AND ', $whereAsSQL);

        $SQL = <<<EOT

            SELECT `category`, `message`, `timestamp`
            FROM `CBLog`
            WHERE {$whereAsSQL}
            ORDER BY `timestamp`

EOT;

        return CBDB::SQLToObjects($SQL);
    }
}
