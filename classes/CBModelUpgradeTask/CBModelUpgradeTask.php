<?php

final class CBModelUpgradeTask {

    /* -- CBInstall interfaces -- -- -- -- -- */

    /**
     * Restart this task for every existing model on every time the site is
     * upgraded. These tasks run a slightly higher than normal priority.
     *
     * @return void
     */
    static function CBInstall_install(): void {
        $IDs = CBDB::SQLToArray(
            'SELECT LOWER(HEX(ID)) FROM CBModels'
        );

        CBTasks2::restart(
            'CBModelUpgradeTask',
            $IDs,
            90 /* priority: */
        );
    }
    /* CBInstall_install */


    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBModels',
            'CBTasks2'
        ];
    }
    /* CBInstall_requiredClassNames() */


    /* -- CBTasks2 intrfaces -- -- -- -- -- */

    /**
     * @param string $ID
     *
     * @return ?object
     */
    static function CBTasks2_run(string $ID): ?stdClass {
        $spec = CBModels::fetchSpecByID($ID);
        $upgradedSpec = CBModel::upgrade($spec);

        if ($upgradedSpec != $spec) {
            CBDB::transaction(
                function () use ($upgradedSpec) {
                    CBModels::save($upgradedSpec);
                }
            );
        }

        return null;
    }
    /* CBTasks2_run() */
}
