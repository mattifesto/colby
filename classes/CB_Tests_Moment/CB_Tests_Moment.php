<?php

final class
CB_Tests_Moment {

    /* -- CBTest interfaces -- */



    /**
     * @return [object]
     */
    static function
    CBTest_getTests(
    ): array {
        return [
            (object)[
                'name' => 'attostampAccessors',
                'type' => 'server',
            ],
            (object)[
                'name' => 'build',
                'type' => 'server',
            ],
            (object)[
                'name' => 'upgrade',
                'type' => 'server',
            ],
        ];
    }
    /* CBTest_getTests() */



    /* -- tests -- -- -- -- -- */



    /**
     * @return object
     */
    static function
    attostampAccessors(
    ): stdClass {

        /* -- test -- */

        $testName = 'bad attostamp 1';

        $momentSpec = CBModel::createSpec(
            'CB_Moment'
        );

        $badAttostampModel = CBModel::createSpec(
            'BadAttostamp'
        );

        CB_Moment::setAttostamp(
            $momentSpec,
            $badAttostampModel
        );

        $expectedResult = null;

        $actualResult = CB_Moment::getAttostamp(
            $momentSpec
        );

        if (
            $actualResult !== $expectedResult
        ) {
            return CBTest::resultMismatchFailure(
                $testName,
                $actualResult,
                $expectedResult
            );
        }


        /* -- test -- */

        $testName = 'bad attostamp 2';

        $momentSpec->CB_Moment_attostamp_property = $badAttostampModel;

        $expectedResult = null;

        $actualResult = CB_Moment::getAttostamp(
            $momentSpec
        );

        if (
            $actualResult !== $expectedResult
        ) {
            return CBTest::resultMismatchFailure(
                $testName,
                $actualResult,
                $expectedResult
            );
        }


        /* -- test -- */

        $testName = 'good attostamp 1';

        $goodAttostampModel = CB_Attostamp::from(
            time()
        );

        CB_Moment::setAttostamp(
            $momentSpec,
            $goodAttostampModel
        );

        $expectedResult = $goodAttostampModel;

        $actualResult = CB_Moment::getAttostamp(
            $momentSpec
        );

        if (
            $actualResult !== $expectedResult
        ) {
            return CBTest::resultMismatchFailure(
                $testName,
                $actualResult,
                $expectedResult
            );
        }


        /* -- done -- */

        return (object)[
            'succeeded' => 'true',
        ];
    }
    /* attostampAccessors() */



    /**
     * @return object
     */
    static function
    build(
    ): stdClass {
        $momentModelCBID = '3d30657bf2e4264cc8fd6f3940919f2797c73ca2';
        $originalAuthorUserModelCBID = '2a5cc78ddc20b8a6bacf4cb5e41df591e79fa148';
        $originalText = 'foo bar baz';
        $originalCreatedTimestamp = time();

        $momentSpec = CBModel::createSpec(
            'CB_Moment',
            $momentModelCBID
        );


        CB_Moment::setAuthorUserModelCBID(
            $momentSpec,
            $originalAuthorUserModelCBID
        );

        CB_Moment::setCreatedTimestamp(
            $momentSpec,
            $originalCreatedTimestamp
        );

        CB_Moment::setText(
            $momentSpec,
            $originalText
        );


        $momentModel = CBModel::build(
            $momentSpec
        );

        $retreivedAuthorUserModelCBID = CB_Moment::getAuthorUserModelCBID(
            $momentModel
        );

        if (
            $retreivedAuthorUserModelCBID !== $originalAuthorUserModelCBID
        ) {
            return CBTest::resultMismatchFailure(
                'author user model CBID',
                $retreivedAuthorUserModelCBID,
                $originalAuthorUserModelCBID
            );
        }


        $retreivedCreatedTimestamp = CB_Moment::getCreatedTimestamp(
            $momentModel
        );

        if (
            $retreivedCreatedTimestamp !== $originalCreatedTimestamp
        ) {
            return CBTest::resultMismatchFailure(
                'created timestamp',
                $retreivedCreatedTimestamp,
                $originalCreatedTimestamp
            );
        }

        $retreivedText = CB_Moment::getText(
            $momentModel
        );

        if (
            $retreivedText !== $originalText
        ) {
            return CBTest::resultMismatchFailure(
                'text',
                $retreivedText,
                $originalText
            );
        }

        return (object)[
            'succeeded' => 'true',
        ];
    }
    /* build() */



    static function
    upgrade(
    ): stdClass {
        $modelCBID = '66cc4077e3a7d67580e551ba87a2838de7161daa';

        /**
         * @TODO 2022_01_09
         *
         *      This test is assuming the folloing Unix timestamp is available
         *      on every website. There should be a function to find us an
         *      available Unix timestamp instead.
         */
        $unixTimestamp = 394839;
        $attoseconds = 0;

        CBTest::deleteModelAndAllAttostamps(
            $modelCBID
        );

        /* -- test -- */

        $testName = 'root spec without a CBID';

        $actualResult = 'no exception was thrown';
        $expectedResult = '0a851becdee9fee6b5a175881c21ec24400468c3';

        try {
            CBModel::upgrade(
                CBModel::createSpec(
                    'CB_Moment'
                )
            );
        } catch (
            Throwable $throwable
        ) {
            $actualResult = CBException::throwableToSourceCBID(
                $throwable
            );
        }

        if (
            $actualResult !== $expectedResult
        ) {
            return CBTest::resultMismatchFailure(
                $testName,
                $actualResult,
                $expectedResult
            );
        }


        /* -- test -- */

        $testName = 'empty spec';

        $momentSpec = CBModel::createSpec(
            'CB_Moment',
            $modelCBID
        );

        $upgradedMomentSpec = CBModel::upgrade(
            $momentSpec
        );

        $expectedResult = false;

        $actualResult = isset(
            $upgradedMomentSpec->CB_Moment_attostamp_property
        );

        if (
            $actualResult !== $expectedResult
        ) {
            return CBTest::resultMismatchFailure(
                $testName,
                $actualResult,
                $expectedResult
            );
        }

        /* -- test -- */

        $testName = 'empty spec 2';

        $expectedResult = false;

        $actualResult = isset(
            $upgradedMomentSpec->CB_Moment_createdTimestamp
        );

        if (
            $actualResult !== $expectedResult
        ) {
            return CBTest::resultMismatchFailure(
                $testName,
                $actualResult,
                $expectedResult
            );
        }


        /* -- test -- */

        $testName = 'upgrade';

        CB_Moment::setCreatedTimestamp(
            $momentSpec,
            $unixTimestamp
        );

        $upgradedMomentSpec = CBModel::upgrade(
            $momentSpec
        );

        $expectedResult = false;

        $actualResult = isset(
            $upgradedMomentSpec->CB_Moment_createdTimestamp
        );

        if (
            $actualResult !== $expectedResult
        ) {
            return CBTest::resultMismatchFailure(
                $testName,
                $actualResult,
                $expectedResult
            );
        }

        /* -- test -- */

        $testName = 'upgraded spec reserved attostamp cound';

        $reservedAttostamps = (
            CB_Attostamp::fetchReservedAttostampsByRootModelCBID(
                $modelCBID
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
                $testName,
                $actualResult,
                $expectedResult
            );
        }


        /* -- test -- */

        $testName = 'upgraded spec reserved attostamp';

        $expectedResult = CB_Attostamp::from(
            $unixTimestamp,
            $attoseconds
        );

        $actualResult = $reservedAttostamps[0];

        if (
            $actualResult != $expectedResult
        ) {
            return CBTest::resultMismatchFailure(
                $testName,
                $actualResult,
                $expectedResult
            );
        }


        /* -- done -- */

        CBTest::deleteModelAndAllAttostamps(
            $modelCBID
        );

        return (object)[
            'succeeded' => 'true',
        ];
    }
    /* upgrade() */

}
