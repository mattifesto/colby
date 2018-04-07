<?php

final class CBPageFramesTests {

    /**
     * @return object
     */
    static function CBTest_test(): stdClass {
        CBPageFrames::$testID = '961cf3538a564259b47db8658f5f507d92f63e52';

        CBPageFrames::CBInstall_install();
        CBPageFrames::installFrame('CBPageFramesTests_frame1');
        CBPageFrames::installFrame('CBPageFramesTests_frame2');
        CBPageFrames::installFrame('CBPageFramesTests_fake');
        CBPageFrames::installFrame('CBPageFramesTests_frame1');
        CBPageFrames::installFrame('CBPageFramesTests_frame2');

        $frameClassNames = CBPageFrames::fetchFrameClassNames();
        $expectedClassNames = [
            'CBPageFramesTests_frame1',
            'CBPageFramesTests_frame2',
        ];

        if ($frameClassNames != $expectedClassNames) {
            return (object)[
                'message' =>
                    "The frame class names do not match the expected class names\n\n" .
                    CBConvertTests::resultAndExpectedToMessage($frameClassNames, $expectedClassNames),
            ];
        }

        CBModels::deleteByID(CBPageFrames::$testID);

        CBPageFrames::$testID = null;

        return (object)[
            'succeeded' => true,
        ];
    }


    /**
     * @return [[<class>, <name>]]
     */
    static function CBUnitTests_tests(): array {
        return [
            ['CBPageFrames', 'test']
        ];
    }
}

final class CBPageFramesTests_frame1 {
    static function CBPageFrame_render(callable $renderContent): void {
        echo __CLASS__ . 'begin';
        $renderContent();
        echo __CLASS__ . 'end';
    }
}

final class CBPageFramesTests_frame2 {
    static function CBPageFrame_render(callable $renderContent): void {
        echo __CLASS__ . 'begin';
        $renderContent();
        echo __CLASS__ . 'end';
    }
}
