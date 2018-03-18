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

        $tenminutes = 60 * 10;
        $ninetydays = 60 * 60 * 24 * 90;

        $count = count($versions);
        $versionsToPrune = [];

        $index = 0;
        $firstTimestamp = $versions[0]->timestamp;

        /**
         * First delete recent versions that were saved in close proximity to
         * the most recent version. These versions are unlikely to contain
         * information that is historically significant.
         */

        for ($index = 1; $index < $count; $index++) {
            if ($count - count($versionsToPrune) <= $minimumVersionCount) {
                break;
            }

            $timestamp = $versions[$index]->timestamp;

            if ($timestamp > $firstTimestamp - $tenminutes) {
                $versionsToPrune[] = $versions[$index]->version;
            } else {
                break;
            }
        }

        /**
         * Delete the oldest versions that are old enough to have expired and
         * are no longer likely to contain information that still remains
         * significant.
         */

        for ($index = $count - 1; $index > 0; $index--) {
            if ($count - count($versionsToPrune) <= $minimumVersionCount) {
                break;
            }

            $timestamp = $versions[$index]->timestamp;

            if ($timestamp < $firstTimestamp - $ninetydays) {
                $versionsToPrune[] = $versions[$index]->version;
            } else {
                break;
            }
        }

        return $versionsToPrune;
    }
}
