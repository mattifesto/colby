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

        $testSpec = (object)[
            'className' => 'CBModelUpgradeTask_Tests_Class1',
            'ID' => $testModelID,
        ];

        CBDB::transaction(
            function () use ($testSpec) {
                CBModels::save($testSpec);
            }
        );


        /* first build process version number is set */

        $testModel = CBModels::fetchModelByIDNullable($testModelID);

        $actualBuildProcessVersionNumberIsSet =
        isset($testModel->buildProcessVersionNumber);

        $expectedBuildProcessVersionNumberIsSet = false;

        if (
            $actualBuildProcessVersionNumberIsSet !==
            $expectedBuildProcessVersionNumberIsSet
        ) {
            return CBTest::resultMismatchFailure(
                'first build process version number is set',
                $actualBuildProcessVersionNumberIsSet,
                $expectedBuildProcessVersionNumberIsSet
            );
        }


        /* update build process version number */

        CBModelUpgradeTask_Tests_Class1::$buildProcessVersionNumber = 2;


        /* run update task */

        CBTasks2::runSpecificTask('CBModelUpgradeTask', $testModelID);


        /* second build process version number */

        $testModel = CBModels::fetchModelByIDNullable($testModelID);

        $actualBuildProcessVersionNumber =
        CBModel::toBuildProcessVersionNumber($testModel);

        $expectedBuildProcessVersionNumber = 2;

        if (
            $actualBuildProcessVersionNumber !==
            $expectedBuildProcessVersionNumber
        ) {
            return CBTest::resultMismatchFailure(
                'second build process version number',
                $actualBuildProcessVersionNumber,
                $expectedBuildProcessVersionNumber
            );
        }


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



    static function CBModel_build(stdClass $spec): stdClass {
        return (object)[];
    }



    static function CBModel_buildProcessVersionNumber(): int {
        return CBModelUpgradeTask_Tests_Class1::$buildProcessVersionNumber;
    }

}
