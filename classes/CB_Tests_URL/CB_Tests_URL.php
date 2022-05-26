<?php

final class
CB_Tests_URL
{
    // -- CBTest interfaces



    /**
     * @return [object]
     */
    static function
    CBTest_getTests(
    ): array
    {
        return
        [
            (object)
            [
                'name' =>
                'convertRawStringToPrettyWord_test',

                'type' =>
                'server',
            ],
            (object)
            [
                'name' =>
                'convertRawStringToURLStub_test',

                'type' =>
                'server',
            ],
            (object)
            [
                'name' =>
                'potentialURLStubIsValid_test',

                'type' =>
                'server',
            ],
        ];
    }
    /* CBTest_getTests() */



    // -- tests



    /**
     * @return object
     */
    static function
    convertRawStringToPrettyWord_test(
    ): stdClass
    {
        $testCases =
        CB_Tests_URL::getTestCases();

        $testIndex =
        0;

        while (
            $testIndex < count($testCases)
        ) {
            $testCase =
            $testCases[$testIndex];

            $rawString =
            $testCase[0];

            $expectedResult =
            $testCase[2];

            $actualResult =
            CB_URL::convertRawStringToPrettyWord(
                $rawString
            );

            if (
                $actualResult !== $expectedResult
            ) {
                return
                CBTest::resultMismatchFailure(
                    "test index ${testIndex}",
                    $actualResult,
                    $expectedResult
                );
            }

            $testIndex +=
            1;
        }

        // done

        return
        (object)
        [
            'succeeded' =>
            true,
        ];
    }
    // convertRawStringToPrettyWord_test()



    /**
     * @return object
     */
    static function
    convertRawStringToURLStub_test(
    ): stdClass
    {
        $testCases =
        CB_Tests_URL::getTestCases();

        $testIndex =
        0;

        while (
            $testIndex < count($testCases)
        ) {
            $testCase =
            $testCases[$testIndex];

            $rawString =
            $testCase[0];

            $expectedResult =
            $testCase[1];

            $actualResult =
            CB_URL::convertRawStringToURLStub(
                $rawString
            );

            if (
                $actualResult !== $expectedResult
            ) {
                return
                CBTest::resultMismatchFailure(
                    "test index ${testIndex}",
                    $actualResult,
                    $expectedResult
                );
            }

            $testIndex +=
            1;
        }

        // done

        return
        (object)
        [
            'succeeded' =>
            true,
        ];
    }
    // convertRawStringToURLStub()



    /**
     * @return object
     */
    static function
    potentialURLStubIsValid_test(
    ): stdClass
    {
        $testCases =
        CB_Tests_URL::getTestCases();

        $testIndex =
        0;

        while (
            $testIndex < count($testCases)
        ) {
            $testCase =
            $testCases[$testIndex];

            $invalidURLStub =
            $testCase[0];

            $validURLStub =
            $testCase[1];

            $actualResult =
            CB_URL::potentialURLStubIsValid(
                $invalidURLStub
            );

            $expectedResult =
            false;

            if (
                $actualResult !== $expectedResult
            ) {
                return
                CBTest::resultMismatchFailure(
                    "test index ${testIndex} (invalid stub)",
                    $actualResult,
                    $expectedResult
                );
            }

            $actualResult =
            CB_URL::potentialURLStubIsValid(
                $validURLStub
            );

            $expectedResult =
            true;

            if (
                $actualResult !== $expectedResult
            ) {
                return
                CBTest::resultMismatchFailure(
                    "test index ${testIndex} (valid stub)",
                    $actualResult,
                    $expectedResult
                );
            }

            $testIndex +=
            1;
        }

        // done

        return
        (object)
        [
            'succeeded' =>
            true,
        ];
    }
    // potentialURLStubIsValid_test()



    // -- functions



    /**
     * @return [[<raw string>, <url stub>, <pretty word>]]
     */
    static function
    getTestCases(
    ): array
    {
        return
        [
            [
                'Hello World',
                'hello-world',
                'HelloWorld',
            ],
            [
                'Hello   World',
                'hello-world',
                'HelloWorld',
            ],
            [
                'Hello -World',
                'hello-world',
                'HelloWorld',
            ],
            [
                'Hello   -World',
                'hello-world',
                'HelloWorld',
            ],
            [
                'Hello- - -     -World',
                'hello-world',
                'HelloWorld',
            ],
            [
                '  ߾He߾llo߾  World ߾  ',
                'hello-world',
                'HelloWorld',
            ],
            [
                '  😀He😀llo😀  World 😀  ',
                'hello-world',
                'HelloWorld',
            ],
            [
                '  Hello  World  ',
                'hello-world',
                'HelloWorld',
            ],
            [
                '  Hello  /  World  ',
                'hello-world',
                'HelloWorld',
            ],
            [
                'A b C d Ť Ŧ Ʒ Ǌ Ѳ Ԭ ℳ Ⱓ Ꙫ 𐲖 𝓚 𝔅 𝕎 𝕽',
                'a-b-c-d-ť-ŧ-ʒ-ǌ-ѳ-ԭ-ℳ-ⱓ-ꙫ-𐳖-𝓚-𝔅-𝕎-𝕽',
                'AbCdŤŦƷǊѲԬℳⰣꙪ𐲖𝓚𝔅𝕎𝕽',
            ],
            [
                ' ß ',
                'ß',
                'ß',
            ],
            [
                '  Jalapeño  ',
                'jalapeño',
                'Jalapeño',
            ],
            [
                '  Café  ',
                'café',
                'Café',
            ],
        ];
    }
    // convertRawStringToURLStub_testCases()

}
