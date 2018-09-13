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

        CBLog::bufferStart();

        $actual = CBModel::upgrade($original);
        $entries = CBLog::bufferContents();

        CBLog::bufferEndClean();

        if ($actual != $expected) {
            return CBTest::resultMismatchFailure('upgrade', $actual, $expected);
        }

        /* log entry count */

        $actual = count($entries);
        $expected = 1;

        if ($actual !== $expected) {
            return CBTest::resultMismatchFailure(
                'log entry count',
                $actual,
                $expected
            );
        }

        /* log entry source ID */

        $actual = CBModel::valueAsID($entries[0], 'sourceID');
        $expected = '7a17c8d3bdc1a02f3930f873ddd7df8f78ae3bfd';

        if ($actual !== $expected) {
            return CBTest::resultMismatchFailure(
                'log entry source ID',
                $actual,
                $expected
            );
        }

        return (object)[
            'succeeded' => true,
        ];
    }
}
