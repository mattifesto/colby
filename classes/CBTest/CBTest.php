<?php

final class CBTest {

    /**
     * To create a test create a class named:
     *
     *      MyClassTests
     *
     * and a function named:
     *
     *      CBTest_myTest()
     *
     * then implement CBUnitTests_tests() to register the test by returning:
     *
     *      [
     *          ['MyClass', 'myTest']
     *      ]
     *
     * The CBTest_myTest() function should return an object:
     *
     *      {
     *          succeeded: bool
     *          message: string
     *      }
     *
     * This object returned by the test function will be passed without
     * modification to the Ajax caller, so be mindful of any other property
     * values you set.
     *
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
            } else if ($testName = '' && is_callable($function = "{$className}Tests::test")) {
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
        }

        return $result;
    }

    /**
     * @return string
     */
    static function CBAjax_run_group(): string {
        return 'Developers';
    }
}
