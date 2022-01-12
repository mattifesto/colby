<?php

final class
CB_Tests_Timestamp {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function
    CBTest_getTests(
    ): array {
        return [
            (object)[
                'name' => 'reserveAndRegister',
                'type' => 'server',
            ],
            (object)[
                'name' => 'saveModel',
                'type' => 'server',
            ],
        ];
    }
    /* CBTest_getTests() */



    /* -- tests  -- -- -- -- -- */



    /**
     * @return object
     */
    static function
    reserveAndRegister(
    ): stdClass {
        $rootModelCBID = '266c71f1662d5e4860dfc11f5e8c0888768397d8';

        $firstCBTimestamp = CB_Timestamp::from(
            1641483605,
            740031000073034
        );

        /* test: restore initial state 1 */

        CB_Timestamp::deleteByRootModelCBID(
            $rootModelCBID
        );

        $reservedCBTimestamps = (
            CB_Timestamp::fetchReservedCBTimestampsByRootModelCBID(
                $rootModelCBID
            )
        );

        $expectedResult = 0;

        $actualResult = count(
            $reservedCBTimestamps
        );

        if (
            $actualResult !== $expectedResult
        ) {
            return CBTest::resultMismatchFailure(
                'restore initial state 1 reserved',
                $actualResult,
                $expectedResult
            );
        }

        $registeredCBTimestamps = (
            CB_Timestamp::fetchRegisteredCBTimestampsByRootModelCBID(
                $rootModelCBID
            )
        );

        $expectedResult = 0;

        $actualResult = count(
            $registeredCBTimestamps
        );

        if (
            $actualResult !== $expectedResult
        ) {
            return CBTest::resultMismatchFailure(
                'restore initial state 1 registered',
                $actualResult,
                $expectedResult
            );
        }


        /* test: reserve with no conflict */

        $expectedResult = true;

        $actualResult = CB_Timestamp::reserve(
            $firstCBTimestamp,
            $rootModelCBID
        );

        if (
            $actualResult !== $expectedResult
        ) {
            return CBTest::resultMismatchFailure(
                'reserve with no conflict',
                $actualResult,
                $expectedResult
            );
        }


        /* test: reserve with no conflict verify */

        $reservedCBTimestamps = (
            CB_Timestamp::fetchReservedCBTimestampsByRootModelCBID(
                $rootModelCBID
            )
        );

        $expectedResult = 1;

        $actualResult = count(
            $reservedCBTimestamps
        );

        if (
            $actualResult !== $expectedResult
        ) {
            return CBTest::resultMismatchFailure(
                'reserve with no conflict verify',
                $actualResult,
                $expectedResult
            );
        }


        /* test: reserve with conflict */

        $expectedResult = false;

        $actualResult = CB_Timestamp::reserve(
            $firstCBTimestamp,
            $rootModelCBID
        );

        if (
            $actualResult !== $expectedResult
        ) {
            return CBTest::resultMismatchFailure(
                'reserve with conflict',
                $actualResult,
                $expectedResult
            );
        }


        /* test: reserve near (return value should be different) */

        $secondCBTimestamp = CB_Timestamp::reserveNear(
            $firstCBTimestamp,
            $rootModelCBID
        );

        $expectedResult = true;

        $actualResult = (
            $secondCBTimestamp !== $firstCBTimestamp
        );

        if (
            $actualResult !== $expectedResult
        ) {
            return CBTest::resultMismatchFailure(
                'reserve near (return value should be different)',
                $actualResult,
                $expectedResult
            );
        }


        /* test: reserve near verify */

        $reservedCBTimestamps = (
            CB_Timestamp::fetchReservedCBTimestampsByRootModelCBID(
                $rootModelCBID
            )
        );

        $expectedResult = 2;

        $actualResult = count(
            $reservedCBTimestamps
        );

        if (
            $actualResult !== $expectedResult
        ) {
            return CBTest::resultMismatchFailure(
                'reserve near verify',
                $actualResult,
                $expectedResult
            );
        }


        /* test: register first cbtimestamp */

        $expectedResult = true;

        $actualResult = CB_Timestamp::register(
            $firstCBTimestamp,
            $rootModelCBID
        );

        if (
            $actualResult != $expectedResult
        ) {
            return CBTest::resultMismatchFailure(
                'register first cbtimestamp',
                $actualResult,
                $expectedResult
            );
        }


        /* test: register second cbtimestamp */

        $expectedResult = true;

        $actualResult = CB_Timestamp::register(
            $secondCBTimestamp,
            $rootModelCBID
        );

        if (
            $actualResult != $expectedResult
        ) {
            return CBTest::resultMismatchFailure(
                'register second cbtimestamp',
                $actualResult,
                $expectedResult
            );
        }


        /* test: register verification 1 */

        $reservedCBTimestamps = (
            CB_Timestamp::fetchReservedCBTimestampsByRootModelCBID(
                $rootModelCBID
            )
        );

        $expectedResult = 0;

        $actualResult = count(
            $reservedCBTimestamps
        );

        if (
            $actualResult !== $expectedResult
        ) {
            return CBTest::resultMismatchFailure(
                'register verification 1',
                $actualResult,
                $expectedResult
            );
        }


        /* test: register verification 2 */

        $registeredCBTimestamps = (
            CB_Timestamp::fetchRegisteredCBTimestampsByRootModelCBID(
                $rootModelCBID
            )
        );

        $expectedResult = 2;

        $actualResult = count(
            $registeredCBTimestamps
        );

        if (
            $actualResult !== $expectedResult
        ) {
            return CBTest::resultMismatchFailure(
                'register verification 2',
                $actualResult,
                $expectedResult
            );
        }


        /* test: restore initial state 2 */

        CB_Timestamp::deleteByRootModelCBID(
            $rootModelCBID
        );

        $reservedCBTimestamps = (
            CB_Timestamp::fetchReservedCBTimestampsByRootModelCBID(
                $rootModelCBID
            )
        );

        $expectedResult = 0;

        $actualResult = count(
            $reservedCBTimestamps
        );

        if (
            $actualResult !== $expectedResult
        ) {
            return CBTest::resultMismatchFailure(
                'restore initial state 2 reserved',
                $actualResult,
                $expectedResult
            );
        }

        $registeredCBTimestamps = (
            CB_Timestamp::fetchRegisteredCBTimestampsByRootModelCBID(
                $rootModelCBID
            )
        );

        $expectedResult = 0;

        $actualResult = count(
            $registeredCBTimestamps
        );

        if (
            $actualResult !== $expectedResult
        ) {
            return CBTest::resultMismatchFailure(
                'restore initial state 2 registered',
                $actualResult,
                $expectedResult
            );
        }


        /* done */

        return (object)[
            'succeeded' => true,
        ];
    }
    /* register() */



    /**
     * @return object
     */
    static function
    saveModel(
    ): stdClass {
        $rootModelCBID = '74aafe433e2fc22a738411c7695a438b5d9d7532';

        CBDB::transaction(
            function () use (
                $rootModelCBID
            ) {
                CBModels::deleteByID(
                    $rootModelCBID
                );
            }
        );

        CB_Timestamp::deleteByRootModelCBID(
            $rootModelCBID
        );

        $firstCBTimestamp = CB_Timestamp::from(
            1641483602,
            940041000052602
        );


        /* test: use cbtimestamp without reserving */

        $spec = CBModel::createSpec(
            'CB_Tests_Timestamp_TestModel',
            $rootModelCBID
        );

        CB_Tests_Timestamp_TestModel::setCBTimestamp(
            $spec,
            $firstCBTimestamp
        );

        $expectedSourceCBID = 'd280cbd05345eaefcd5705fc981d5e603165d596';
        $actualSourceCBID = 'no exception thrown';

        try {
            CBDB::transaction(
                function () use (
                    $spec
                ) {
                    CBModels::save(
                        $spec
                    );
                }
            );
        } catch (
            Throwable $throwable
        ) {
            $actualSourceCBID = CBException::throwableToSourceCBID(
                $throwable
            );
        }

        if (
            $actualSourceCBID !== $expectedSourceCBID
        ) {
            return CBTest::resultMismatchFailure(
                'use cbtimestamp without reserving',
                $actualSourceCBID,
                $expectedSourceCBID
            );
        }


        /* test: use cbtimestamp correctly */

        CB_Timestamp::reserve(
            $firstCBTimestamp,
            $rootModelCBID
        );

        CBDB::transaction(
            function () use (
                $spec
            ) {
                CBModels::save(
                    $spec
                );
            }
        );


        /* clean up */

        CBDB::transaction(
            function () use (
                $rootModelCBID
            ) {
                CBModels::deleteByID(
                    $rootModelCBID
                );
            }
        );

        CB_Timestamp::deleteByRootModelCBID(
            $rootModelCBID
        );


        /* done */

        return (object)[
            'succeeded' => true,
        ];
    }
    /* saveModel() */

}
