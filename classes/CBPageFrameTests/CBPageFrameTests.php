<?php

final class CBPageFrameTests {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'test',
                'title' => 'CBPageFrame',
                'type' => 'server',
            ],
        ];
    }



    /* -- tests -- -- -- -- -- */



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
            return CBTest::resultMismatchFailure(
                'test 1',
                $result,
                $expected
            );
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_test() */

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
