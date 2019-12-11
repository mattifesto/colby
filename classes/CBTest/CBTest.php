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
            $className = CBModel::valueToString(
                $args,
                'className'
            );

            $testName = CBModel::valueToString(
                $args,
                'testName'
            );

            CBTest::checkForUnsupportedTestImplementation(
                $className,
                $testName
            );

            if (
                is_callable(
                    $function = "{$className}::CBTest_{$testName}"
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
     * @param $className
     * @param $testName
     *
     * @return void
     */
    static function checkForUnsupportedTestImplementation(
        string $className,
        string $testName
    ): void {
        $functionName = "{$className}Tests::CBTest_{$testName}";

        if (is_callable($functionName)) {
            throw new CBExceptionWithValue(
                'This test interface implementation is no longer supported.',
                $functionName . '()',
                '1b9c4378e95cba772465e2295379ce6a83b2ebdb'
            );
        }

        $functionName = "{$className}Tests::{$testName}Test";

        if (is_callable($functionName)) {
            throw new CBExceptionWithValue(
                'This test interface implementation is no longer supported.',
                $functionName . '()',
                '259d228d6326f55d67e8848d5b88c0d46ee3fb25'
            );
        }
    }
    /* checkForUnsupportedTestImplementation() */



    /**
     * @return [object]
     *
     *      {
     *          description: string
     *
     *          name: string
     *
     *              The name of the test. The function that will be called to
     *              perform the test will be CBTest_<name>().
     *
     *          testClassName: string
     *
     *              The class that implements the test function in JavaScript.
     *              If not specified this will be the class name that
     *              implemented the CBTest_getTests() interface.
     *
     *          title: string
     *
     *          type: string
     *
     *              The default is to look for a client side test, specify the
     *              string 'server' for a server test.
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

        $tests = array_merge(
            $tests,
            CBTest::PHPTests()
        );

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
                throw new CBExceptionWithValue(
                    (
                        'The function ' .
                        $functionName .
                        '() should return an array.'
                    ),
                    $tests,
                    '2f124f63ff0a25662415c894d2eb9f742a74f5c3'
                );
            }

            for ($index = 0; $index < count($tests); $index += 1) {
                $test = $tests[$index];

                if (!is_object($test)) {
                    throw new CBExceptionWithValue(
                        (
                            'The array of tests returned by ' .
                            $functionName .
                            '() has a non-object value at index ' .
                            $index
                        ),
                        $tests,
                        'a955214c24c7cb1edbb1dfae513220fb63382f1a'
                    );
                }

                $testClassName = CBModel::valueToString(
                    $test,
                    'testClassName'
                );

                if ($testClassName === '') {
                    $test->testClassName = $className;
                }

                $name = CBModel::valueAsName(
                    $test,
                    'name'
                );

                if ($name === '') {
                    throw new CBExceptionWithValue(
                        'The "name" property value for this test is invalid.',
                        $test,
                        'b328b5deb67a04e2cef7c1a3aa3ef96cadf57dc3'
                    );
                }

                $title = CBModel::valueToString(
                    $test,
                    'title'
                );

                if ($title === '') {
                    $type = CBModel::valueToString(
                        $test,
                        'type'
                    );

                    if (empty($type)) {
                        $type = 'client';
                    }

                    $test->title = "{$test->testClassName} | {$name} | {$type}";
                }
            }
            /* for */
        }

        return $tests;
    }
    /* getTests_classNameToTests() */



    /**
     * @deprecated 2019_11_03
     *
     *      Implement CBTest_getTests() instead of CBTest_PHPTests().
     *
     * @return [object]
     */
    static function PHPTests(): array {
        $tests = [
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
            ['CBMarkaround',            'paragraphToHTML'],
            ['CBPageLayout',            'build'],
            ['CBPages',                 'stringToDencodedURIPath'],
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
        }

        $tests = array_map(
            function (array $test): stdClass {
                if (count($test) < 2) {
                    throw CBException::createModelIssueException(
                        'Invalid PHP test specification.',
                        $test
                    );
                }

                $testClassName = $test[0];
                $testName = $test[1];

                return (object)[
                    'name' => $testName,
                    'testClassName' => $testClassName,
                    'title' => "{$testClassName} => {$testName}",
                    'type' => 'server',
                ];
            },
            $tests
        );

        return $tests;
    }
    /* PHPTests() */



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
    /* resultMismatchFailure() */



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
    /* resultMismatchFailureDiff() */



    /**
     * @param string $testTitle
     * @param mixed $value
     * @param string $issueCBMessage
     *
     * @return object
     */
    static function valueIssueFailure(
        string $testTitle,
        $value,
        string $issueCBMessage
    ): stdClass {
        $testTitleAsMessage = CBMessageMarkup::stringToMessage(
            $testTitle
        );

        $valueAsJSONAsMessage = CBMessageMarkup::stringToMessage(
            CBConvert::valueToPrettyJSON($value)
        );

        $cbmessage = <<<EOT

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
                    ${issueCBMessage}
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
            'message' => $cbmessage,
        ];
    }
    /* valueIssueFailure() */

}
