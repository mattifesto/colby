<?php

final class CBPageFrameTests {

    /**
     * @return object
     */
    static function CBTest_test(): stdClass {
        $renderContent = function () {
            echo ' content ';
        };

        ob_start();

        CBPageFrame::render('CBPageFrameTests_frame', $renderContent);

        $result = ob_get_clean();
        $expected = 'framebegin content frameend';

        if ($result != $expected) {
            return (object)[
                'message' =>
                    "The rendered frame output was not what was expected.\n\n" .
                    CBConvertTests::resultAndExpectedToMessage($result, $expected),
            ];
        }

        return (object)[
            'succeeded' => true,
        ];
    }

    /**
     * @return [[<class>, <function>]]
     */
    static function CBUnitTests_tests(): array {
        return [
            ['CBPageFrame', 'test'],
        ];
    }
}

final class CBPageFrameTests_frame {

    /**
     * @param callable $renderContent
     *
     * @return void
     */
    static function CBPageFrame_render(callable $renderContent): void {
        echo 'framebegin';
        $renderContent();
        echo 'frameend';
    }
}
