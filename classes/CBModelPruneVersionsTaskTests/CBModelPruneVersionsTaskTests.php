<?php

final class CBModelPruneVersionsTaskTests {

    /**
     * @return object
     */
    static function CBTest_assignActions(): stdClass {
        $now = time();
        $one_second = 1;
        $one_minute = 60 * $one_second;
        $one_hour = 60 * $one_minute;
        $one_day = 24 * $one_hour;
        $one_week = 7 * $one_day;

        $tests = [

            /* 0 to 1 hour */

            [
                'deathspan: 5 seconds, lifespan: less than 10 seconds', /* name */
                $one_second * 5, /* deathspan */
                $one_second * 5, /* lifespan */
                'prune', /* action */
            ],
            [
                'deathspan: 5 seconds, lifespan: greater than 10 seconds', /* name */
                $one_second * 5, /* deathspan */
                $one_second * 15, /* lifespan */
                'keep', /* action */
            ],

            [
                'deathspan: 55 minutes, lifespan: less than 10 seconds', /* name */
                $one_minute * 55, /* deathspan */
                $one_second * 5, /* lifespan */
                'prune', /* action */
            ],
            [
                'deathspan: 55 minutes, lifespan: greater than 10 seconds', /* name */
                $one_minute * 55, /* deathspan */
                $one_second * 15, /* lifespan */
                'keep', /* action */
            ],

            /* 1 hour to 1 day */

            [
                'deathspan: 65 minutes, lifespan: less than 1 minute', /* name */
                $one_minute * 65, /* deathspan */
                $one_second * 55, /* lifespan */
                'prune', /* action */
            ],
            [
                'deathspan: 65 minutes, lifespan: greater than 1 minute', /* name */
                $one_minute * 65, /* deathspan */
                $one_second * 65, /* lifespan */
                'keep', /* action */
            ],

            [
                'deathspan: 23 hours, lifespan: less than 1 minute', /* name */
                $one_hour * 23, /* deathspan */
                $one_second * 55, /* lifespan */
                'prune', /* action */
            ],
            [
                'deathspan: 23 hours, lifespan: greater than 1 minute', /* name */
                $one_hour * 23, /* deathspan */
                $one_second * 65, /* lifespan */
                'keep', /* action */
            ],

            /* 1 day to 1 week */

            [
                'deathspan: 25 hours, lifespan: less than 10 minutes', /* name */
                $one_hour * 25, /* deathspan */
                $one_minute * 9, /* lifespan */
                'prune', /* action */
            ],
            [
                'deathspan: 25 hours, lifespan: greater than 10 minutes', /* name */
                $one_hour * 25, /* deathspan */
                $one_minute * 11, /* lifespan */
                'keep', /* action */
            ],

            [
                'deathspan: 6 days, lifespan: less than 10 minutes', /* name */
                $one_day * 6, /* deathspan */
                $one_minute * 9, /* lifespan */
                'prune', /* action */
            ],
            [
                'deathspan: 6 days, lifespan: greater than 10 minutes', /* name */
                $one_day * 6, /* deathspan */
                $one_minute * 11, /* lifespan */
                'keep', /* action */
            ],

            /* 1 week to 30 days */

            [
                'deathspan: 8 days, lifespan: less than 1 hour', /* name */
                $one_day * 8, /* deathspan */
                $one_minute * 55, /* lifespan */
                'prune', /* action */
            ],
            [
                'deathspan: 8 days, lifespan: greater than 1 hour', /* name */
                $one_day * 8, /* deathspan */
                $one_minute * 65, /* lifespan */
                'keep', /* action */
            ],

            [
                'deathspan: 29 days, lifespan: less than 1 hour', /* name */
                $one_day * 29, /* deathspan */
                $one_minute * 55, /* lifespan */
                'prune', /* action */
            ],
            [
                'deathspan: 20 days, lifespan: greater than 1 hour', /* name */
                $one_day * 29, /* deathspan */
                $one_minute * 65, /* lifespan */
                'keep', /* action */
            ],

            /* 30 days to 90 days */

            [
                'deathspan: 31 days, lifespan: less than 10 hours', /* name */
                $one_day * 31, /* deathspan */
                $one_hour * 9, /* lifespan */
                'prune', /* action */
            ],
            [
                'deathspan: 31 days, lifespan: greater than 10 hours', /* name */
                $one_day * 31, /* deathspan */
                $one_hour * 11, /* lifespan */
                'keep', /* action */
            ],

            [
                'deathspan: 89 days, lifespan: less than 10 hours', /* name */
                $one_day * 89, /* deathspan */
                $one_hour * 9, /* lifespan */
                'prune', /* action */
            ],
            [
                'deathspan: 89 days, lifespan: greater than 10 hours', /* name */
                $one_day * 89, /* deathspan */
                $one_hour * 11, /* lifespan */
                'keep', /* action */
            ],

            /* greater than 90 days */

            [
                'deathspan: 91 days, lifespan: greater than 10 hours', /* name */
                $one_day * 91, /* deathspan */
                $one_hour * 11, /* lifespan */
                'prune', /* action */
            ],
            [
                'deathspan: 365 days, lifespan: 365 days', /* name */
                $one_day * 365, /* deathspan */
                $one_day * 365, /* lifespan */
                'prune', /* action */
            ],
            [
                'deathspan: 100,000 days, lifespan: 100,000 days', /* name */
                $one_day * 100000, /* deathspan */
                $one_day * 100000, /* lifespan */
                'prune', /* action */
            ],
        ];

        foreach ($tests as $test) {
            $name = $test[0];
            $deathspan = $test[1];
            $lifespan = $test[2];
            $action = $test[3];

            $replaced = $now - $deathspan;
            $timestamp = $replaced - $lifespan;

            $versions = [
                (object)[
                    'version' => 100,
                    'timestamp' => $now,
                ],
                (object)[
                    'version' => 50,
                    'timestamp' => $timestamp,
                    'replaced' => $replaced,
                ],
            ];
            $expected = [
                (object)[
                    'version' => 100,
                    'timestamp' => $now,
                    'action' => 'keep',
                ],
                (object)[
                    'version' => 50,
                    'timestamp' => $timestamp,
                    'replaced' => $replaced,
                    'action' => $action,
                ],
            ];

            CBModelPruneVersionsTask::assignActions($versions);

            if ($versions != $expected) {
                return CBTest::resultMismatchFailure($name, $versions, $expected);
            }
        }

        return (object)[
            'succeeded' => true,
        ];
    }

