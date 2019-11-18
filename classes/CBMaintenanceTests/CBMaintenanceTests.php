<?php

final class CBMaintenanceTests {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'lock',
                'title' => 'CBMaintenance::lock()',
                'type' => 'server',
            ],
        ];
    }



    /* -- tests -- -- -- -- -- */



    /**
     * @return object
     */
    static function CBTest_lock(): stdClass {
        $holderID = CBID::generateRandomCBID();
        $title = __METHOD__ . '()';

        /* --- */

        $testTitle = 'isLocked() before locking';
        $expected = false;
        $actual = CBMaintenance::isLocked();

        if ($actual !== $expected) {
            return CBTest::resultMismatchFailure(
                $testTitle,
                $actual,
                $expected
            );
        };

        /* --- */

        $testTitle = 'Lock';
        $expected = true;

        $actual = CBMaintenance::lock(
            (object)[
                'holderID' => $holderID,
                'title' => $title,
            ]
        );

        if ($actual !== $expected) {
            return CBTest::resultMismatchFailure(
                $testTitle,
                $actual,
                $expected
            );
        };

        /* --- */

        $testTitle = 'isLocked() after locking';
        $expected = true;
        $actual = CBMaintenance::isLocked();

        if ($actual !== $expected) {
            return CBTest::resultMismatchFailure(
                $testTitle,
                $actual,
                $expected
            );
        };

        /* --- */

        $testTitle = 'Lock with new holder while already locked';
        $expected = false;

        $actual = CBMaintenance::lock(
            (object)[
                'holderID' => CBID::generateRandomCBID(),
                'title' => $title,
            ]
        );

        if ($actual !== $expected) {
            return CBTest::resultMismatchFailure(
                $testTitle,
                $actual,
                $expected
            );
        };

        /* --- */

        CBMaintenance::unlock($holderID);

        $testTitle = 'isLocked() after unlocking';
        $expected = false;
        $actual = CBMaintenance::isLocked();

        if ($actual !== $expected) {
            return CBTest::resultMismatchFailure(
                $testTitle,
                $actual,
                $expected
            );
        };

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_lock() */

}
