<?php

final class CBUpgradesForVersion380 {

    /**
     * @return void
     */
    static function CBInstall_install(): void {

        /**
         * Check for DATA_TYPE in this query because eventually the BINARY
         * column "archiveID" will be renamed to "ID"
         */

        $SQL = <<<EOT

            SELECT  COUNT(*)
            FROM    information_schema.COLUMNS
            WHERE   TABLE_SCHEMA = DATABASE() AND
                    TABLE_NAME = 'ColbyPages' AND
                    COLUMN_NAME = 'ID' AND
                    DATA_TYPE = 'bigint'

EOT;

        if (CBDB::SQLToValue($SQL) == 1) {
            $SQL = <<<EOT

                ALTER TABLE ColbyPages
                DROP COLUMN ID,
                DROP KEY archiveID,
                ADD PRIMARY KEY (archiveID),
                DROP FOREIGN KEY ColbyPages_publishedBy

EOT;

            Colby::query($SQL);

            $SQL = <<<EOT

                ALTER TABLE CBPagesInTheTrash
                DROP COLUMN ID,
                DROP KEY archiveID,
                ADD PRIMARY KEY (archiveID),
                DROP FOREIGN KEY CBPagesInTheTrash_publishedBy

EOT;

            Colby::query($SQL);
        }
    }

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return ['CBUpgradesForVersion351'];
    }

}
