<?php

final class CBModelPruneVersionsTaskTests {

    /**
     * @return object
     */
    static function CBTest_assignActions(): stdClass {
        $now = time();

        $tests = [

            /* 0 to 1,000 */

            [
                'deathspan: greater than 0, lifespan: less than 10', /* name */
                5, /* deathspan */
                5, /* lifespan */
                'prune', /* action */
            ],
            [
                'deathspan: greater than 0, lifespan: greater than 10', /* name */
                 5, /* deathspan */
                15, /* lifespan */
                'keep', /* action */
            ],

            [
                'deathspan: less than 1,000, lifespan: less than 10', /* name */
                900, /* deathspan */
                  5, /* lifespan */
                'prune', /* action */
            ],
            [
                'deathspan: less than 1,000, lifespan: greater than 10', /* name */
                900, /* deathspan */
                 15, /* lifespan */
                'keep', /* action */
            ],

            /* 1,000 to 100,000 */

            [
                'deathspan: greater than 1,000, lifespan: less than 100', /* name */
                1100, /* deathspan */
                  90, /* lifespan */
                'prune', /* action */
            ],
            [
                'deathspan: greater than 1,000, lifespan: greater than 100', /* name */
                1100, /* deathspan */
                 110, /* lifespan */
                'keep', /* action */
            ],

            [
                'deathspan: less than 100,000, lifespan: less than 100', /* name */
                99000, /* deathspan */
                90, /* lifespan */
                'prune', /* action */
            ],
            [
                'deathspan: less than 100,000, lifespan: greater than 100', /* name */
                99000, /* deathspan */
                  110, /* lifespan */
                'keep', /* action */
            ],

            /* 100,000 to 1,000,000 */

            [
                'deathspan: greater than 100,000, lifespan: less than 1,000', /* name */
                110000, /* deathspan */
                   900, /* lifespan */
                'prune', /* action */
            ],
            [
                'deathspan: greater than 100,000, lifespan: greater than 1,000', /* name */
                110000, /* deathspan */
                  1100, /* lifespan */
                'keep', /* action */
            ],

            [
                'deathspan: less than 1,000,000, lifespan: less than 1,000', /* name */
                990000, /* deathspan */
                   900, /* lifespan */
                'prune', /* action */
            ],
            [
                'deathspan: less than 1,000,000, lifespan: greater than 1,000', /* name */
                990000, /* deathspan */
                  1100, /* lifespan */
                'keep', /* action */
            ],

            /* 1,000,000 to 10,000,000 */

            [
                'deathspan: greater than 1,000,000, lifespan: less than 10,000', /* name */
                1100000, /* deathspan */
                   9000, /* lifespan */
                'prune', /* action */
            ],
            [
                'deathspan: greater than 1,000,000, lifespan: greater than 10,000', /* name */
                1100000, /* deathspan */
                  11000, /* lifespan */
                'keep', /* action */
            ],

            [
                'deathspan: less than 10,000,000, lifespan: less than 10,000', /* name */
                9900000, /* deathspan */
                   9900, /* lifespan */
                'prune', /* action */
            ],
            [
                'deathspan: less than 10,000,000, lifespan: greater than 10,000', /* name */
                9900000, /* deathspan */
                  11000, /* lifespan */
                'keep', /* action */
            ],

            /* greater than 10,000,000 */

            [
                'deathspan: 11,000,000, lifespan: 1', /* name */
                11000000, /* deathspan */
                1, /* lifespan */
                'prune', /* action */
            ],
            [
                'deathspan: 11,000,000, lifespan: 10,000,000', /* name */
                11000000, /* deathspan */
                10000000, /* lifespan */
                'prune', /* action */
            ],
            [
                'deathspan: 100,000,000,000, lifespan: 10,000,000', /* name */
                100000000000, /* deathspan */
                10000000, /* lifespan */
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
