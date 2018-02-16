<?php

final class CBTests {

    /**
     * This test calls the interface CBTests_classTest() on every class
     * that implements it. This interface should perform simple class specific
     * functionaly tests such as making sure that model building or view
     * rendering or other bits of class functionality are working correctly in
     * ways that are specific to the class.
     *
     * CBTests_classTest() is meant to perform relatively simple tests and runs
     * the test for every single class in one request. A complex class with more
     * extended and time consuming test requirements should implement and
     * register its own test so it can use the entire request time.
     */
    static function classTest() {
        $classNames = CBAdmin::fetchClassNames();

        foreach ($classNames as $className) {
            if (is_callable($function = "{$className}::CBTests_classTest")) {
                $value = call_user_func($function);

                if ($value !== null) {
                    $message = 'The test (' .
                        CBMessageMarkup::stringToMarkup($function) .
                        '\(\) (code)) failed.' .
                        "\n\n" .
                        CBModel::valueToString($value, 'message');

                    return (object)[
                        'failed' => true,
                        'message' => $message,
                    ];
                }
            }
        }
    }
}
