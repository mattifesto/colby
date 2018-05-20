<?php

final class CBTest {

    /**
     * How to create a test for functionality in the class MyClass.
     *
     * 1. Create a class named MyClassTests
     *
     *      final class MyClassTests {
     *          ...
     *      }
     *
     * 2. Create a function named CBTest_myTest() which will perform the tests
     *
     *      static function CBTest_myTest(): stdClass {
     *          ...
     *      }
     *
     * 3. Make CBTest_myTest() return an object after performing the tests
     *
     *      return (object)[
     *          'succeeded' => false,
     *          'message' => 'This is what went wrong.',
     *      ];
     *
     *      This object returned by the test function will be passed without
     *      modification to the Ajax caller, so be mindful of any other property
     *      values you set.
     *
     * 4. Implement CBUnitTests_tests() to register all of the test functions in
     *    MyClassTests
     *
     *      static function CBUnitTests_tests(): array {
     *          return [
     *              ['MyClass', 'myTest'],
     *              ['MyClass', 'myOtherTest'],
     *          ];
     *      }
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
