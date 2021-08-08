<?php

final class
CBConvertTests {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function
    CBHTMLOutput_JavaScriptURLs(
    ): array {
        return [
            Colby::flexpath(
                __CLASS__,
                'v675.35.js',
                cbsysurl()
            ),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */



    /**
     * @return [[<variableName>, <variableValue>]]
     */
    static function
    CBHTMLOutput_JavaScriptVariables(
    ): array {
        return [
            [
                'CBConvertTests_stringToStubTestCases',
                CBConvertTests::CBTest_stringToStub_testCases(),
            ],
            [
                'CBConvertTests_stringToURITestCases',
                CBConvertTests::CBTest_stringToURI_testCases(),
            ],
            [
                'CBConvertTests_valueAsMonikerTestCases',
                CBConvertTests::valueAsMonikerTestCases(),
            ],
            [
                'CBConvertTests_valueAsNameTestCases',
                CBConvertTests::valueAsNameTestCases(),
            ],
        ];
    }
    /* CBHTMLOutput_JavaScriptVariables() */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBConvert',
            'CBModel',
            'CBMessageMarkup',
            'CBTest',
        ];
    }



    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'centsToDollars',
                'type' => 'server',
            ],
            (object)[
                'name' => 'linesToParagraphs',
                'type' => 'server',
            ],
            (object)[
                'name' => 'stringToCleanLine',
                'type' => 'server',
            ],
            (object)[
                'name' => 'stringToLines',
                'type' => 'server',
            ],
            (object)[
                'name' => 'stringToStub',
                'type' => 'server',
            ],
            (object)[
                'name' => 'stringToURI',
                'type' => 'server',
            ],
            (object)[
                'name' => 'valueAsInt',
                'type' => 'server',
            ],
            (object)[
                'name' => 'valueAsModel',
                'type' => 'server',
            ],
            (object)[
                'name' => 'valueAsMoniker',
                'type' => 'server',
            ],
            (object)[
                'name' => 'valueAsName',
                'type' => 'server',
            ],
            (object)[
                'name' => 'valueAsNames',
                'type' => 'server',
            ],
            (object)[
                'name' => 'valueAsNumber',
                'type' => 'server',
            ],
            (object)[
                'name' => 'valueToBool',
                'type' => 'server',
            ],

            (object)[
                'name' => 'centsToDollars',
            ],
            (object)[
                'name' => 'dollarsAsCents',
            ],
            (object)[
                'name' => 'stringToCleanLine',
            ],
            (object)[
                'name' => 'stringToLines',
            ],
            (object)[
                'name' => 'stringToStub',
            ],
            (object)[
                'name' => 'stringToURI',
            ],
            (object)[
                'name' => 'valueAsInt',
            ],
            (object)[
                'name' => 'valueAsModel',
            ],
            (object)[
                'name' => 'valueAsMoniker',
            ],
            (object)[
                'name' => 'valueAsName',
            ],
            (object)[
                'name' => 'valueAsNumber',
            ],
            (object)[
                'name' => 'valueAsObject',
            ],
            (object)[
                'name' => 'valueToBool',
            ],
            (object)[
                'name' => 'valueToObject',
            ],
        ];
    }
    /* CBTest_getTests() */



    /* -- tests -- -- -- -- -- */



    /**
     * @return object
     */
    static function CBTest_centsToDollars(): stdClass {
        $tests = [
            [150, "1.50"],
            ["5", "0.05"],
            [75, "0.75"],
            ["  3500  ", "35.00"],
            ["  -5  ", "-0.05"],
            ["  -3500  ", "-35.00"],
        ];

        foreach ($tests as $test) {
            $value = $test[0];
            $actualResult = CBConvert::centsToDollars($value);
            $expectedResult = $test[1];

            if ($actualResult !== $expectedResult) {
                return CBTest::resultMismatchFailure(
                    json_encode($value),
                    $actualResult,
                    $expectedResult
                );
            }
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_centsToDollars() */



    /**
     * return object
     */
    static function CBTest_linesToParagraphs(): stdClass {
        $lines = [
            'a',
            ' b ',
            ' ',
            '    ',
            '',
            '    c',
            '  d'
        ];

        $actualResult = ColbyConvert::linesToParagraphs($lines);

        $expectedResult = [
            'a  b ',
            '    c   d'
        ];

        if ($actualResult != $expectedResult) {
            return CBTest::resultMismatchFailure(
                'test 1',
                $actualResult,
                $expectedResult
            );
        }


        /* done */

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_linesToParagraphs() */



    /**
     * @return object
     */
    static function CBTest_stringToCleanLine(): stdClass {
        $actualResult = CBConvert::stringToCleanLine(
            "   Hello.\n\nHow are you?\t\tI'm fine!\t  \n"
        );

        $expectedResult = 'Hello. How are you? I\'m fine!';

        if ($actualResult !== $expectedResult) {
            return CBTest::resultMismatchFailure(
                'test 1',
                $actualResult,
                $expectedResult
            );
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_stringToCleanLine() */



    /**
     * @return object
     */
    static function CBTest_stringToLines(): stdClass {

        /* test 1 */

        $testValue = "one\ntwo\rthree\r\nfour\rfive\nsix";
        $actualResult = CBConvert::stringToLines($testValue);
        $expectedResult = [
            'one',
            'two',
            'three',
            'four',
            'five',
            'six'
        ];

        if ($actualResult !== $expectedResult) {
            return CBTest::resultMismatchFailure(
                'test 1',
                $actualResult,
                $expectedResult
            );
        }


        /* test 2 */

        $test2String = (
            "abc \r" .
            " bcd \n" .
            " cde \r\n" .
            " def \n\r" .
            " efg \r\n\r" .
            " fgh \r\n\n" .
            " ghi \r\r\n" .
            " hij \r\n\r\n" .
            " ijk"
        );

        $actualResult = CBConvert::stringToLines($test2String);

        $expectedResult = [
            'abc ',
            ' bcd ',
            ' cde ',
            ' def ',
            '',
            ' efg ',
            '',
            ' fgh ',
            '',
            ' ghi ',
            '',
            ' hij ',
            '',
            ' ijk'
        ];

        if ($actualResult != $expectedResult) {
            return CBTest::resultMismatchFailure(
                'test 2',
                $actualResult,
                $expectedResult
            );
        }


        /* done */

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_stringToLines() */



    /**
     * @return object
     */
    static function
    CBTest_stringToStub(
    ): stdClass {
        $testCases = CBConvertTests::CBTest_stringToStub_testCases();

        for (
            $index = 0;
            $index < count($testCases);
            $index += 1
        ) {
            $originalString = $testCases[$index][0];
            $expectedResult = $testCases[$index][1];

            $actualResult = CBConvert::stringToStub(
                $originalString
            );

            if ($actualResult !== $expectedResult) {
                return CBTest::resultMismatchFailure(
                    "Test Index {$index}",
                    $actualResult,
                    $expectedResult
                );
            }
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_stringToStub() */



    /**
     * @return [[originalString, expectedResult]
     */
    private static function
    CBTest_stringToStub_testCases(
    ): array {
        return [
            [
                ' ',
                '',
            ],
            [
                'You\'re the_best',
                'youre-the-best',
            ],
            [
                '-é- __ --hello __-é-__ world  __ -ö-__',
                'hello-world'
            ],
        ];
    }
    /* CBTest_stringToStub_testCases() */



    /**
     * @return object
     */
    static function
    CBTest_stringToURI(
    ): stdClass {
        $testCases = CBConvertTests::CBTest_stringToURI_testCases();

        for (
            $index = 0;
            $index < count($testCases);
            $index += 1
        ) {
            $originalString = $testCases[$index][0];
            $expectedResult = $testCases[$index][1];

            $actualResult = CBConvert::stringToURI(
                $originalString
            );

            if ($actualResult !== $expectedResult) {
                return CBTest::resultMismatchFailure(
                    "Test Index {$index}",
                    $actualResult,
                    $expectedResult
                );
            }
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_stringToURI() */



    /**
     * @return [[originalString, expectedResult]
     */
    private static function
    CBTest_stringToURI_testCases(
    ): array {
        return [
            [
                '',
                '',
            ],
            [
                '  foo ',
                'foo',
            ],
            [
                ' / foo/',
                'foo',
            ],
            [
                '/café/piñata/',
                'caf/piata',
            ],
            [
                '   / foo /bar   /// baz/ /',
                'foo/bar/baz',
            ],
        ];
    }
    /* CBTest_stringToURI_testCases() */



    /**
     * @return object
     */
    static function CBTest_valueAsInt(): stdClass {
        $tests = [
            [0, 0],
            [5, 5],
            [5.0, 5],
            [5.1, null],
            ["5", 5],
            [" 5 ", 5],
            ["5.0", 5],
            ["5.1", null],
            ["five", null],
            [null, null],
            [true, null],
            [false, null],
            [[], null],
            [(object)[], null],
        ];

        foreach ($tests as $test) {
            $actualResult = CBConvert::valueAsInt($test[0]);
            $expectedResult = $test[1];

            if ($actualResult !== $expectedResult) {
                return CBTest::resultMismatchFailure(
                    json_encode($test[0]),
                    $actualResult,
                    $expectedResult
                );
            }
        }


        /* done */

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_valueAsInt() */



    /**
     * @return object
     */
    static function CBTest_valueAsModel(): stdClass {
        $validModels = [
            (object)[
                'className' => 'CBViewPage',
            ],
            (object)[
                'className' => ' ',
            ],
            (object)[
                'className' => ' CBViewPage',
            ],
            (object)[
                'className' => 'CBViewPage ',
            ],
        ];

        foreach ($validModels as $model) {
            if (CBConvert::valueAsModel($model) === null) {
                return CBTest::valueIssueFailure(
                    'valid model',
                    $model,
                    (
                        'The test object is a valid model but was not ' .
                        'considered so by CBConvert::valueAsModel()'
                    )
                );
            }
        }

        $invalidModels = [
            2,
            5.5,
            "hello",
            [],
            (object)[
                'className' => '',
            ],
        ];

        foreach($invalidModels as $model) {
            if (CBConvert::valueAsModel($model) !== null) {
                return CBTest::valueIssueFailure(
                    'invalid model',
                    $model,
                    (
                        'The test object is not a valid model but was ' .
                        'considered valid by CBConvert::valueAsModel()'
                    )
                );
            }
        }


        /* deprecated PHP-only class name matching tests */

        $model = (object)[
            'className' => 'CBFoo',
        ];

        if (
            CBConvert::valueAsModel(
                $model,
                ['CBFee', 'CBFaa', 'CBFoo']
            ) !== $model
        ) {
            return (object)[
                'failed' => true,
                'message' =>
                    'The (CBConvert::valueAsModel\(\) (code)) class name ' .
                    'matching test failed.',
            ];
        }


        /* done */

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_valueAsModel() */



    /**
     * @return object
     */
    static function CBTest_valueAsMoniker(): stdClass {
        foreach (CBConvertTests::valueAsMonikerTestCases() as $testCase) {
            $actualResult = CBConvert::valueAsMoniker($testCase->originalValue);
            $expectedResult = $testCase->expectedResult;

            if ($actualResult !== $expectedResult) {
                return CBTest::resultMismatchFailure(
                    json_encode($testCase->originalValue),
                    $actualResult,
                    $expectedResult
                );
            }
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_valueAsMoniker() */



    /**
     * @return object
     */
    static function CBTest_valueAsName(): stdClass {
        foreach (CBConvertTests::valueAsNameTestCases() as $testCase) {
            $actualResult = CBConvert::valueAsName($testCase->originalValue);
            $expectedResult = $testCase->expectedResult;

            if ($actualResult !== $expectedResult) {
                return CBTest::resultMismatchFailure(
                    json_encode($testCase->originalValue),
                    $actualResult,
                    $expectedResult
                );
            }
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_valueAsName() */



    /**
     * @return object
     */
    static function CBTest_valueAsNames(): stdClass {
        foreach (CBConvertTests::valueAsNamesTestCases() as $testCase) {
            $actualResult = CBConvert::valueAsNames($testCase->originalValue);
            $expectedResult = $testCase->expectedResult;

            if ($actualResult !== $expectedResult) {
                return CBTest::resultMismatchFailure(
                    json_encode($testCase->originalValue),
                    $actualResult,
                    $expectedResult
                );
            }
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_valueAsNames() */



    /**
     * @return object
     */
    static function CBTest_valueAsNumber(): stdClass {

        /**
         * Unlike the JavaScript tests for this function, the expected results
         * here are always floating point numbers because in PHP 5 === 5.0 is
         * false.
         */

        $tests = [
            [1, 1.0],
            [2.0, 2.0],
            [2.1, 2.1],
            ["3", 3.0],
            [" 4 ", 4.0],
            ["5.0", 5.0],
            ["5.1", 5.1],
            ["  3.14159  ", 3.14159],
            [" -3.14159  ", -3.14159],
            ["- 3.14159  ", null],
            ["", null],
            ["five", null],
            [true, null],
            [false, null],
            [NAN, null],
            [INF, null],
            [-0, 0.0],
            [function () { return 5; }, null],
            [(object)['a' => 1], null],
        ];

        for ($i = 0; $i < count($tests); $i += 1) {
            $test = $tests[$i];
            $value = $test[0];
            $actualResult = CBConvert::valueAsNumber($value);
            $expectedResult = $test[1];

            if ($actualResult !== $expectedResult) {
                $valueAsMessage = CBMessageMarkup::stringToMessage(
                    CBConvert::valueToPrettyJSON($value)
                );

                $actualResultAsMessage = CBMessageMarkup::stringToMessage(
                    CBConvert::valueToPrettyJSON($actualResult)
                );

                $expectedResultAsMessage = CBMessageMarkup::stringToMessage(
                    CBConvert::valueToPrettyJSON($expectedResult)
                );

                $message = <<<EOT

                    When the value (${valueAsMessage} (code)) was used as an
                    argument to CBConvert.valueAsNumber() the actual result was
                    (${actualResultAsMessage} (code)) but the expected result
                    was (${expectedResultAsMessage} (code)).

EOT;

                return (object)[
                    'succeeded' => false,
                    'message' => $message,
                ];
            }
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_valueAsNumber() */



    /**
     * @return object
     */
    static function CBTest_valueToBool(): stdClass {
        $falsyValues = [
            false,
            0,
            0.0,
            null,
            "",
            " ",
            "          ",
            "\t",
            " \t ",
            "0",
            "    0",
            "0    ",
            "  0  ",
            "\t 0 \t",
            "\n 0 \n",
        ];

        foreach ($falsyValues as $falsyValue) {
            $actualResult = CBConvert::valueToBool($falsyValue);
            $expectedResult = false;

            if ($actualResult !== $expectedResult) {
                return CBTest::resultMismatchFailure(
                    CBConvert::valueToPrettyJSON($falsyValue),
                    $actualResult,
                    $expectedResult
                );
            }
        }

        $truthyValues = [
            true,
            1,
            1.0,
            "1",
            " 1 ",
            "\t 1 \t",
            "\n 1 \n",
            "a",
            NAN,
            INF,
        ];

        foreach ($truthyValues as $truthyValue) {
            $actualResult = CBConvert::valueToBool($truthyValue);
            $expectedResult = true;

            if ($actualResult !== $expectedResult) {
                return CBTest::resultMismatchFailure(
                    CBConvert::valueToPrettyJSON($truthyValue),
                    $actualResult,
                    $expectedResult
                );
            }
        }

        /* done */

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_valueToBool() */



    /* -- functions -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function valueAsMonikerTestCases(): array {
        return [
            (object)[
                'originalValue' => 'cats',
                'expectedResult' => 'cats',
            ],
            (object)[
                'originalValue' => ' cats2',
                'expectedResult' => 'cats2',
            ],
            (object)[
                'originalValue' => '2cats ',
                'expectedResult' => '2cats',
            ],
            (object)[
                'originalValue' => '    cats    ',
                'expectedResult' => 'cats',
            ],
            (object)[
                'originalValue' => '    __cats    ',
                'expectedResult' => '__cats',
            ],
            (object)[
                'originalValue' => '    cats__    ',
                'expectedResult' => 'cats__',
            ],
            (object)[
                'originalValue' => '   dogs_cats    ',
                'expectedResult' => 'dogs_cats',
            ],
            (object)[
                'originalValue' => '    dogs cats    ',
                'expectedResult' => null,
            ],
            (object)[
                'originalValue' => '    dogs:cats    ',
                'expectedResult' => null,
            ],
            (object)[
                'originalValue' => '    dogs,cats    ',
                'expectedResult' => null,
            ],
            (object)[
                'originalValue' => '    café    ',
                'expectedResult' => null,
            ],
            (object)[
                'originalValue' => '    A    ',
                'expectedResult' => null,
            ],
            (object)[
                'originalValue' => '        ',
                'expectedResult' => null,
            ],
            (object)[
                'originalValue' => '',
                'expectedResult' => null,
            ],
        ];
    }
    /* valueAsMonikerTestCases() */



    /**
     * @return [object]
     */
    static function valueAsNameTestCases(): array {
        return [
            (object)[
                'originalValue' => 'dogs',
                'expectedResult' => 'dogs',
            ],
            (object)[
                'originalValue' => ' dogs ',
                'expectedResult' => 'dogs',
            ],
            (object)[
                'originalValue' => "\n\tdogs\t ",
                'expectedResult' => 'dogs',
            ],
            (object)[
                'originalValue' => 'dogs8 ',
                'expectedResult' => 'dogs8',
            ],
            (object)[
                'originalValue' => ' dogs',
                'expectedResult' => 'dogs',
            ],
            (object)[
                'originalValue' => ' DogsLoveToBark ',
                'expectedResult' => 'DogsLoveToBark',
            ],
            (object)[
                'originalValue' => 'dogs dogs',
                'expectedResult' => null,
            ],
            (object)[
                'originalValue' => ',dogs',
                'expectedResult' => null,
            ],
            (object)[
                'originalValue' => 'dögs',
                'expectedResult' => null,
            ],
            (object)[
                'originalValue' => '   ',
                'expectedResult' => null,
            ],
            (object)[
                'originalValue' => [],
                'expectedResult' => null,
            ],
            (object)[
                'originalValue' => (object)[
                    'foo' => 'bar',
                ],
                'expectedResult' => null,
            ],
            (object)[
                'originalValue' => 0,
                'expectedResult' => '0',
            ],
            (object)[
                'originalValue' => 123,
                'expectedResult' => '123',
            ],
            (object)[
                'originalValue' => -1,
                'expectedResult' => null,
            ],
            (object)[
                'originalValue' => 3.14,
                'expectedResult' => null,
            ],
        ];
    }
    /* valueAsNameTestCases() */



    /**
     * @return [object]
     */
    static function valueAsNamesTestCases(): array {
        return [
            (object)[
                'originalValue' => '',
                'expectedResult' => [],
            ],
            (object)[
                'originalValue' => ' ,   ,   , ',
                'expectedResult' => [],
            ],
            (object)[
                'originalValue' => ' , dogs* ,',
                'expectedResult' => null,
            ],
            (object)[
                'originalValue' => 'dogs',
                'expectedResult' => ['dogs'],
            ],
            (object)[
                'originalValue' => ' dogs',
                'expectedResult' => ['dogs'],
            ],
            (object)[
                'originalValue' => ' , , , dogs, , ,',
                'expectedResult' => ['dogs'],
            ],
            (object)[
                'originalValue' => ' , , , dogs, , cats ,',
                'expectedResult' => ['dogs', 'cats'],
            ],
            (object)[
                'originalValue' => "\t\ndogs ,cats    hippos2\t\t",
                'expectedResult' => ['dogs', 'cats', 'hippos2'],
            ],
        ];
    }
    /* valueAsNamesTestCases() */

}
