<?php

/**
 * 2017.06.25
 *
 * Remove the `CBDictionary` table.
 */
final class CBUpgradesForVersion279 {

    /**
     * @return null
     */
    static function run() {
        Colby::query('DROP TABLE IF EXISTS `CBDictionary`');
    }
}