    /**
     * @return object
     */
    static function CBTest_runTask(): stdClass {
        $ID = '8ab4d33187e3016af99e9c2e97ecd1284d219917';
        $IDAsSQL = CBHex160::toSQL($ID);

        $versionNumber = 0;
        $versionHistory = [
            [$versionNumber += 1, 30000001, 'prune'],

            [$versionNumber += 1, 30000000, 'keep'], // filler
            [$versionNumber += 1,  9011000, 'keep'],
            [$versionNumber += 1,  9000000, 'keep'], // filler
            [$versionNumber += 1,  8009000, 'prune'],
            [$versionNumber += 1,  8000000, 'keep'], // filler
            [$versionNumber += 1,  3011000, 'keep'],
            [$versionNumber += 1,  3000000, 'keep'], // filler
            [$versionNumber += 1,  2009000, 'prune'],

            [$versionNumber += 1,  2000000, 'keep'], // filler
            [$versionNumber += 1,   901100, 'keep'],
            [$versionNumber += 1,   900000, 'keep'], // filler
            [$versionNumber += 1,   800900, 'prune'],
            [$versionNumber += 1,   800000, 'keep'], // filler
            [$versionNumber += 1,   301100, 'keep'],
            [$versionNumber += 1,   300000, 'keep'], // filler
            [$versionNumber += 1,   200900, 'prune'],

            [$versionNumber += 1,   200000, 'keep'], // filler
            [$versionNumber += 1,    90110, 'keep'],
            [$versionNumber += 1,    90000, 'keep'], // filler
            [$versionNumber += 1,    80090, 'prune'],
            [$versionNumber += 1,    80000, 'keep'], // filler
            [$versionNumber += 1,     1510, 'keep'],
            [$versionNumber += 1,     1400, 'keep'], // filler
            [$versionNumber += 1,     1190, 'prune'],

            [$versionNumber += 1,     1100, 'keep'], // filler
            [$versionNumber += 1,      915, 'keep'],
            [$versionNumber += 1,      900, 'keep'], // filler
            [$versionNumber += 1,      805, 'prune'],
            [$versionNumber += 1,      800, 'keep'], // filler
            [$versionNumber += 1,      215, 'keep'],
            [$versionNumber += 1,      200, 'keep'], // filler
            [$versionNumber += 1,        5, 'prune'],

            [$versionNumber += 1,        0, 'keep'], /* will be current version */
        ];

        $count = count($versionHistory);

        CBModels::deleteByID($ID);

        $spec = (object)[
            'className' => 'CBModelPruneVersionsTaskTests_class',
            'ID' => $ID,
        ];

        for ($index = 0; $index < $count; $index += 1) {
            CBDB::transaction(function () use ($spec) {
                CBModels::save($spec);
            });
        }

        $now = time();

        for ($index = 0; $index < $count; $index += 1) {
            $versionNumber = $versionHistory[$index][0];
            $timestamp = $now - $versionHistory[$index][1];
            $SQL = <<<EOT

                UPDATE  CBModelVersions
                SET     timestamp = {$timestamp}
                WHERE   ID = {$IDAsSQL} AND
                        version = {$versionNumber}

EOT;

            Colby::query($SQL);
        }

        CBTasks2::runSpecificTask('CBModelPruneVersionsTask', $ID);

        $SQL = <<<EOT

            SELECT      version
            FROM        CBModelVersions
            WHERE       ID = {$IDAsSQL}
            ORDER BY    version

EOT;

        $actual = CBDB::SQLToArray($SQL);
        $expected = [];

        for ($index = 0; $index < $count; $index += 1) {
            $values = $versionHistory[$index];
            $versionNumber = $values[0];

            if ($values[2] === 'keep') {
                array_push($expected, strval($versionNumber));
            }
        }

        if ($actual != $expected) {
            return CBTest::resultMismatchFailure("Final", $actual, $expected);
        }

        CBModels::deleteByID($ID);

        return (object)[
            'succeeded' => true,
        ];
    }

    /**
     * @return [[<class>, <test>]]
     */
    static function CBUnitTests_tests(): array {
        return [
            ['CBModelPruneVersionsTask', 'assignActions'],
            ['CBModelPruneVersionsTask', 'runTask'],
        ];
    }
}

final class CBModelPruneVersionsTaskTests_class {

    /**
     * @param object $spec
     *
     * @return object
     */
    static function CBModel_build(stdClass $spec): ?stdClass {
        return (object)[];
    }
}
