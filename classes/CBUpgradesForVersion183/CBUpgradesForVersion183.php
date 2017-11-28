<?php

/**
 * 2016.02.10
 *
 * - Add the created and modified columns to support finding and sorting pages
 *   in the administration interface.
 *
 * - Add indexes used by the administration interface.
 */
class CBUpgradesForVersion183 {

    static function CBInstall_install(): void {
        $SQL = <<<EOT

            SELECT  COUNT(*)
            FROM    information_schema.COLUMNS
            WHERE   TABLE_SCHEMA = DATABASE() AND
                    TABLE_NAME = 'ColbyPages' AND
                    COLUMN_NAME = 'created'

EOT;

        if (CBDB::SQLToValue($SQL) == 1) {
            return;
        }

        Colby::query('ALTER TABLE `ColbyPages` ADD COLUMN `created` BIGINT NOT NULL AFTER `classNameForKind`');
        Colby::query('ALTER TABLE `ColbyPages` ADD COLUMN `modified` BIGINT NOT NULL AFTER `iteration`');
        Colby::query('ALTER TABLE `ColbyPages` ADD KEY `created` (`created`)');
        Colby::query('ALTER TABLE `ColbyPages` ADD KEY `modified` (`modified`)');
        Colby::query('ALTER TABLE `ColbyPages` ADD KEY `classNameForKind_created` (`classNameForKind`, `created`)');
        Colby::query('ALTER TABLE `ColbyPages` ADD KEY `classNameForKind_modified` (`classNameForKind`, `modified`)');

        Colby::query('ALTER TABLE `CBPagesInTheTrash` ADD COLUMN `created` BIGINT NOT NULL AFTER `classNameForKind`');
        Colby::query('ALTER TABLE `CBPagesInTheTrash` ADD COLUMN `modified` BIGINT NOT NULL AFTER `iteration`');
    }

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return ['CBUpgradesForVersion178'];
    }
}
