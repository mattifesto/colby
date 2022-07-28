<?php

final class
CB_Tests_SCCountry
{
    // -- CBTest interfaces



    /**
     * @return [object]
     */
    static function
    CBTest_getTests(
    ): array
    {
        $tests =
        [
            (object)
            [
                'name' =>
                'CB_Tests_SCCountry_build',

                'type' =>
                'server',
            ],
        ];

        return $tests;
    }
    // CBTest_getTests()



    // -- tests



    /**
     * @return void
     */
    static function
    CB_Tests_SCCountry_build(
    ): void
    {
        $testCountryModelCBID =
        'afa3b515624d75a7c0f4aac888e5761d805e4ac9';



        // delete an orphaned test model

        CBDB::transaction(
            function () use (
                $testCountryModelCBID
            ): void
            {
                CBModels::deleteByID(
                    $testCountryModelCBID
                );
            }
        );



        // create test country spec

        $testCountrySpec =
        CBModel::createSpec(
            'SCCountry',
            $testCountryModelCBID
        );

        SCCountry::setTitle(
            $testCountrySpec,
            'Test Country Model'
        );

        $testCountrySpec->isActive =
        false;

        CBDB::transaction(
            function () use (
                $testCountrySpec
            ): void
            {
                CBModels::save(
                    $testCountrySpec
                );
            }
        );



        // verify test country model

        $actualTestCountryModel =
        CBModels::fetchModelByCBID(
            $testCountryModelCBID
        );

        $expectedTestCountryModel =
        (object)
        [
            'isActive' =>
            false,

            'isDefault' =>
            false,

            'moniker' =>
            null,

            'title' =>
            'Test Country Model',

            'className' =>
            'SCCountry',

            'ID' =>
            'afa3b515624d75a7c0f4aac888e5761d805e4ac9',

            'CBModel_processVersionNumber_property' =>
            '2022_07_28_1658973516',

            'version' =>
            1,
        ];

        if (
            $actualTestCountryModel !=
            $expectedTestCountryModel
        ) {
            $cbmessage =
            CBTest::generateTestResultMismatchCBMessage(
                $actualTestCountryModel,
                $expectedTestCountryModel
            );

            $testName =
            'build test 1';

            throw
            new CBException(
                $testName,
                $cbmessage,
                'b258c7e884d046042e3aa8f19ad74de7643f9906'
            );
        }



        // clean up



        CBDB::transaction(
            function () use (
                $testCountryModelCBID
            ): void
            {
                CBModels::deleteByID(
                    $testCountryModelCBID
                );
            }
        );
    }
    // CB_Tests_SCCountry_build()

}
