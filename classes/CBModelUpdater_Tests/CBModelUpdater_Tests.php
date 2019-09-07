<?php

final class CBModelUpdater_Tests {

    /* -- CBTest interfaces -- -- -- -- -- */

    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'title' => 'CBModelUpdater::updateIfExists()',
                'type' => 'server',
                'name' => 'updateIfExists'
            ],
        ];
    }
    /* CBTest_getTests() */


    /* -- tests -- -- -- -- -- */

    /**
     * @return object
     */
    static function CBTest_updateIfExists(): stdClass {
        $modelID = 'dc325defccbfaeb390304b35e4073d2a8c19063a';
        $spec = (object)[
            'className' => 'CBMessageView',
            'ID' => $modelID,
        ];


        /* regular update */

        CBModelUpdater::update($spec);

        $actualResult = CBModels::fetchSpecByIDNullable($modelID);
        $expectedResult = (object)[
            'className' => 'CBMessageView',
            'ID' => $modelID,
            'version' => 1,
        ];

        if ($actualResult != $expectedResult) {
            return CBTest::resultMismatchFailure(
                'update',
                $actualResult,
                $expectedResult
            );
        }


        /* delete model */

        CBModels::deleteByID($modelID);


        /* update if exists */

        CBModelUpdater::updateIfExists($spec);

        $actualResult = CBModels::fetchSpecByIDNullable($modelID);
        $expectedResult = null;

        if ($actualResult !== $expectedResult) {
            return CBTest::resultMismatchFailure(
                'updateIfExists',
                $actualResult,
                $expectedResult
            );
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_updateIfExists() */
}
