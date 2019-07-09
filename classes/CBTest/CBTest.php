<?php

final class CBTest {

    /* -- CBAjax interfaces -- -- -- -- -- */

    /**
     * @return array
     */
    static function CBAjax_getPHPTests(): array {
        return CBTest::PHPTests();
    }
    /* CBAjax_getPHPTests() */


    /**
     * @return string
     */
    static function CBAjax_getPHPTests_group(): string {
        return 'Developers';
    }
    /* CBAjax_getPHPTests_group() */


    /**
     * @param object $args
     *
     *      {
     *          className: string
     *          testName: string?
     *      }
     *
     * @return object
     */
    static function CBAjax_run($args): stdClass {
        try {
            $className = CBModel::valueToString($args, 'className');
            $testName = CBModel::valueToString($args, 'testName');

            if (
                is_callable(
                    $function = "{$className}::CBTest_{$testName}"
                )

                ||

                /* deprecated */
                is_callable(
                    $function = "{$className}Tests::CBTest_{$testName}"
                )
            ) {
                $result = call_user_func($function);

                if (!is_object($result)) {
                    $resultAsJSON = CBConvert::valueToPrettyJSON($result);

                    $functionAsMarkup = CBMessageMarkup::stringToMarkup(
                        $function
                    );

                    $result = (object)[
                        'succeeded' => false,
                        'message' => <<<EOT

                            This test failed because the function
                            ({$functionAsMarkup}\(\) (code)) did not return an
                            object. Instead it returned:

                            --- pre\n{$resultAsJSON}
                            ---

EOT
                    ];
                }
            } else if (
                is_callable(
                    $function = "{$className}Tests::{$testName}Test"
                )
            ) {
                /* deprecated */
                $result = call_user_func($function);
                $result = (object)[
                    'succeeded' => empty($result->failed),
                    'message' => CBModel::valueToString($result, 'message'),
                ];
            } else if (
                $testName === ''

                &&

                is_callable(
                    $function = "{$className}Tests::test"
                )
            ) {
                /* deprecated */
                $result = call_user_func($function);
                $result = (object)[
                    'succeeded' => empty($result->failed),
                    'message' => CBModel::valueToString($result, 'message'),
                ];
            } else {
                $result = (object)[
                    'succeeded' => false,
                    'message' => <<<EOT

                        No test is available to run for:

                        class name: "{$className}"((br))
                        test name: "{$testName}"

EOT
                ];
            }
        } catch (Throwable $throwable) {
            $message = CBMessageMarkup::stringToMarkup(
                CBConvert::throwableToMessage($throwable)
            );

            $stack = CBMessageMarkup::stringToMarkup(
                CBConvert::throwableToStackTrace($throwable)
            );

            $message = <<<EOT

                $message

                --- pre\n{$stack}
                ---

EOT;

            $result = (object)[
                'succeeded' => false,
                'message' => $message,
            ];

            CBErrorHandler::report($throwable);
        }

        return $result;
    }
    /* CBAjax_run() */


    /**
     * @return string
     */
    static function CBAjax_run_group(): string {
        return 'Developers';
    }
    /* CBAjax_run_group() */


