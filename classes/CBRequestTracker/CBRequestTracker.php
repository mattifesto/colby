<?php

/**
 * @deprecated
 */
final class CBRequestTracker {

    /**
     * @return null
     */
    public static function install() {
        Colby::query('DROP TABLE IF EXISTS `CBRequests`');
    }
}
