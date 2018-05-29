<?php

final class CBLogTests {

    static function noClassNameTest() {
        $message = <<<EOT

            This is a test log entry created by noClassNameTest() in
            CBLogTests.php.

            This test creates this log entry with no class name and a severity
            of 7 (debug). The log entry should have its severity raised to 4
            (warning) because it has no class name.

EOT;

        $serialNumber = CBLog::log((object)[
            'message' => $message,
            'severity' => 7,
        ]);

        $SQL = <<<EOT

            SELECT  `className`, `message`, `severity`
            FROM    `CBLog`
            WHERE   `serial` = {$serialNumber}

EOT;

        $row = CBDB::SQLToObject($SQL);

        if ($row->severity != 4) {
            throw new Exception('The severity of the log entry should have been changed to 4.');
        }
    }

    static function noMessageTest() {
        $serialNumber = CBLog::log((object)[
            'className' => __CLASS__,
            'severity' => 7,
        ]);

        $SQL = <<<EOT

            SELECT  `className`, `message`, `severity`
            FROM    `CBLog`
            WHERE   `serial` = {$serialNumber}

EOT;

        $row = CBDB::SQLToObject($SQL);

        if ($row->severity != 4) {
            throw new Exception('The severity of the log entry should have been changed to 4.');
        }
    }
}
