<?php

/**
 * 2017.10.19
 *
 * Upgrade all tables and columns to use the `utf8mb4` character set instead of
 * `utf8`. The `utf8` character set is not actually UTF-8 compliant, which is
 * why `utf8mb4` was added later.
 */
final class CBUpgradesForVersion346 {

    /**
     * @return null
     */
    static function run() {

        /**
         * Check to see if the CBDataStores table has the old collation. If it
         * does, do the upgrade. If it doesn't, exit early because the upgrade
         * is already complete.
         */

        $SQL = <<<EOT

            SELECT  COUNT(*)
            FROM    information_schema.TABLES
            WHERE   TABLE_SCHEMA = DATABASE() AND
                    TABLE_NAME = 'ColbyPages' AND
                    TABLE_COLLATION = 'utf8_unicode_ci';

EOT;

        if (CBDB::SQLToValue($SQL) == 0) {
            return;
        }

        /**
         * @NOTE
         *
         *      Upgrading tables from utf8 to utf8mb4 is a bit of a complex
         *      task. It's not because it's difficult, it's because there are so
         *      many different suggested ways of doing it and all are slightly
         *      wrong on some way or another. Sometimes the suggestion is
         *      outdated, sometimes it's just not correct for every situation.
         *
         *      Fact 1: Any data in a utf8 column is already in utf8mb4 format.
         *      The reason for the upgrade is that utf8 columns reject some
         *      extended characters, such as emoji, that utf8mb4 will allow.
         *
         *      Fact 2: Because of Fact 1, no data conversion is required for
         *      this upgrade.
         *
         *      Fact 3: Using the CONVERT TO CHARACTER SET option with ALTER
         *      TABLE will automatically do data conversion (which we don't
         *      need) and automatically grow the column types to fit larger
         *      characters (which we don't want). So we do not use that option.
         *
         *      Fact 4: The barely mentioned and poorly documented
         *      `utf8mb4_unicode_520_ci` collation is the currently correct
         *      collation to be used for MySQL 5.6 and above. There is an even
         *      more updated collation for MySQL 8.0 but that will not be
         *      available for some time.
         *
         *      Reference:
         *      https://dev.mysql.com/doc/refman/5.6/en/charset-unicode-conversion.html
         *
         *      Here the exact operations per table, similar to but not the same
         *      as the reference:
         *
         *      - Change the default character set and collation for the table
         *      so that future added texty colums will inherit them.
         *
         *      - Modify each texty column to be exactly the same type but with
         *      an updated character set and collation. We must do this because
         *      the character set and collation for each column is set at table
         *      creation time to the default character set and collation for the
         *      table. These column properties are not inherted, so we have to
         *      updated them.
         */

        $tables = [
            (object)[
                'name' => 'CBDataStores',
                'columns' => [
                ],
            ],
            (object)[
                'name' => 'CBImages',
                'columns' => [
                    (object)[
                        'pre' => '`extension` VARCHAR(10)',
                        'post' => 'NOT NULL',
                    ],
                ],
            ],

            /**
             * CBLog updates are left out because CBUpgradesForVersion351
             * drops and re-creates the table.
             */

            (object)[
                'name' => 'CBModelAssociations',
                'columns' => [
                    (object)[
                        'pre' => '`className` VARCHAR(80)',
                        'post' => 'NOT NULL',
                    ],
                ],
            ],
            (object)[
                'name' => 'CBModelVersions',
                'columns' => [
                    (object)[
                        'pre' => '`modelAsJSON` LONGTEXT',
                        'post' => 'NOT NULL',
                    ],
                    (object)[
                        'pre' => '`specAsJSON` LONGTEXT',
                        'post' => 'NOT NULL',
                    ],
                ],
            ],
            (object)[
                'name' => 'CBModels',
                'columns' => [
                    (object)[
                        'pre' => '`className` VARCHAR(80)',
                        'post' => 'NOT NULL',
                    ],
                    (object)[
                        'pre' => '`title` TEXT',
                        'post' => 'NOT NULL',
                    ],
                ],
            ],
            (object)[
                'name' => 'CBPagesInTheTrash',
                'columns' => [
                    (object)[
                        'pre' => '`keyValueData` LONGTEXT',
                        'post' => 'NOT NULL',
                    ],
                    (object)[
                        'pre' => '`className` VARCHAR(80)',
                    ],
                    (object)[
                        'pre' => '`classNameForKind` VARCHAR(80)',
                    ],
                    (object)[
                        'pre' => '`URI` VARCHAR(100)',
                    ],
                    (object)[
                        'pre' => '`titleHTML` TEXT',
                        'post' => 'NOT NULL',
                    ],
                    (object)[
                        'pre' => '`subtitleHTML` TEXT',
                        'post' => 'NOT NULL',
                    ],
                    (object)[
                        'pre' => '`thumbnailURL` VARCHAR(200)',
                    ],
                    (object)[
                        'pre' => '`searchText` LONGTEXT',
                    ],
                ],
            ],

            /**
             * CBTasks2 updates are left out because CBUpgradesForVersion351
             * drops and re-creates the table.
             */

            (object)[
                'name' => 'ColbyPages',
                'columns' => [
                    (object)[
                        'pre' => '`keyValueData` LONGTEXT',
                        'post' => 'NOT NULL',
                    ],
                    (object)[
                        'pre' => '`className` VARCHAR(80)',
                    ],
                    (object)[
                        'pre' => '`classNameForKind` VARCHAR(80)',
                    ],
                    (object)[
                        'pre' => '`URI` VARCHAR(100)',
                    ],
                    (object)[
                        'pre' => '`titleHTML` TEXT',
                        'post' => 'NOT NULL',
                    ],
                    (object)[
                        'pre' => '`subtitleHTML` TEXT',
                        'post' => 'NOT NULL',
                    ],
                    (object)[
                        'pre' => '`thumbnailURL` VARCHAR(200)',
                    ],
                    (object)[
                        'pre' => '`searchText` LONGTEXT',
                    ],
                ],
            ],
            (object)[
                'name' => 'ColbyUsers',
                'columns' => [
                    (object)[
                        'pre' => '`facebookAccessToken` VARCHAR(255)',
                    ],
                    (object)[
                        'pre' => '`facebookName` VARCHAR(100)',
                        'post' => 'NOT NULL',
                    ],
                    (object)[
                        'pre' => '`facebookFirstName` VARCHAR(50)',
                        'post' => 'NOT NULL',
                    ],
                    (object)[
                        'pre' => '`facebookLastName` VARCHAR(50)',
                        'post' => 'NOT NULL',
                    ],
                ],
            ],
            (object)[
                'name' => 'ColbyUsersWhoAreAdministrators',
                'columns' => [
                ],
            ],
            (object)[
                'name' => 'ColbyUsersWhoAreDevelopers',
                'columns' => [
                ],
            ],
        ];

        foreach ($tables as $table) {
            $columns = array_map(function ($column) {
                $pre = CBModel::value($column, 'pre', '');
                $post = CBModel::value($column, 'post', '');

                return ", MODIFY {$pre} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci {$post}";
            }, $table->columns);

            $columns = implode('', $columns);

            $SQL = <<<EOT

                ALTER TABLE `{$table->name}`
                    DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_520_ci
                    {$columns}

EOT;

            Colby::query($SQL);
        }
    }
}
