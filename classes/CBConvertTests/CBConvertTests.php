<?php

final class CBConvertTests {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */



    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v533.js', cbsysurl()),
        ];
    }



    /**
     * @return [[<variableName>, <variableValue>]]
     */
    static function CBHTMLOutput_JavaScriptVariables(): array {
        return [
            [
                'CBConvertTests_valueAsMonikerTestCases',
                CBConvertTests::valueAsMonikerTestCases(),
            ],
        ];
    }



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
     * @return [[<className>, <testName>]]
     */
    static function CBTest_JavaScriptTests(): array {
        return [
            ['CBConvert', 'dollarsAsCents'],
            ['CBConvert', 'stringToLines'],
            ['CBConvert', 'valueAsInt'],
            ['CBConvert', 'valueAsModel'],
            ['CBConvert', 'valueAsMoniker'],
            ['CBConvert', 'valueAsNumber'],
            ['CBConvert', 'valueAsObject'],
            ['CBConvert', 'valueToBool'],
            ['CBConvert', 'valueToObject'],
        ];
    }



    /**
     * @return [[<className>, <testName>]]
     */
    static function CBTest_PHPTests(): array {
        return [
            ['CBConvert', 'stringToStub'],
            ['CBConvert', 'stringToURI'],
            ['CBConvert', 'valueAsMoniker'],
            ['CBConvert', 'valueAsName'],
            ['CBConvert', 'valueAsNames'],
            ['CBConvert', 'valueAsNumber'],
            ['CBConvert', 'valueToBool'],
        ];
    }
    /* CBTest_PHPTests() */



    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'centsToDollars',
                'title' => 'CBConvert.centsToDollars()',
            ],
            (object)[
                'name' => 'centsToDollars',
                'title' => 'CBConvert::centsToDollars()',
                'type' => 'server',
            ],
            (object)[
                'name' => 'linesToParagraphs',
                'title' => 'ColbyConvert::linesToParagraphs()',
                'type' => 'server',
            ],
            (object)[
                'name' => 'stringToCleanLine',
                'title' => 'CBConvert.stringToCleanLine()',
            ],
            (object)[
                'name' => 'stringToCleanLine',
                'title' => 'CBConvert::stringToCleanLine()',
                'type' => 'server',
            ],
            (object)[
                'name' => 'stringToLines',
                'title' => 'CBConvert::stringToLines()',
                'type' => 'server',
            ],
            (object)[
                'name' => 'valueAsInt',
                'title' => 'CBConvert::valueAsInt()',
                'type' => 'server',
            ],
            (object)[
                'name' => 'valueAsModel',
                'title' => 'CBConvert::valueAsModel',
                'type' => 'server',
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
    static function CBTest_stringToStub(): stdClass {
        /* Test 1 */

        $text = ' ';
        $expected = '';
        $actual = CBConvert::stringToStub($text);

        if ($actual !== $expected) {
            return CBTest::resultMismatchFailure('Test 1', $actual, $expected);
        }

        /* Test 2 */

        $text = 'You\'re the_best';
        $expected = 'youre-the-best';
        $actual = CBConvert::stringToStub($text);

        if ($actual !== $expected) {
            return CBTest::resultMismatchFailure('Test 2', $actual, $expected);
        }

        /* Test 3 */

        $text = '-é- __ --hello __-é-__ world  __ -ö-__';
        $expected = 'hello-world';
        $actual = CBConvert::stringToStub($text);

        if ($actual !== $expected) {
            return CBTest::resultMismatchFailure('Test 3', $actual, $expected);
        }

        /* done */

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_stringToStub() */



    /**
     * @return object
     */
    static function CBTest_stringToURI(): stdClass {

        /* test 1 */

        $testcase = '   / foo /bar   /// baz/ /';
        $expected = 'foo/bar/baz';
        $result = CBConvert::stringToURI($testcase);

        if ($result != $expected) {
            return CBTest::resultMismatchFailure(
                'test 1',
                $result,
                $expected
            );
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_stringToURI() */



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
     * @deprecated use CBTest::resultMismatchFailure()
     *
     * @param mixed $result
     * @param mixed $expected
     *
     * @return string
     */
    static function resultAndExpectedToMessage($result, $expected): string {
        $resultAsJSON = CBMessageMarkup::stringToMarkup(
            CBConvert::valueToPrettyJSON($result)
        );

        $expectedAsJSON = CBMessageMarkup::stringToMarkup(
            CBConvert::valueToPrettyJSON($expected)
        );

        $message = <<<EOT

            (result (strong))

            --- pre\n{$resultAsJSON}
            ---

            (expected (strong))

            --- pre\n{$expectedAsJSON}
            ---

EOT;

        return $message;
    }



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
        ];
    }
    /* valueAsNameTestCases() */



    /**
     * @return [object]
     */
    static function valueAsNamesTestCases(): array {
        return [
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
