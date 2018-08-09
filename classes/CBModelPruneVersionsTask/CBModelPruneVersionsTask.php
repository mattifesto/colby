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

    const standardTaskPriority = 200;

    /**
     * @return void
     */
    static function CBInstall_install(): void {
        $IDs = CBDB::SQLToArray('SELECT LOWER(HEX(ID)) FROM CBModels');
        $priority = CBModelPruneVersionsTask::standardTaskPriority;

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
     * @param ID $ID
     *
     * @return ?object
     */
    static function CBTasks2_run(string $ID): ?stdClass {
        $IDAsSQL = CBHex160::toSQL($ID);
        $SQL = <<<EOT

            SELECT      version,
                        timestamp,
                        replaced
            FROM        CBModelVersions
            WHERE       ID = {$IDAsSQL}
            ORDER BY    version DESC

EOT;

        $versions = CBDB::SQLToObjects($SQL);

        /**
         * If the model has been deleted or only has one version there's no need
         * to take any further action or reschedule this task.
         */
        if (count($versions) < 2) {
            return null;
        }

        CBModelPruneVersionsTask::assignActions($versions);

        $versionsToPrune = [];

        foreach ($versions as $version) {
            if ($version->action === 'prune') {
                array_push($versionsToPrune, $version->version);
            } else if ($version->action === 'update') {
                $replacedAsSQL = CBConvert::valueAsInt($version->replaced);
                $versionAsSQL = CBConvert::valueAsInt($version->version);
                $SQL = <<<EOT

                    UPDATE  CBModelVersions
                    SET     replaced = {$replacedAsSQL}
                    WHERE   ID = {$IDAsSQL} AND
                            version = {$versionAsSQL}

EOT;

                Colby::query($SQL);
            }
        }

        if (!empty($versionsToPrune)) {
            $versionsToPruneAsSQL = implode(',', $versionsToPrune);
            $SQL = <<<EOT

                DELETE FROM CBModelVersions
                WHERE       ID = {$IDAsSQL} AND
                            version in ($versionsToPruneAsSQL)

EOT;

            Colby::query($SQL);
        }

        /**
         * Reschedule this task to run again in 5 days to further prune the
         * model versions.
         */
        return (object)[
            'scheduled' => time() + (60 * 60 * 24 * 5),
            'priority' => CBModelPruneVersionsTask::standardTaskPriority,
        ];
    }

    /**
     * This function takes an array of version records and:
     *
     *      Calculates the repaclace column value if it doesn't have one yet.
     *
     *      Determines the next appropriate action for the row: 'keep',
     *      'updated', or 'prune'.
     *
     * @param [object] $versions
     *
     * @return void
     *
     *      This function is a function which modifies the objects inside the
     *      $versions array. It's technically possible to clone this whole array
     *      including its objects but in all use cases there's no desire for
     *      that. So instead this function just modifies the $versions array in
     *      place and returns void.
     */
    static function assignActions(array $versions): void {
        if (empty($versions)) {
            return;
        }

        $now = time();

        /**
         * After creating an implementation of this function that used time
         * spans in round minutes, hours, days, weeks, months, and years; it
         * became apparent that simplifying the time spans into more visibly
         * obvious numbers would be helpful to developers and maintainers.
         *
         * The actual amounts of time are less important than being able to see
         * them at a glance without calculation.
         *
         *         100    1.6666 minutes
         *       1,000   16.6666 minutes
         *      10,000    2.7777 hours    166.6666 minutes
         *     100,000    1.1574 days      27.7777 hours
         *   1,000,000   11.5740 days
         *  10,000,000    3.8580 months   115.7407 days
         */

        $count = count($versions);
        $nextVersion = $versions[0];
        $nextVersion->action = 'keep';

        /**
         * Loop through the version records starting at index 1, which holds the
         * first record that could potentially be pruned.
         */

        for ($index = 1; $index < $count; $index++) {
            $version = $versions[$index];

            if (empty($version->replaced)) {
                $version->replaced = $nextVersion->timestamp;
                $version->action = 'update';
            }

            // lifespan = the duration the version was the current version
            $lifespan = $version->replaced - $version->timestamp;

            if ($lifespan < 0) {
                throw new Exception('The lifespan of a version is negative.');
            }

            // deathspan = the time since the version was replaced
            $deathspan = $now - $version->replaced;

            if ($deathspan < 0) {
                throw new Exception('The deathspan of a version is negative.');
            }

            if ($deathspan < 1000) { /* 0 to 16.6 minutes: 10 seconds */
                if ($lifespan < 10) {
                    $version->action = 'prune';
                }
            } else if ($deathspan < 100000) { /* 16.6 minutes to 1.1 days: 1.6 minutes */
                if ($lifespan < 100) {
                    $version->action = 'prune';
                }
            } else if ($deathspan < 1000000) { /* 1.1 days to 11.5 days: 16.6 minutes */
                if ($lifespan < 1000) {
                    $version->action = 'prune';
                }
            } else if ($deathspan < 10000000) { /* 11.5 days to 3.8 months: 2.7 hours */
                if ($lifespan < 10000) {
                    $version->action = 'prune';
                }
            } else { /* greater than 3.8 months: prune */
                $version->action = 'prune';
            }

            if (empty($version->action)) {
                $version->action = 'keep';
            }

            $nextVersion = $version;
        }
    }
}
