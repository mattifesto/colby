<?php

final class CBLogTable {

    /**
     * Columns:
     *
     *      message
     *
     *          The message of the log entry. The very first line should provide
     *          a short understandable summary of the entire log message.
     *
     *          Bad first lines:
     *
     *          <empty>
     *          Done
     *          1/15
     *
     *          Good first lines:
     *
     *          The page "My Blog Post" was checked for deprecated views.
     *          The data directory "data/a5/23" was checked for files.
     *          The log table was cleared of 35 very old entries.
     *
     *      modelID
     *
     *          The modelID is provided if the log entry pertains to a specific
     *          model.
     *
     *      processID
     *
     *          This is automatically set if a process ID has been set when the
     *          log entry is created.
     *
     *      serial
     *
     *          An automatically incremented number to establish an exact order
     *          of log entries.
     *
     *      severity
     *
     *          A number 0-7 indicating the severity of the log message.
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
     *      sourceClassName
     *
     *          This class name is specified by developer to provide name of the
     *          class that created the log entry.
     *
     *      sourceID
     *
     *          This ID is specified by a developer to provide an easy way to
     *          find the exact location in the source code that created a log
     *          entry. Searching the source code for this ID with a tool like
     *          "ack" will find that location.
     *
     *          Only a single source code call to CBLog::log() should use a
     *          unique source ID. If that source code is called multiple times,
     *          then there will be multiple log entries that share the same
     *          source ID.
     *
     *      timestamp
     *
     *          The timestamp the log entry was created.
     *
     * Indexes:
     *
     *      PRIMARY KEY (serial)
     *
     *          This is an important index because we usually list log entries
     *          in order of creation ascending or descending.
     *
     *      KEY processID_serial (processID, serial)
     *
     *          This index is used to track log entries made while a process is
     *          running or after a process has run, such as the model import
     *          process.
     *
     *      KEY sourceClassName_serial (sourceClassName, serial)
     *
     *          This index is used when a source class name is selected to
     *          filter the log entires on the log admin page.
     *
     * @return void
     */
    static function
    CBInstall_install(
    ): void
    {
        $SQL =
        <<<EOT

            CREATE TABLE
            IF NOT EXISTS
            CBLog
            (
                message
                TEXT NOT NULL,

                modelID
                BINARY(20),

                processID
                BINARY(20),

                serial
                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,

                severity
                TINYINT NOT NULL,

                sourceClassName
                VARCHAR(80) NOT NULL,

                sourceID
                BINARY(20),

                timestamp
                BIGINT NOT NULL,



                PRIMARY KEY
                (
                    serial
                ),

                KEY
                processID_serial
                (
                    processID,
                    serial
                ),

                KEY
                sourceClassName_serial (
                    sourceClassName,
                    serial
                )
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_520_ci

        EOT;

        Colby::query(
            $SQL
        );
    }
    // CBInstall_install()

}
