<?php

final class CBTest {

    /* -- CBAjax interfaces -- -- -- -- -- */

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

            if (is_callable($function = "{$className}Tests::CBTest_{$testName}")) {
                $result = call_user_func($function);

                if (!is_object($result)) {
                    $resultAsJSON = CBConvert::valueToPrettyJSON($result);
                    $functionAsMarkup = CBMessageMarkup::stringToMarkup($function);
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
            } else if (is_callable($function = "{$className}Tests::{$testName}Test")) {
                /* deprecated */
                $result = call_user_func($function);
                $result = (object)[
                    'succeeded' => empty($result->failed),
                    'message' => CBModel::valueToString($result, 'message'),
                ];
            } else if ($testName === '' && is_callable($function = "{$className}Tests::test")) {
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

    /**
     * @return string
     */
    static function CBAjax_run_group(): string {
        return 'Developers';
    }

    /* -- CBHTMLOutput interfaces -- -- -- -- -- */

    /**
     * @return [string]
     */
    static function CBHTMLOutput_JavaScriptURLs(): array {
        return [
            Colby::flexpath(__CLASS__, 'v471.js', cbsysurl()),
        ];
    }

    /**
     * @return [string]
     */
    static function CBHTMLOutput_requiredClassNames(): array {
        return [
            'CBConvert',
            'CBMessageMarkup',
        ];
    }

    /* -- functions -- -- -- -- -- */

    /**
     * Sample JavaScript tests are provided in CBTestTests.php and
     * CBTestTests.js.
     *
     * @return [object]
     *
     *      {
     *          type: "JavaScript",
     *          testClassName: string
     *          testName: string
     *      }
     */
    static function JavaScriptTests(): array {
        $allTests = [];
        $classNames = CBAdmin::fetchClassNames();

        foreach ($classNames as $className) {
            if (is_callable($function = "{$className}::CBTest_JavaScriptTests")) {
                $tests = call_user_func($function);
                $tests = array_map(
                    function ($value) use ($className) {
                        if (is_string($value)) {
                            return (object)[
                                'type' => 'JavaScript',
                                'testClassName' => $className,
                                'testName' => $value,
                            ];
                        } else {
                            return (object)[
                                'type' => 'JavaScript',
                                'testClassName' => $value[0],
                                'testName' => $value[1],
                            ];
                        }
                    },
                    $tests
                );

                $allTests = array_merge($allTests, $tests);
            }
        }

        return $allTests;
    }

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
            ['CBModels',                'fetchModelByID'],
            ['CBModels',                'fetchModelsByID'],
            ['CBModels',                'saveNullableModel'],
            ['CBModels',                'saveSpecWithoutID'],
            ['CBPageLayout',            'CBModel_toModel'],
            ['CBPages',                 'stringToDencodedURIPath'],
            ['CBProjection'],
            ['CBSitePreferences'],
            ['CBTestPage'],
            ['CBView',                  'render'],
            ['CBView',                  'toSubviews'],
            ['CBViewPage',              'save'],
            ['Colby',                   'encryption'],
            ['ColbyMarkaroundParser',   'orderedList'],
            ['ColbyMarkaroundParser',   'paragraph'],
            ['ColbyMarkaroundParser',   'unorderedList'],
        ];

        $classNames = CBAdmin::fetchClassNames();

        foreach ($classNames as $className) {
            if (is_callable($function = "{$className}::CBTest_PHPTests")) {
                $tests = array_merge($tests, call_user_func($function));
            } else if (is_callable($function = "{$className}::CBUnitTests_tests")) {
                $tests = array_merge($tests, call_user_func($function));
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
    static function resultMismatchFailure(string $testTitle, $actualResult, $expectedResult) {
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
    static function resultMismatchFailureDiff(string $testTitle, string $actualResult, string $expectedResult): stdClass {
        $actualResultLines = CBConvert::stringToLines($actualResult);
        $actualResultLinesCount = count($actualResultLines);
        $expectedResultLines = CBConvert::stringToLines($expectedResult);
        $expectedResultLinesCount = count($expectedResultLines);


        for ($index = 0; $index < $actualResultLinesCount; $index += 1) {
            if (!isset($expectedResultLines[$index])) {
                $message = <<<EOT

                    {$testTitle}

                    Line {$index} of the actual result doesn't exist in the expected result.

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
