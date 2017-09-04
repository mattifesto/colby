<?php

/**
 * This class is used to manage the CBDataStores table. This table holds a list
 * of data store IDs that have a data store directory. This list is continually
 * updated by the CBDataStoresFinderTask which looks for data store directories
 * on a regular schedule.
 */
final class CBDataStores {

    /**
     * @return null
     */
    static function install() {
        $SQL = <<<EOT

            CREATE TABLE IF NOT EXISTS `CBDataStores`
            (
                `ID`            BINARY(20) NOT NULL,
                `timestamp`     BIGINT NOT NULL,
                PRIMARY KEY     (`ID`)
            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8
            COLLATE=utf8_unicode_ci

EOT;

        Colby::query($SQL);
    }
}
