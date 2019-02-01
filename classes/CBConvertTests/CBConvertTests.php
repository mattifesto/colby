<?php

final class CBConvertTests {

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v469.js', cbsysurl()),
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
            ['CBConvert', 'centsToDollars'],
            ['CBConvert', 'dollarsAsCents'],
            ['CBConvert', 'valueAsInt'],
            ['CBConvert', 'valueAsModel'],
            ['CBConvert', 'valueAsNumber'],
            ['CBConvert', 'valueAsObject'],
            ['CBConvert', 'valueToBool'],
            ['CBConvert', 'valueToObject'],
        ];
    }

    /**
     * @return [[<class>, <test>]]
     */
    static function CBTest_PHPTests(): array {
        return [
            ['CBConvert', 'centsToDollars'],
            ['CBConvert', 'stringToStub'],
            ['CBConvert', 'stringToURI'],
            ['CBConvert', 'valueAsMoniker'],
            ['CBConvert', 'valueAsNumber'],
            ['CBConvert', 'valueToBool'],
        ];
    }

    /* -- tests -- -- -- -- -- */

    /**
     * @return null
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

    /**
     * @return null
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

    /**
     * @return object
     */
    static function CBTest_stringToURI(): stdClass {

        /* test 1 */
        $testcase = '   / foo /bar   /// baz/ /';
        $expected = 'foo/bar/baz';
        $result = CBConvert::stringToURI($testcase);

        if ($result != $expected) {
            return (object)[
                'message' =>
                    "The result of test 1 did not match the expected value.\n\n" .
                    CBConvertTests::resultAndExpectedToMessage($result, $expected),
            ];
        } else {
            return (object)[
                'succeeded' => true,
            ];
        }
    }

    /**
     * @return object
     */
    static function CBTest_valueAsMoniker(): stdClass {
        /* subtest 1 */

        $actualResult = CBConvert::valueAsMoniker(' love_life ');
        $expectedResult = 'love_life';

        if ($actualResult !== $expectedResult) {
            return CBTest::resultMismatchFailure('subtest 1', $actualResult, $expectedResult);
        }

        /* subtest 2 */

        $actualResult = CBConvert::valueAsMoniker(' love life ');
        $expectedResult = null;

        if ($actualResult !== $expectedResult) {
            return CBTest::resultMismatchFailure('subtest 2', $actualResult, $expectedResult);
        }

        /* subtest 3 */

        $actualResult = CBConvert::valueAsMoniker(' 2love ');
        $expectedResult = null;

        if ($actualResult !== $expectedResult) {
            return CBTest::resultMismatchFailure('subtest 3', $actualResult, $expectedResult);
        }

        /* subtest 4 */

        $actualResult = CBConvert::valueAsMoniker(' love2 ');
        $expectedResult = 'love2';

        if ($actualResult !== $expectedResult) {
            return CBTest::resultMismatchFailure('subtest 4', $actualResult, $expectedResult);
        }

        /* done */

        return (object)[
            'succeeded' => true,
        ];
    }

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
     * return void
     */
    static function linesToParagraphsTest() {
        $lines      = ['a', ' b ', ' ', '    ', '', '    c', '  d'];
        $expected   = ['a  b ', '    c   d'];
        $actual     = ColbyConvert::linesToParagraphs($lines);
        $diff       = array_diff($actual, $expected);

        if ($diff) {
            $JSON = json_encode($diff);
            throw new Exception("runTestForLineArrayToParagraphArray: The array returned does not match the expected array with these differences: {$JSON}.");
        }
    }

    /**
     * return void
     */
    static function textToLinesTest() {
        $text       = "abc \r bcd \n cde \r\n def \n\r efg \r\n\r fgh \r\n\n ghi \r\r\n hij \r\n\r\n ijk";
        $expected   = ['abc ', ' bcd ', ' cde ', ' def ',
                       '', ' efg ', '', ' fgh ', '', ' ghi ', '', ' hij ', '', ' ijk'];
        $actual     = ColbyConvert::textToLines($text);
        $diff       = array_diff($actual, $expected);

        if ($diff) {
            $JSON = json_encode($diff);
            throw new Exception("runTestForTextToLineArray: The array returned does not match the expected array with these differences: {$JSON}.");
        }
    }

    /**
     * @return null
     */
    static function valueAsIntTest() {
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
            $result = CBConvert::valueAsInt($test[0]);

            if ($result !== $test[1]) {
                $inputAsJSON = json_encode($test[0]);
                $actualResultAsJSON = json_encode($result);
                $expectedResultAsJSON = json_encode($test[1]);
                throw new Exception("The tested input: {$inputAsJSON} produced: {$actualResultAsJSON} instead of: {$expectedResultAsJSON}");
            }
        }
    }

    /**
     * @return object|null
     */
    static function valueAsModelTest(): ?stdClass {
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

        foreach($validModels as $model) {
            if (CBConvert::valueAsModel($model) === null) {
                $modelAsJSON = CBConvert::valueToPrettyJSON($model);
                $message = <<<EOT

                    The following object is a valid model but not considered
                    so by CBConvert::valueAsModel():

                    --- pre\n{$modelAsJSON}
                    ---

EOT;

                return (object)[
                    'failed' => true,
                    'message' => $message,
                ];
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
                $modelAsJSON = CBConvert::valueToPrettyJSON($model);
                $message = <<<EOT

                    The following object is an invalid model but not considered
                    so by CBConvert::valueAsModel():

                    --- pre\n{$modelAsJSON}
                    ---

EOT;

                return (object)[
                    'failed' => true,
                    'message' => $message,
                ];
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

        return null;
    }
}
