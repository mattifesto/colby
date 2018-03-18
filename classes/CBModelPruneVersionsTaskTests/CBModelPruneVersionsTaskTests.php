<?php

final class CBModelPruneVersionsTaskTests {

    /**
     * @return ?object
     */
    static function calculateVersionsToPruneTest(): ?stdClass {
        $time = time();

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
        $expected = [50, 60];

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
        $expected = [90, 85, 84, 83, 60, 70, 80];

        if ($result != $expected) {
            return (object)[
                'failed' => true,
                'message' =>
                    "Test 5 failed:\n\n" .
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
