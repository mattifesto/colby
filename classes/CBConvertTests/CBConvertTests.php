<?php

final class CBConvertTests {

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
     * @return [[<class>, <test>]]
     */
    static function CBUnitTests_tests(): array {
        return [
            ['CBConvert', 'stringToURI'],
        ];
    }

    /**
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
    static function textToStubTest() {
        $text = ' ';
        $stub = ColbyConvert::textToStub($text);

        if ($stub !== '') {
            throw new Exception('Test failed');
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
     * @return ?stdClass
     */
    static function valueAsModelTest(): ?stdClass {
        $validModels = [
            (object)[
                'className' => 'CBViewPage',
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

        return null;
    }
}
