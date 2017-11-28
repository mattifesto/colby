<?php

/**
 * 2016.04.28
 *
 * Remove the page lists table. For page list functionality use page kinds or
 * menus.
 */
final class CBUpgradesForVersion191 {

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        Colby::query('DROP TABLE IF EXISTS `CB' . 'PageLists`');
    }

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return ['CBUpgradesForVersion188'];
    }
}
