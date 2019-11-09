<?php

final class CBUpgradesForVersion446 {

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $SQL = <<<EOT

            SELECT  COUNT(*)
            FROM    information_schema.COLUMNS
            WHERE   TABLE_SCHEMA = DATABASE() AND
                    TABLE_NAME = 'CBLog' AND
                    COLUMN_NAME = 'sourceClassName'

        EOT;

        if (CBConvert::valueAsInt(CBDB::SQLToValue($SQL)) === 0) {

            /**
             * Drop the "className_serial" index.
             *
             *      It will be replaced by the "sourceClassName_serial" index.
             *
             * Drop the "className_ID_serial" index.
             *
             *      This index isn't useful, so it will not be replaced.
             *
             * Drop the "severity_serial" index.
             *
             *      This index isn't useful, so it will not be replaced.
             *
             * Rename the "className" column to "sourceClassName".
             *
             * Rename the "ID" column to "modelID".
             *
             * Move the "processID" column after the "modelID" column.
             *
             * Add the "sourceID" column.
             *
             * Add the "sourceClassName_serial" index.
             */


            $SQL = <<<EOT

                ALTER TABLE CBLog

                DROP INDEX className_serial,
                DROP INDEX className_ID_serial,
                DROP INDEX severity_serial,

                CHANGE COLUMN   className   sourceClassName VARCHAR(80) NOT NULL
                    AFTER severity,

                CHANGE COLUMN   ID          modelID         BINARY(20)
                    AFTER message,

                CHANGE COLUMN   processID   processID       BINARY(20)
                    AFTER modelID,

                ADD COLUMN                  sourceID        BINARY(20)
                    AFTER sourceClassName,

                ADD INDEX sourceClassName_serial (sourceClassName, serial)

            EOT;

            Colby::query($SQL);
        }
    }
    /* CBInstall_install() */



    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBLogTable'
        ];
    }

}
