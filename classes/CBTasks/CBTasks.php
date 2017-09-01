<?php

/**
 * @deprecated 2017.08.31 Remove this class as soon as install() has run on all
 * sites.
 */
final class CBTasks {

    /**
     * @return null
     */
    static function install() {
        Colby::query("DROP TABLE IF EXISTS `CBTasks`");
    }
}
