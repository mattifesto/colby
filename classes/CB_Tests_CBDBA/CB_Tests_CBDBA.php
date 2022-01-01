<?php

final class
CB_Tests_CBDBA {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function
    CBTest_getTests(
    ): array {
        return [
            (object)[
                'name' => 'tableHasIndexNamed',
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
    CBTest_tableHasIndexNamed(
    ): stdClass {
        $expectedResult = true;
        $actualResult = CBDBA::tableHasIndexNamed(
            'CBModels',
            'className_created'
        );

        if (
            $actualResult !== $expectedResult
        ) {
            return CBTest::resultMismatchFailure(
                'Table: CBModels, Index: className_created',
                $actualResult,
                $expectedResult
            );
        }


        $expectedResult = false;
        $actualResult = CBDBA::tableHasIndexNamed(
            'CBModels',
            'index_name_that_does_not_exist'
        );

        if (
            $actualResult !== $expectedResult
        ) {
            return CBTest::resultMismatchFailure(
                'Table: CBModels, Index: index_name_that_does_not_exist',
                $actualResult,
                $expectedResult
            );
        }


        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_tableHasIndexNamed() */

}
