<?php

final class CBProjectionTests {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'general',
                'title' => 'CBProjection',
                'type' => 'server',
            ],
        ];
    }



    /* -- tests -- -- -- -- -- */




    /**
     * @return object
     */
    static function CBTest_general(): stdClass {
        $projection = CBProjection::withSize(100, 200);
        $projection = CBProjection::scale($projection, 0.5);


        $actualResult = $projection->destination->width;
        $expectedResult = 50;

        if ($actualResult != $expectedResult) {
            return CBTest::resultMismatchFailure(
                'width test 1',
                $actualResult,
                $expectedResult
            );
        }


        $actualResult = $projection->destination->height;
        $expectedResult = 100;

        if ($actualResult != $expectedResult) {
            return CBTest::resultMismatchFailure(
                'height test 1',
                $actualResult,
                $expectedResult
            );
        }


        $projection = CBProjection::withSize(100, 200);
        $projection = CBProjection::applyOpString($projection, "s0.5");


        $actualResult = $projection->destination->width;
        $expectedResult = 50;

        if ($actualResult != $expectedResult) {
            return CBTest::resultMismatchFailure(
                'width test 2',
                $actualResult,
                $expectedResult
            );
        }


        $actualResult = $projection->destination->height;
        $expectedResult = 100;

        if ($actualResult != $expectedResult) {
            return CBTest::resultMismatchFailure(
                'height test 2',
                $actualResult,
                $expectedResult
            );
        }


        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_general() */

}
