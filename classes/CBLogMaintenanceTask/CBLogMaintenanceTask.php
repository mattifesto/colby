<?php

final class CBLogMaintenanceTask {

    /**
     * When we do an install or update schedule this task to run. This is also
     * the time at which the priority of the task can be changed if needed.
     *
     * @return void
     */
    static function CBInstall_install(): void {

        /**
         * This task originally used the zero ID. Singleton tasks must now use
         * a unique ID. This line can be remove once it has run on all sites.
         */
        CBTasks2::remove(__CLASS__, '0000000000000000000000000000000000000000');

        /**
         * This will make the task ready the first time it is called and not
         * have any effect after that. The task will already be scheduled to
         * run.
         */
        CBTasks2::updateTask(__CLASS__, CBLogMaintenanceTask::ID());
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
     * @return void
     */
    static function CBTasks2_Execute(string $ID): void {
        if ($ID !== CBLogMaintenanceTask::ID()) {
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

    /**
     * @return hex160
     */
    static function ID(): string {
        return '9f70a32600e39cabe7e4f7310d478b5159623929';
    }
}
