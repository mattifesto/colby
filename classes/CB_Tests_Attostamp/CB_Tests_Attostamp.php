<?php

final class
CB_Tests_Attostamp {

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

        $firstAttostamp = CB_Attostamp::from(
            1641483605,
            740031000070034020
        );

        /* test: restore initial state 1 */

        CB_Attostamp::deleteAttostampsByRootModelCBID(
            $rootModelCBID
        );

        $reservedAttostamps = (
            CB_Attostamp::fetchReservedAttostampsByRootModelCBID(
                $rootModelCBID
            )
        );

        $expectedResult = 0;

        $actualResult = count(
            $reservedAttostamps
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

        $registeredAttostamps = (
            CB_Attostamp::fetchRegisteredAttostampsByRootModelCBID(
                $rootModelCBID
            )
        );

        $expectedResult = 0;

        $actualResult = count(
            $registeredAttostamps
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

        $actualResult = CB_Attostamp::reserve(
            $firstAttostamp,
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

        $reservedAttostamps = (
            CB_Attostamp::fetchReservedAttostampsByRootModelCBID(
                $rootModelCBID
            )
        );

        $expectedResult = 1;

        $actualResult = count(
            $reservedAttostamps
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

        $actualResult = CB_Attostamp::reserve(
            $firstAttostamp,
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

        $secondAttostamp = CB_Attostamp::reserveNear(
            $firstAttostamp,
            $rootModelCBID
        );

        $expectedResult = true;

        $actualResult = (
            $secondAttostamp !== $firstAttostamp
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

        $reservedAttostamps = (
            CB_Attostamp::fetchReservedAttostampsByRootModelCBID(
                $rootModelCBID
            )
        );

        $expectedResult = 2;

        $actualResult = count(
            $reservedAttostamps
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


        /* test: register first attostamp */

        $expectedResult = true;

        $actualResult = CB_Attostamp::register(
            $firstAttostamp,
            $rootModelCBID
        );

        if (
            $actualResult != $expectedResult
        ) {
            return CBTest::resultMismatchFailure(
                'register first attostamp',
                $actualResult,
                $expectedResult
            );
        }


        /* test: register second attostamp */

        $expectedResult = true;

        $actualResult = CB_Attostamp::register(
            $secondAttostamp,
            $rootModelCBID
        );

        if (
            $actualResult != $expectedResult
        ) {
            return CBTest::resultMismatchFailure(
                'register second attostamp',
                $actualResult,
                $expectedResult
            );
        }


        /* test: register verification 1 */

        $reservedAttostamps = (
            CB_Attostamp::fetchReservedAttostampsByRootModelCBID(
                $rootModelCBID
            )
        );

        $expectedResult = 0;

        $actualResult = count(
            $reservedAttostamps
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

        $registeredAttostamps = (
            CB_Attostamp::fetchRegisteredAttostampsByRootModelCBID(
                $rootModelCBID
            )
        );

        $expectedResult = 2;

        $actualResult = count(
            $registeredAttostamps
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

        CB_Attostamp::deleteAttostampsByRootModelCBID(
            $rootModelCBID
        );

        $reservedAttostamps = (
            CB_Attostamp::fetchReservedAttostampsByRootModelCBID(
                $rootModelCBID
            )
        );

        $expectedResult = 0;

        $actualResult = count(
            $reservedAttostamps
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

        $registeredAttostamps = (
            CB_Attostamp::fetchRegisteredAttostampsByRootModelCBID(
                $rootModelCBID
            )
        );

        $expectedResult = 0;

        $actualResult = count(
            $registeredAttostamps
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

        CB_Attostamp::deleteAttostampsByRootModelCBID(
            $rootModelCBID
        );

        $firstAttostamp = CB_Attostamp::from(
            1641483602,
            740041000050002030
        );


        /* test: use attostamp without reserving */

        $spec = CBModel::createSpec(
            'CB_Tests_Attostamp_TestModel',
            $rootModelCBID
        );

        CB_Tests_Attostamp_TestModel::setAttostamp(
            $spec,
            $firstAttostamp
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
                'use attostamp without reserving',
                $actualSourceCBID,
                $expectedSourceCBID
            );
        }


        /* test: use attostamp correctly */

        CB_Attostamp::reserve(
            $firstAttostamp,
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

        CB_Attostamp::deleteAttostampsByRootModelCBID(
            $rootModelCBID
        );


        /* done */

        return (object)[
            'succeeded' => true,
        ];
    }
    /* saveModel() */

}
