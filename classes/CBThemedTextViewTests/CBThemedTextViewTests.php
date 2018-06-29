<?php

final class CBThemedTextViewTests {

    /**
     * @return object
     */
    static function CBTest_convertToCBMessageView(): stdClass {
        /* subtest 1 */

        $actualResult = CBThemedTextView::convertToCBMessageView((object)[
            'titleAsMarkaround' => 'Dogs (Awesome)',
        ]);

        $expectedResult = (object)[
            'className' => 'CBMessageView',
            'markup' => "--- h1\nDogs \\(Awesome\\)\n---",
        ];

        if ($actualResult != $expectedResult) {
            return CBTest::resultMismatchFailure('subtest 1', $actualResult, $expectedResult);
        }

        /* subtest 2 */

        $actualResult = CBThemedTextView::convertToCBMessageView((object)[
            'contentAsMarkaround' => 'They are (very) super-duper!',
        ]);

        $expectedResult = (object)[
            'className' => 'CBMessageView',
            'markup' => "They are \\(very\\) super\\-duper!",
        ];

        if ($actualResult != $expectedResult) {
            return CBTest::resultMismatchFailure('subtest 2', $actualResult, $expectedResult);
        }

        /* subtest 3 */

        $actualResult = CBThemedTextView::convertToCBMessageView((object)[
            'stylesTemplate' => 'view { text-align: center; }',
        ]);

        $expectedResult = (object)[
            'className' => 'CBMessageView',
            'CSSTemplate' => 'view { text-align: center; }',
        ];

        if ($actualResult != $expectedResult) {
            return CBTest::resultMismatchFailure('subtest 3', $actualResult, $expectedResult);
        }

        /* subtest 4 */

        $actualResult = CBThemedTextView::convertToCBMessageView((object)[
            'titleColor' => ' red ',
        ]);

        $expectedResult = (object)[
            'className' => 'CBMessageView',
            'CSSTemplate' => 'view > .content > h1:first-child { color: red }',
        ];

        if ($actualResult != $expectedResult) {
            return CBTest::resultMismatchFailure('subtest 4', $actualResult, $expectedResult);
        }

        /* subtest 5 */

        $actualResult = CBThemedTextView::convertToCBMessageView((object)[
            'contentColor' => ' gray ',
        ]);

        $expectedResult = (object)[
            'className' => 'CBMessageView',
            'CSSTemplate' => 'view > .content { color: gray }',
        ];

        if ($actualResult != $expectedResult) {
            return CBTest::resultMismatchFailure('subtest 5', $actualResult, $expectedResult);
        }

        /* subtest 6 */

        $actualResult = CBThemedTextView::convertToCBMessageView((object)[
            'center' => true,
        ]);

        $expectedResult = (object)[
            'className' => 'CBMessageView',
            'CSSClassNames' => 'center',
        ];

        if ($actualResult != $expectedResult) {
            return CBTest::resultMismatchFailure('subtest 6', $actualResult, $expectedResult);
        }

        /* subtest 7 */

        $actualResult = CBThemedTextView::convertToCBMessageView((object)[
            'URL' => ' http://disney-fun.com ',
        ]);

        $expectedResult = (object)[
            'className' => 'CBMessageView',
            'markup' => "--- center\n(http://disney\\-fun.com (a http://disney\\-fun.com))\n---",
        ];

        if ($actualResult != $expectedResult) {
            return CBTest::resultMismatchFailure('subtest 7', $actualResult, $expectedResult);
        }

        /* subtest 8 */

        $actualResult = CBThemedTextView::convertToCBMessageView((object)[
            'titleAsMarkaround' => 'Dogs (Awesome)',
            'contentAsMarkaround' => 'They are (very) super-duper!',
            'stylesTemplate' => 'view { text-align: center; }',
            'titleColor' => ' red ',
            'contentColor' => ' gray ',
            'center' => true,
            'URL' => ' http://disney-fun.com ',
        ]);

        $expectedResult = (object)[
            'className' => 'CBMessageView',
            'markup' => "--- h1\nDogs \\(Awesome\\)\n---\n\nThey are \\(very\\) super\\-duper!\n\n--- center\n(http://disney\\-fun.com (a http://disney\\-fun.com))\n---",
            'CSSClassNames' => 'center',
            'CSSTemplate' => "view { text-align: center; }\n\nview > .content > h1:first-child { color: red }\n\nview > .content { color: gray }",
        ];

        if ($actualResult != $expectedResult) {
            return CBTest::resultMismatchFailure('subtest 8', $actualResult, $expectedResult);
        }

        /* finished */

        return (object)[
            'succeeded' => true,
        ];
    }

    /**
     * @return [[<class>, <test>]]
     */
    static function CBUnitTests_tests(): array {
        return [
            ['CBThemedTextView', 'convertToCBMessageView'],
        ];
    }
}
