<?php

/**
 * This class is used to manage the CBDataStores table. This table holds a list
 * of data store IDs that have a data store directory. This list is continually
 * updated by the CBDataStoresFinderTask which looks for data store directories
 * on a regular schedule.
 */
final class CBDataStores {

    /**
     * @param [hex160]|hex160 $IDs
     *
     * @return null
     */
    static function deleteByID($IDs) {
        if (empty($IDs)) {
            return;
        }

        if (!is_array($IDs)) {
            $IDs = [$IDs];
        }

        $values = CBID::toSQL($IDs);
        $SQL = <<<EOT

            DELETE FROM `CBDataStores`
            WHERE `ID` IN ($values)

EOT;

        Colby::query($SQL);
    }

    /**
     * Table columns:
     *
     *      ID: The 160-bit ID of the data store.
     *
     *      timestamp: The most recent time the row was updated. This column is
     *      not terribly important, but may help identify zombied rows.
     *
     * @return void
     */
    static function CBInstall_install(): void {
        $SQL = <<<EOT

            CREATE TABLE IF NOT EXISTS `CBDataStores`
            (
                `ID`            BINARY(20) NOT NULL,
                `timestamp`     BIGINT NOT NULL,
                PRIMARY KEY     (`ID`)
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_520_ci

EOT;

        Colby::query($SQL);
    }

    /**
     * Creates or updates a row in the CBDataStores table.
     *
     * @param [hex160]|hex160 $IDs
     *
     * @return null
     */
    static function update($IDs, $timestamp = null) {
        if (empty($IDs)) {
            return;
        }

        if (!is_array($IDs)) {
            $IDs = [$IDs];
        }

        if (empty($timestamp)) {
            $timestamp = time();
        } else {
            $timestamp = intval($timestamp);
        }

        $values = array_map(function ($ID) use ($timestamp) {
            $IDAsSQL = CBID::toSQL($ID);
            return "({$IDAsSQL}, {$timestamp})";
        }, $IDs);
        $values = implode(',', $values);

        $SQL = <<<EOT

            INSERT INTO `CBDataStores`
                (`ID`, `timestamp`)
            VALUES
                {$values}
            ON DUPLICATE KEY UPDATE
                `timestamp` = {$timestamp}

EOT;

        Colby::query($SQL);
    }
}
