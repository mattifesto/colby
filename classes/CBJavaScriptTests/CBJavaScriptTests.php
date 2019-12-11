<?php

final class CBJavaScriptTests {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'stackToMessage',
                'type' => 'server',
            ],
        ];
    }
    /* CBTest_getTests() */



    /* -- tests -- -- -- -- -- */



    /**
     * @return object
     */
    static function CBTest_stackToMessage(): stdClass {
        $stack = implode(
            "\n",
            [
                'function1@https://example.com/foo/bar.js:15:30',
                'foobar',
            ]
        );

        $expectedResult = implode(
            "\n\n",
            [
                implode(
                    "((br))\n",
                    [
                        'function1\(\)',
                        'was running on line 15 of',
                        'bar.js',
                    ]
                ),
                'foobar',
            ]
        );

        $actualResult = CBJavaScript::stackToMessage($stack);

        if ($actualResult !== $expectedResult) {
            return CBTest::resultMismatchFailureDiff(
                'Test 1',
                $actualResult,
                $expectedResult
            );
        }

        return (object)[
            'succeeded' => true,
        ];
    }
}
