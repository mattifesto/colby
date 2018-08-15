<?php

final class CBLogTable {

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
     * @NOTE Future upgrade thoughts
     *
     *      className -> sourceClassName
     *
     *          The className property should be renamed to sourceClassName
     *          because a className property on an object makes it look like
     *          it's a model which is not the case here.
     *
     *      add sourceID
     *
     *          This property is used to identify the specific source code that
     *          created the log entry. For instance, if the CBTextView class
     *          creates a log entry each time it upgrades a CBTextView model to
     *          a CBMessageView, it would use a sourceID each time it created
     *          the log entry. This property allows to test to determine if a
     *          log entry that is expected to be created actually is created and
     *          it also allows log browsers to see all entries created that are
     *          from this exact source.
     *
     *      ID -> modelID
     *
     *          The fact that this property is named ID makes it appear that
     *          this property is the ID of the log entry. It is not, it is the
     *          ID of a specific model associated with a log entry.
     *
     * @return void
     */
    static function CBInstall_install(): void {
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
}
