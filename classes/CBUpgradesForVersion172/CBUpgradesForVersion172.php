<?php

/**
 * 2015.10.26
 *
 * - Remove the deprecated typeID and groupID columns.
 *
 * - Replace the unique index on the URI column with an index on URI and
 *   published. A request will apply to the record with the earliest published
 *   value for the URI. Various bits of user interface will warn of duplicate
 *   published URIs. This is to make creating pages faster because there won't
 *   have to be a complex rule of one negotiation for the URI value.
 */
class CBUpgradesForVersion172 {

    public static function run() {
        $SQL = <<<EOT

            SELECT  COUNT(*)
            FROM    information_schema.COLUMNS
            WHERE   TABLE_SCHEMA = DATABASE() AND
                    TABLE_NAME = 'ColbyPages' AND
                    COLUMN_NAME = 'typeID'

EOT;

        if (CBDB::SQLToValue($SQL) == 0) {
            return;
        }

        Colby::query('ALTER TABLE `ColbyPages` DROP COLUMN `typeID`');
        Colby::query('ALTER TABLE `ColbyPages` DROP COLUMN `groupID`');
        Colby::query('ALTER TABLE `ColbyPages` DROP KEY `stub`');
        Colby::query('ALTER TABLE `ColbyPages` ADD KEY `URI_published` (`URI`, `published`)');

        Colby::query('ALTER TABLE `CBPagesInTheTrash` DROP COLUMN `typeID`');
        Colby::query('ALTER TABLE `CBPagesInTheTrash` DROP COLUMN `groupID`');
        Colby::query('ALTER TABLE `CBPagesInTheTrash` CHANGE COLUMN `dataStoreID` `archiveID` BINARY(20) NOT NULL');
    }
}
