<?php

final class
CBModelUpgradeTask {

    /* -- CBInstall interfaces -- -- -- -- -- */



    /**
     * Restart this task for every existing model on every time the site is
     * upgraded. These tasks run a slightly higher than normal priority.
     *
     * @return void
     */
    static function
    CBInstall_install(
    ): void {
        $IDs = CBDB::SQLToArray(<<<EOT

            SELECT
            LOWER(HEX(ID))
            FROM
            CBModels

        EOT);

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
    static function
    CBInstall_requiredClassNames(
    ): array {
        return [
            'CBModels',
            'CBTasks2'
        ];
    }
    /* CBInstall_requiredClassNames() */



    /* -- CBTasks2 interfaces -- -- -- -- -- */



    /**
     * @param string $modelCBID
     *
     * @return ?object
     */
    static function
    CBTasks2_run(
        string $modelCBID
    ): ?stdClass {
        $originalSpec = CBModels::fetchSpecByIDNullable(
            $modelCBID
        );

        if (
            $originalSpec === null
        ) {
            CBTasks2::remove(
                __CLASS__,
                $modelCBID
            );

            return null;
        }

        $upgradedSpec = CBModel::upgrade(
            $originalSpec
        );

        if (
            $upgradedSpec != $originalSpec
        ) {
            CBDB::transaction(
                function () use (
                    $upgradedSpec
                ) {
                    CBModels::save(
                        $upgradedSpec
                    );
                }
            );
        }

        return null;
    }
    /* CBTasks2_run() */

}
