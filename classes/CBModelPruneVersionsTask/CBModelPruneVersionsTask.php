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

        $onedayago      = $now - $oneday;
        $sevendaysago   = $now - ($oneday * 7);
        $thirtydaysago  = $now - ($oneday * 30);
        $ninetydaysago  = $now - ($oneday * 90);

        $count = count($versions);
        $versionsToPrune = [];

        $nextVersionTimestamp = $versions[0]->timestamp;

        for ($index = 1; $index < $count; $index++) {
            if ($count - count($versionsToPrune) <= $minimumVersionCount) {
                break;
            }

            $thisVersionNumber = $versions[$index]->version;
            $thisVersionTimestamp = $versions[$index]->timestamp;
            $thisVersionAge = $nextVersionTimestamp - $thisVersionTimestamp;

            if ($thisVersionTimestamp > $onedayago) {
                $minimumAge = $tenminutes;
            } else if ($thisVersionTimestamp > $sevendaysago) {
                $minimumAge = $twohours;
            } else if ($thisVersionTimestamp > $ninetydaysago) {
                $minimumAge = $oneday;
            } else {
                $minimumAge = PHP_INT_MAX;
            }

            if ($thisVersionAge < $minimumAge) {
                $versionsToPrune[] = $thisVersionNumber;
            } else {
                $nextVersionTimestamp = $thisVersionTimestamp;
            }
        }

        return $versionsToPrune;
    }
}
