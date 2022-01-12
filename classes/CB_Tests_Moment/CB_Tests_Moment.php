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
                'name' => 'cbtimestampAccessors',
                'type' => 'server',
            ],
            (object)[
                'name' => 'build',
                'type' => 'server',
            ],
            (object)[
                'name' => 'save',
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
    cbtimestampAccessors(
    ): stdClass {

        /* -- test -- */

        $testName = 'bad cbtimestamp 1';

        $momentSpec = CBModel::createSpec(
            'CB_Moment'
        );

        $badTimestampModel = CBModel::createSpec(
            'MY_BadTimestamp'
        );

        CB_Moment::setCBTimestamp(
            $momentSpec,
            $badTimestampModel
        );

        $expectedResult = null;

        $actualResult = CB_Moment::getCBTimestamp(
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

        $testName = 'bad cbtimestamp 2';

        $momentSpec->CB_Moment_cbtimestamp_property = $badTimestampModel;

        $expectedResult = null;

        $actualResult = CB_Moment::getCBTimestamp(
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

        $testName = 'good cbtimestamp 1';

        $goodCBTimestampModel = CB_Timestamp::from(
            time()
        );

        CB_Moment::setCBTimestamp(
            $momentSpec,
            $goodCBTimestampModel
        );

        $expectedResult = $goodCBTimestampModel;

        $actualResult = CB_Moment::getCBTimestamp(
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
    /* cbtimestampAccessors() */



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



    /**
     * @return object
     */
    static function
    save(
    ): stdClass {
        $momentModelCBID = 'ad1e33d570d9d0a58a6d3f6c2b3d836cb02fe6a6';
        $momentModelUnixTimestamp = 443649600;
        $momentModelFemtoseconds = 351395993427875;

        CBDB::transaction(
            function () use (
                $momentModelCBID
            ) {
                CBModels::deleteByID(
                    $momentModelCBID
                );
            }
        );

        $momentSpec = CBModel::createSpec(
            'CB_Moment',
            $momentModelCBID
        );

        CB_Moment::setAuthorUserModelCBID(
            $momentSpec,
            ColbyUser::getCurrentUserCBID()
        );

        CB_Moment::setText(
            $momentSpec,
            "test {$momentModelCBID}"
        );

        $momentCBTimestamp = CB_Timestamp::from(
            $momentModelUnixTimestamp,
            $momentModelFemtoseconds
        );

        CB_Timestamp::reserve(
            $momentCBTimestamp,
            $momentModelCBID
        );

        CB_Moment::setCBTimestamp(
            $momentSpec,
            $momentCBTimestamp
        );

        CBDB::transaction(
            function () use (
                $momentSpec
            ) {
                CBModels::save(
                    $momentSpec
                );
            }
        );

        $registeredCBTimestamps = (
            CB_Timestamp::fetchRegisteredCBTimestampsByRootModelCBID(
                $momentModelCBID
            )
        );


        /* --- */

        $testName = 'count of registered cbtimestamps';

        $expectedResult = 1;

        $actualResult = count(
            $registeredCBTimestamps
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


        /* --- */

        $testName = 'value of registered timestamp';

        $expectedResult = $momentCBTimestamp;

        $actualResult = $registeredCBTimestamps[0];

        if (
            $actualResult != $expectedResult
        ) {
            return CBTest::resultMismatchFailure(
                $testName,
                $actualResult,
                $expectedResult
            );
        }

        /* --- */

        $testName = 'removal of model association';

        CBDB::transaction(
            function () use (
                $momentModelCBID
            ) {
                CBModels::deleteByID(
                    $momentModelCBID
                );
            }
        );

        $expectedResult = [];

        $actualResult = CBModelAssociations::fetch(
            null,
            null,
            $momentModelCBID
        );

        if (
            $actualResult != $expectedResult
        ) {
            return CBTest::resultMismatchFailure(
                $testName,
                $actualResult,
                $expectedResult
            );
        }


        /* done */


        return (object)[
            'succeeded' => 'true',
        ];
    }
    /* save() */



    /**
     * @return object
     */
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
        $unixTimestamp = 443649600;
        $femtoseconds = 0;

        CBDB::transaction(
            function () use (
                $modelCBID
            ) {
                CBModels::deleteByID(
                    $modelCBID
                );
            }
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
            $upgradedMomentSpec->CB_Moment_cbtimestamp_property
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

        $testName = 'upgraded spec reserved cbtimestamp cound';

        $reservedCBTimestamps = (
            CB_Timestamp::fetchReservedCBTimestampsByRootModelCBID(
                $modelCBID
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
                $testName,
                $actualResult,
                $expectedResult
            );
        }


        /* -- test -- */

        $testName = 'upgraded spec reserved cbtimestamp';

        $expectedResult = CB_Timestamp::from(
            $unixTimestamp,
            $femtoseconds
        );

        $actualResult = $reservedCBTimestamps[0];

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

        CBDB::transaction(
            function () use (
                $modelCBID
            ) {
                CBModels::deleteByID(
                    $modelCBID
                );
            }
        );

        return (object)[
            'succeeded' => 'true',
        ];
    }
    /* upgrade() */

}
