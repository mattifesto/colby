<?php

/**
 * This task is responsible for pruning old and unneeded versions from the
 * CBModelVersions table.  The task is restarted for the model ID each time a
 * model is saved.
 *
 * The heuristics for pruning versions is relatively simple, but complex enough
 * that doing it with a set of SQL queries would lead to very complex and
 * difficult to understand queries.
 */
final class CBModelPruneVersionsTask {

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $IDs = CBDB::SQLToArray('SELECT LOWER(HEX(ID)) FROM CBModels');
        $priority = 200;

        CBTasks2::restart('CBModelPruneVersionsTask', $IDs, $priority);
    }

    /**
     * @return [string]
     */
    static function CBInstall_requiredClassNames(): array {
        return [
            'CBTasks2',
        ];
    }

    /**
     * @param hex160 $ID
     *
     * @return void
     */
    static function CBTasks2_run(string $ID): void {
        $IDAsSQL = CBHex160::toSQL($ID);
        $SQL = <<<EOT

            SELECT      version, timestamp
            FROM        CBModelVersions
            WHERE       ID = {$IDAsSQL}
            ORDER BY    version DESC

EOT;

        $versions = CBDB::SQLToObjects($SQL);
        $versionsToPrune = CBModelPruneVersionsTask::calculateVersionsToPrune($versions);

        if (!empty($versionsToPrune)) {
            $versionsToPruneAsSQL = implode(',', $versionsToPrune);
            $SQL = <<<EOT

                DELETE FROM CBModelVersions
                WHERE       ID = {$IDAsSQL} AND
                            version in ($versionsToPruneAsSQL)

EOT;

            Colby::query($SQL);
        }
    }

    /**
     * This is a separate function so that it can be tested without having to
     * purposely insert records into the CBModelVersions table.
     *
     * @param [object] $versions
     *
     *      The array of versions with the most recent version at index 0 and
     *      each additional index holding an older version that the previous.
     *
     *      The version at index 0 will never be pruned.
     *
     *      {
     *          timestamp: int
     *          version: int
     *      }
     *
     * @return [int]
     */
    static function calculateVersionsToPrune(array $versions): array {
        if (empty($versions)) {
            return [];
        }

        /**
         * Always keep a minimum number of versions regardless of age so that if
         * the most recently saved version is bad for some reason there will be
         * versions available to revert to.
         */

        $minimumVersionCount = 3;

        $now        = time();
        $tenminutes = 60 * 10;
        $twohours   = 60 * 60 * 2;
        $oneday     = 60 * 60 * 24;
        $sevendays  = $oneday * 7;
        $ninetydays = $oneday * 90;

        $count = count($versions);
        $versionsToPrune = [];

        $nextVersionCreated = $versions[0]->timestamp;

        /**
         * Loop through the versions starting at index 1, which holds the first
         * version that could potentially be pruned.
         */

        for ($index = 1; $index < $count; $index++) {
            if ($count - count($versionsToPrune) <= $minimumVersionCount) {
                break;
            }

            $thisVersionNumber = $versions[$index]->version;
            $thisVersionCreated = $versions[$index]->timestamp;
            $thisVersionDuration = $nextVersionCreated - $thisVersionCreated;
            $thisVersionAge = $now - $thisVersionCreated;

            if ($thisVersionAge < $oneday) {
                $minimumDurationRequiredToBeKept = $tenminutes;
            } else if ($thisVersionAge < $sevendays) {
                $minimumDurationRequiredToBeKept = $twohours;
            } else if ($thisVersionAge < $ninetydays) {
                $minimumDurationRequiredToBeKept = $oneday;
            } else {
                $minimumDurationRequiredToBeKept = PHP_INT_MAX;
            }

            if ($thisVersionDuration < $minimumDurationRequiredToBeKept) {
                $versionsToPrune[] = $thisVersionNumber;
            } else {
                $nextVersionCreated = $thisVersionCreated;
            }
        }

        return $versionsToPrune;
    }
}
