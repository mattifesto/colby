<?php

/**
 * 2017_06_25
 *
 * Remove the CBDictionary table.
 */
final class CBUpgradesForVersion279 {

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        Colby::query('DROP TABLE IF EXISTS `CBDictionary`');
    }
}
