<?php

final class CBLogTests {

    static function noClassNameTest() {
        $serialNumber = CBLog::log((object)[
            'message' => 'This is a CBLogsTests log entry with no class name.',
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

        if ($row->severity != 3) {
            throw new Exception('The severity of the log entry should have been changed to 4.');
        }
    }
}
