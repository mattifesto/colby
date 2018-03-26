<?php

final class CBModelPruneVersionsTaskTests {

    /**
     * @return ?object
     */
    static function calculateVersionsToPruneTest(): ?stdClass {
        $time       = time();
        $now        = time();
        $oneMinute  = 60;

        /* test 1 */

        $versions = [
            (object)[
                'timestamp' => $time,
                'version' => 100,
            ],
        ];

        $result = CBModelPruneVersionsTask::calculateVersionsToPrune($versions);
        $expected = [];

        if ($result != $expected) {
            return (object)[
                'failed' => true,
                'message' =>
                    "Test 1 failed:\n\n" .
                    CBConvertTests::resultAndExpectedToMessage($result, $expected),
            ];
        }

        /* test 2 */

        $versions = [
            (object)[
                'timestamp' => $time,
                'version' => 100,
            ],
            (object)[
                'timestamp' => $time - 10,
                'version' => 90,
            ],
            (object)[
                'timestamp' => $time - (60 * 60 * 24 * 100),
                'version' => 80,
            ],
        ];

        $result = CBModelPruneVersionsTask::calculateVersionsToPrune($versions);
        $expected = [];

        if ($result != $expected) {
            return (object)[
                'failed' => true,
                'message' =>
                    "Test 2 failed:\n\n" .
                    CBConvertTests::resultAndExpectedToMessage($result, $expected),
            ];
        }

        /* test 3 */

        $versions = [
            (object)[
                'timestamp' => $time,
                'version' => 100,
            ],
            (object)[
                'timestamp' => $time - 10,
                'version' => 90,
            ],
            (object)[
                'timestamp' => $time - 20,
                'version' => 85,
            ],
            (object)[
                'timestamp' => $time - (60 * 60 * 24 * 100),
                'version' => 80,
            ],
        ];

        $result = CBModelPruneVersionsTask::calculateVersionsToPrune($versions);
        $expected = [90];

        if ($result != $expected) {
            return (object)[
                'failed' => true,
                'message' =>
                    "Test 3 failed:\n\n" .
                    CBConvertTests::resultAndExpectedToMessage($result, $expected),
            ];
        }

        /* test 4 */

        $versions = [
            (object)[
                'timestamp' => $time,
                'version' => 100,
            ],
            (object)[
                'timestamp' => $time - (60 * 60 * 24 * 100),
                'version' => 80,
            ],
            (object)[
                'timestamp' => $time - (60 * 60 * 24 * 110),
                'version' => 70,
            ],
            (object)[
                'timestamp' => $time - (60 * 60 * 24 * 120),
                'version' => 60,
            ],
            (object)[
                'timestamp' => $time - (60 * 60 * 24 * 130),
                'version' => 50,
            ],
        ];

        $result = CBModelPruneVersionsTask::calculateVersionsToPrune($versions);
        $expected = [80, 70];

        if ($result != $expected) {
            return (object)[
                'failed' => true,
                'message' =>
                    "Test 4 failed:\n\n" .
                    CBConvertTests::resultAndExpectedToMessage($result, $expected),
            ];
        }

        /* test 5 */

        $versions = [
            (object)[
                'timestamp' => $time,
                'version' => 100,
            ],
            (object)[
                'timestamp' => $time - 10,
                'version' => 90,
            ],
            (object)[
                'timestamp' => $time - 20,
                'version' => 85,
            ],
            (object)[
                'timestamp' => $time - 30,
                'version' => 84,
            ],
            (object)[
                'timestamp' => $time - 40,
                'version' => 83,
            ],
            (object)[
                'timestamp' => $time - (60 * 60 * 24 * 29),
                'version' => 82,
            ],
            (object)[
                'timestamp' => $time - (60 * 60 * 24 * 30),
                'version' => 81,
            ],
            (object)[
                'timestamp' => $time - (60 * 60 * 24 * 100),
                'version' => 80,
            ],
            (object)[
                'timestamp' => $time - (60 * 60 * 24 * 110),
                'version' => 70,
            ],
            (object)[
                'timestamp' => $time - (60 * 60 * 24 * 120),
                'version' => 60,
            ],
        ];

        $result = CBModelPruneVersionsTask::calculateVersionsToPrune($versions);
        $expected = [90, 85, 84, 83, 80, 70, 60];

        if ($result != $expected) {
            return (object)[
                'failed' => true,
                'message' =>
                    "Test 5 failed:\n\n" .
                    CBConvertTests::resultAndExpectedToMessage($result, $expected),
            ];
        }

        /* test 6 */

        $versions = [
            (object)[
                'timestamp' => $now,
                'version' => 200,
            ],
            (object)[
                'timestamp' => $now - (3 * $oneMinute),
                'version' => 190,
            ],
            (object)[
                'timestamp' => $now - (6 * $oneMinute),
                'version' => 180,
            ],
            (object)[
                'timestamp' => $now - (9 * $oneMinute),
                'version' => 170,
            ],
            (object)[
                'timestamp' => $now - (12 * $oneMinute),
                'version' => 160,
            ],
            (object)[
                'timestamp' => $now - (15 * $oneMinute),
                'version' => 150,
            ],
            (object)[
                'timestamp' => $now - (18 * $oneMinute),
                'version' => 140,
            ],
            (object)[
                'timestamp' => $now - (21 * $oneMinute),
                'version' => 130,
            ],
            (object)[
                'timestamp' => $now - (24 * $oneMinute),
                'version' => 120,
            ],
            (object)[
                'timestamp' => $now - (27 * $oneMinute),
                'version' => 110,
            ],
        ];

        $result = CBModelPruneVersionsTask::calculateVersionsToPrune($versions);
        $expected = [190, 180, 170, 150, 140, 130, 110];

        if ($result != $expected) {
            return (object)[
                'failed' => true,
                'message' =>
                    "Test 6 failed:\n\n" .
                    CBConvertTests::resultAndExpectedToMessage($result, $expected),
            ];
        }

        return null;
    }

    /**
     * @return [[<class>, <test>]]
     */
    static function CBUnitTests_tests(): array {
        return [
            ['CBModelPruneVersionsTask', 'calculateVersionsToPrune'],
        ];
    }
}
