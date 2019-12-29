<?php

final class CBTasks2_RemoveExpiredCompletedTasks_Task {

    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * When we do an install or update schedule this task to run. This is also
     * the time at which the priority of the task can be changed if needed.
     *
     * @return void
     */
    static function CBInstall_install(): void {

        /**
         * This will make the task ready the first time it is called and not
         * have any effect after that. The task will already be scheduled to
         * run.
         */
        CBTasks2::restart(
            __CLASS__,
            CBTasks2_RemoveExpiredCompletedTasks_Task::taskCBID()
        );
    }
    /* CBInstall_install() */



    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBLog',
            'CBTasks2',
        ];
    }



    /* -- CBTasks2 interfaces -- -- -- -- -- */



    /**
     * @param CBID $CBID
     *
     * @return object
     */
    static function CBTasks2_run(
        string $CBID
    ): stdClass {
        $expectedTaskCBID = (
            CBTasks2_RemoveExpiredCompletedTasks_Task::taskCBID()
        );

        if ($CBID !== $expectedTaskCBID) {
            throw new CBExceptionWithValue(
                'Invalid task CBID',
                $CBID,
                'c78eb6c22159ef46ee79c1c7348f2b3fb37332c3'
            );
        }


        $timestamp30DaysAgo = (
            time() -
            (
                60 /* seconds */ *
                60 /* minutes */ *
                24 /* hours */ *
                30 /* days */
            )
        );

        $completeStateCode = 3;

        $SQL = <<<EOT

            DELETE FROM     CBTasks2

            WHERE           state = {$completeStateCode}
                AND         timestamp < {$timestamp30DaysAgo}

        EOT;

        Colby::query($SQL);

        $countOfRemovedTasks = Colby::mysqli()->affected_rows;


        /* log */

        CBLog::log(
            (object)[
                'message' => <<<EOT

                    {$countOfRemovedTasks} expired completed tasks were removed
                    by CBTasks2_RemoveExpiredCompletedTasks_Task.

                EOT,
                'severity' => 6,
                'sourceClassName' => __CLASS__,
                'sourceID' => '60a57739946371cb4736a55033b2da12a225dd2e',
            ]
        );


        /* done */

        return (object)[
            'scheduled' => (
                time() +
                (
                    60 /* seconds */ *
                    60 /* minutes */ *
                    24 /* hours */
                )
            ),
        ];
    }
    /* CBTasks2_run() */



    /* -- functions -- -- -- -- -- */



    /**
     * @return CBID
     */
    static function taskCBID(): string {
        return '9717fca8cffbb2a4d5f1a3c58fb4dbb048ca9b49';
    }

}
