<?php

final class CBModelUpgradeTask_Tests {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'general',
                'title' => 'CBModelUpgradeTask',
                'type' => 'server',
            ],
        ];
    }



    /* -- tests -- -- -- -- -- */



    /**
     * @return object
     */
    static function CBTest_general(): stdClass {
        $testModelID = '94c8ca0ec70555f88d28b9a9fc76eb019b9c88be';

        CBDB::transaction(
            function () use ($testModelID) {
                CBModels::deleteByID($testModelID);
            }
        );

        $testSpecVersion0 = (object)[
            'className' => 'CBModelUpgradeTask_Tests_Class1',
            'ID' => $testModelID,
        ];

        CBDB::transaction(
            function () use ($testSpecVersion0) {
                CBModels::save($testSpecVersion0);
            }
        );


        /* test spec version 1 */

        $testSpecVersion1 = CBModels::fetchSpecByIDNullable(
            $testModelID
        );

        $actualBuildProcessVersionNumber = CBModel::valueAsInt(
            $testSpecVersion1,
            'buildProcessVersionNumber'
        );

        $expectedBuildProcessVersionNumber = 1;


        if (
            $actualBuildProcessVersionNumber !==
            $expectedBuildProcessVersionNumber
        ) {
            return CBTest::resultMismatchFailure(
                'test spec version 1',
                $actualBuildProcessVersionNumber,
                $expectedBuildProcessVersionNumber
            );
        }


        /* update build process version number */

        CBModelUpgradeTask_Tests_Class1::$buildProcessVersionNumber = 2;


        /* run update task */

        CBTasks2::runSpecificTask('CBModelUpgradeTask', $testModelID);


        /* test spec version 1 */

        $testSpecVersion2 = CBModels::fetchSpecByIDNullable(
            $testModelID
        );

        $actualBuildProcessVersionNumber = CBModel::valueAsInt(
            $testSpecVersion2,
            'buildProcessVersionNumber'
        );

        $expectedBuildProcessVersionNumber = 2;


        if (
            $actualBuildProcessVersionNumber !==
            $expectedBuildProcessVersionNumber
        ) {
            return CBTest::resultMismatchFailure(
                'test spec version 2',
                $actualBuildProcessVersionNumber,
                $expectedBuildProcessVersionNumber
            );
        }


        /* reset */

        CBModelUpgradeTask_Tests_Class1::$buildProcessVersionNumber = 1;


        /* delete test model */

        CBDB::transaction(
            function () use ($testModelID) {
                CBModels::deleteByID($testModelID);
            }
        );

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_general() */

}



final class CBModelUpgradeTask_Tests_Class1 {

    static $buildProcessVersionNumber = 1;



    static function CBModel_build(
        stdClass $spec
    ): stdClass {
        return (object)[];
    }



    static function CBModel_upgrade(
        stdClass $upgradableSpec
    ): stdClass {
        $upgradableSpec->buildProcessVersionNumber =
        CBModelUpgradeTask_Tests_Class1::$buildProcessVersionNumber;

        return $upgradableSpec;
    }

}
