<?php

/**
 * 2017.06.25
 *
 * Remove the `CBDictionary` table.
 */
final class CBUpgradesForVersion279 {

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        Colby::query('DROP TABLE IF EXISTS `CBDictionary`');
    }

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return ['CBUpgradesForVersion191'];
    }
}
