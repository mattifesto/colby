<?php

final class CBFlexBoxViewTests {

    /**
     * @return [[<class>, <function>]]
     */
    static function CBUnitTests_tests(): array {
        return [
            ['CBFlexBoxView', 'upgrade'],
        ];
    }

    /**
     * @return object
     */
    static function CBTest_upgrade(): stdClass {
        $original = (object)[
            'className' => 'CBFlexBoxView',
            'flexWrap' => 'wrap',
            'subviews' => [
                (object)[
                    'className' => 'CBMessageView',
                    'markup' => 'Hello 1',
                ],
                (object)[
                    'className' => 'CBMessageView',
                    'markup' => 'Hello 2',
                ],
                (object)[
                    'className' => 'CBMessageView',
                    'markup' => 'Hello 3',
                ],
            ],
        ];

        $expected = (object)[
            'className' => 'CBContainerView2',
            'CSSClassNames' => 'flow',
            'subviews' => [
                (object)[
                    'className' => 'CBMessageView',
                    'markup' => 'Hello 1',
                ],
                (object)[
                    'className' => 'CBMessageView',
                    'markup' => 'Hello 2',
                ],
                (object)[
                    'className' => 'CBMessageView',
                    'markup' => 'Hello 3',
                ],
            ],
        ];

        $result = CBModel::upgrade($original);

        if ($result != $expected) {
            $message = CBConvertTests::resultAndExpectedToMessage($result, $expected);

            return (object)[
                'message' => <<<EOT

                    The result upgraded spec does not match the expected upgrade spec:

                    {$message}

EOT
            ];
        }

        return (object)[
            'succeeded' => true,
        ];
    }
}