    /* -- CBHTMLOutput interfaces -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v474.js', cbsysurl()),
        ];
    }
    /* CBHTMLOutput_JavaScriptURLs() */


    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBConvert',
            'CBMessageMarkup',
            'CBModel',
            'Colby',
        ];
    }
    /* CBHTMLOutput_requiredClassNames() */


    /* -- functions -- -- -- -- -- */

    /**
     * @return [object]
     *
     *      {
     *          title: string
     *          description: string
     *          name: string
     *
     *              The name of the test.
     *
     *          testClassName: string
     *
     *              The class that implements the test function in JavaScript.
     *      }
     */
    static function getTests(): array {
        $tests = [];
        $classNames = CBAdmin::fetchClassNames();

        foreach ($classNames as $className) {
            $tests = array_merge(
                $tests,
                CBTest::getTests_classNameToTests($className),

                /* deprecated */
                CBTest::getTests_classNameToJavaScriptTests($className)
            );
        }

        return $tests;
    }
    /* getTests() */


    /**
     * @deprecated 2019_05_25
     *
     *      Use CBTest_getTests() instead of CBTest_JavaScriptTests().
     *
     * @param string $className
     *
     * @return [object]
     */
    static function getTests_classNameToJavaScriptTests(
        string $className
    ): array {
        $tests = [];
        $functionName = "{$className}::CBTest_JavaScriptTests";

        if (is_callable($functionName)) {
            $values = call_user_func($functionName);

            if (!is_array($values)) {
                throw CBException::createModelIssueException(
                    'The function '
                    . $functionName
                    . '() should return an array.',
                    $values,
                    'b090aa3351dc247954ed7a8b82ae0ed08678d179'
                );
            }

            $tests = array_map(
                function ($value) use ($className) {
                    return (object)[
                        'title' => "{$value[0]}Tests / {$value[1]} (OSJS)",
                        'testClassName' => "{$value[0]}Tests",
                        'name' => $value[1],
                    ];
                },
                $values
            );
        }

        return $tests;
    }
    /* getTests_classNameToJavaScriptTests() */


    /**
     * @param string $className
     *
     * @return [object]
     */
    static function getTests_classNameToTests(
        string $className
    ): array {
        $tests = [];
        $functionName = "{$className}::CBTest_getTests";

        if (is_callable($functionName)) {
            $tests = call_user_func($functionName);

            if (!is_array($tests)) {
                throw CBException::createModelIssueException(
                    'The function '
                    . $functionName
                    . '() should return an array.',
                    $tests,
                    '2f124f63ff0a25662415c894d2eb9f742a74f5c3'
                );
            }

            for ($index = 0; $index < count($tests); $index += 1) {
                $test = $tests[$index];

                if (!is_object($test)) {
                    throw CBException::createModelIssueException(
                        'The array of tests returned by '
                        . $functionName
                        . '() has a non-object value at index '
                        . $index,
                        $tests,
                        'a955214c24c7cb1edbb1dfae513220fb63382f1a'
                    );
                }

                if (CBModel::valueToString($test, 'testClassName') === '') {
                    $test->testClassName = $className;
                }
            }
            /* for */
        }

        return $tests;
    }
    /* getTests_classNameToTests() */


    /**
     * @return [[<className>, <testName>]]
     */
    static function PHPTests(): array {
        $tests = [
            ['CB',                      'class'],
            ['CBConvert',               'linesToParagraphs'],
            ['CBConvert',               'textToLines'],
            ['CBConvert',               'valueAsInt'],
            ['CBConvert',               'valueAsModel'],
            ['CBDataStore',             'createAndDelete'],
            ['CBDataStore',             'directoryNameFromDocumentRoot'],
            ['CBDataStore',             'toURL'],
            ['CBDataStore',             'URIToID'],
            ['CBDB',                    'hex160ToSQL'],
            ['CBDB',                    'optional'],
            ['CBDB',                    'SQLToArray'],
            ['CBDB',                    'SQLToAssociativeArray'],
            ['CBDB',                    'SQLToValue'],
            ['CBImage',                 'upgrade'],
            ['CBImages',                'resize'],
            ['CBMarkaround',            'paragraphToHTML'],
            ['CBPageLayout',            'build'],
            ['CBPages',                 'stringToDencodedURIPath'],
            ['CBProjection'],
            ['CBSitePreferences'],
            ['CBTestPage'],
            ['CBView',                  'render'],
            ['CBView',                  'toSubviews'],
            ['CBViewPage',              'save'],
            ['ColbyMarkaroundParser',   'orderedList'],
            ['ColbyMarkaroundParser',   'paragraph'],
            ['ColbyMarkaroundParser',   'unorderedList'],
        ];

        $classNames = CBAdmin::fetchClassNames();

        foreach ($classNames as $className) {
            if (
                is_callable($function = "{$className}::CBTest_PHPTests")
            ) {
                $tests = array_merge(
                    $tests,
                    call_user_func($function)
                );
            }

            else if (
                is_callable($function = "{$className}::CBUnitTests_tests")
            ) {
                $tests = array_merge(
                    $tests,
                    call_user_func($function)
                );
            }
        }

        return $tests;
    }


    /**
     * @param string $testTitle
     *
     *      The test function name and class name will be added by the test
     *      framework so this title only needs to indicate which subtest in the
     *      test function  had the issue.
     *
     *      Examples:
     *
     *      "Test 1"
     *      "Test 2"
     *      "String Test"
     *
     * @param mixed $actualResult
     * @param mixed $expectedResult
     *
     * @return object
     */
    static function resultMismatchFailure(
        string $testTitle,
        $actualResult,
        $expectedResult
    ) {
        $testTitleAsMessage = CBMessageMarkup::stringToMessage($testTitle);

        $actualResultAsJSONAsMessage = CBMessageMarkup::stringToMarkup(
            CBConvert::valueToPrettyJSON($actualResult)
        );

        $expectedResultAsJSONAsMessage = CBMessageMarkup::stringToMarkup(
            CBConvert::valueToPrettyJSON($expectedResult)
        );

        $message = <<<EOT

            (test title (strong))

            {$testTitleAsMessage}

            (actual result (strong))

            --- pre\n{$actualResultAsJSONAsMessage}
            ---

            (expected result (strong))

            --- pre\n{$expectedResultAsJSONAsMessage}
            ---

EOT;

        return (object)[
            'succeeded' => false,
            'message' => $message,
        ];
    }

    /**
     * @param string $testTitle
     * @param string $actualResult 1
     * @param string $expectedResult 2
     *
     * @return object
     */
    static function resultMismatchFailureDiff(
        string $testTitle,
        string $actualResult,
        string $expectedResult
    ): stdClass {
        $actualResultLines = CBConvert::stringToLines($actualResult);
        $actualResultLinesCount = count($actualResultLines);
        $expectedResultLines = CBConvert::stringToLines($expectedResult);
        $expectedResultLinesCount = count($expectedResultLines);


        for ($index = 0; $index < $actualResultLinesCount; $index += 1) {
            if (!isset($expectedResultLines[$index])) {
                $message = <<<EOT

                    {$testTitle}

                    Line {$index} of the actual result doesn't exist in the
                    expected result.

                    ({$actualLine} (code))

EOT;

                return (object)[
                    'message' => $message,
                    'sourceID' => '08437677d6da8acc68b06c9a4418f7fbd4ffeb76',
                ];
            }

            $actualLine = $actualResultLines[$index];
            $expectedLine = $expectedResultLines[$index];

            if ($actualLine !== $expectedLine) {
                $actualLineAsMessage = CBMessageMarkup::stringToMessage(
                    $actualLine
                );

                $expectedLineAsMessage = CBMessageMarkup::stringToMessage(
                    $expectedLine
                );

                $message = <<<EOT

                    {$testTitle}

                    Line {$index} of the actual result is:

                    ({$actualLineAsMessage} (code))

                    which does't match line {$index} of the expected result:

                    ({$expectedLineAsMessage} (code))

EOT;

                return (object)[
                    'message' => $message,
                    'sourceID' => '696cd3366b14ad7ac12fd4604a9c8fb41824cb96',
                ];
            }
        }

        if ($actualResultLinesCount != $expectedResultLinesCount) {
            $message = <<<EOT

                {$testTitle}

                The expected result has more lines than the actual result.

EOT;

            return (object)[
                'message' => $message,
                'sourceID' => '2c8897739fcfa9df391fc698894e4a7af566a9a6',
            ];
        }

        $message = <<<EOT

            {$testTitle}

            No difference was found between the actual and expected result. This
            function should only be called if there is a difference.

EOT;

        return (object)[
            'message' => $message,
            'sourceID' => '3098da7e4559278488a42ad81fbaef5fe0a7575e',
        ];
    }

    /**
     * @param string testTitle
     * @param mixed value
     * @param string message
     *
     * @return object
     */
    static function valueIssueFailure(
        string $testTitle,
        $value,
        string $issueMessage
    ): stdClass {
        $testTitleAsMessage = CBMessageMarkup::stringToMessage(
            $testTitle
        );

        $valueAsJSONAsMessage = CBMessageMarkup::stringToMessage(
            CBConvert::valueToPrettyJSON($value)
        );

        $message = <<<EOT

            --- dl
                --- dt
                    test title
                ---
                --- dd
                    ${testTitleAsMessage}
                ---
                --- dt
                    issue
                ---
                --- dd
                    ${issueMessage}
                ---
                --- dt
                    value
                ---
                --- dd
                    --- pre\n${valueAsJSONAsMessage}
                    ---
                ---
            ---

EOT;

        return (object)[
            'succeeded' => false,
            'message' => $message,
        ];
    }
}
