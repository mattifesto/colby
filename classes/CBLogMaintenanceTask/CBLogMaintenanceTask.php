<?php

final class CBLogMaintenanceTask {

    /**
     * When we do an install or update schedule this task to run. This is also
     * the time at which the priority of the task can be changed if needed.
     *
     * @return void
     */
    static function CBInstall_install(): void {
        CBTasks2::updateTask(__CLASS__, CBHex160::zero());
    }

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return ['CBLog', 'CBTasks2'];
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

        CBLog::log((object)[
            'className' => __CLASS__,
            'message' => 'CBLogMaintenanceTask performed log maintenance.',
            'severity' => 6,
        ]);

        return (object)[
            'scheduled' => time() + (60 * 60 * 24), /* 24 hours from now */
        ];
    }
}
