<?php

final class CBLogMaintenanceTask {

    /**
     * When we do an install or update schedule this task to run. This is also
     * the time at which the priority of the task can be changed if needed.
     *
     * @return null
     */
    static function install() {
        CBTasks2::updateTask(__CLASS__, CBHex160::zero());
    }

    /**
     * @param hex160 $ID
     *
     * @return null
     */
    static function CBTasks2_Execute($ID) {
        if ($ID !== CBHex160::zero()) {
            throw new RuntimeException("An invalid ID {$ID} was passed to " . __METHOD__ . '()');
        }

        CBLog::removeExpiredEntries();

        return (object)[
            'scheduled' => time() + (60 * 60 * 24), /* 24 hours from now */
        ];
    }
}
