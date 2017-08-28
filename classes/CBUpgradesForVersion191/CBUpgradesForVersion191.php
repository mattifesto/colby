<?php

/**
 * 2016.04.28
 *
 * Remove the page lists table. For page list functionality use page kinds or
 * menus.
 */
final class CBUpgradesForVersion191 {

    /**
     * @return null
     */
    static function run() {
        Colby::query('DROP TABLE IF EXISTS `CB' . 'PageLists`');
    }
}
