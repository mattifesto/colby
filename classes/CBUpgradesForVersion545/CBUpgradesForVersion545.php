<?php

final class CBUpgradesForVersion545 {

    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * @return void
     */
    static function CBInstall_install(): void {

        /* ColbyPages */

        $SQL = <<<EOT

            SELECT  COUNT(*)

            FROM    information_schema.COLUMNS

            WHERE   TABLE_SCHEMA = DATABASE() AND
                    TABLE_NAME = 'ColbyPages' AND
                    COLUMN_NAME = 'publishedBy'

        EOT;

        $publishedByColumnExists = CBConvert::valueAsInt(
            CBDB::SQLToValue($SQL)
        );

        if ($publishedByColumnExists) {
            $SQL = <<<EOT

                ALTER TABLE ColbyPages

                DROP COLUMN publishedBy

            EOT;

            Colby::query($SQL);
        }


        /* CBPagesInTheTrash */

        $SQL = <<<EOT

            SELECT  COUNT(*)

            FROM    information_schema.COLUMNS

            WHERE   TABLE_SCHEMA = DATABASE() AND
                    TABLE_NAME = 'CBPagesInTheTrash' AND
                    COLUMN_NAME = 'publishedBy'

        EOT;

        $publishedByColumnExists = CBConvert::valueAsInt(
            CBDB::SQLToValue($SQL)
        );

        if ($publishedByColumnExists) {
            $SQL = <<<EOT

                ALTER TABLE CBPagesInTheTrash

                DROP COLUMN publishedBy

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
            'CBPages',
        ];
    }
    /* CBInstall_requiredClassNames() */

}
