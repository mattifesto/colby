<?php

final class CBArtworkView_Tests {

    /* -- CBTest interfaces -- -- -- -- -- */

    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'build',
                'title' => 'CBArtworkView build()',
                'type' => 'server',
            ],
        ];
    }



    /* -- tests -- -- -- -- -- */

    static function CBTest_build(): stdClass {
        $cases = [
            (object)[
                'spec' => (object)[
                    'className' => 'CBArtworkView',
                    'captionAsMarkdown' => 'a',
                ],
                'expectedCaptionAsCBMessage' => '',
                'expectedCaptionAsMarkdown' => 'a',
            ],
            (object)[
                'spec' => (object)[
                    'className' => 'CBArtworkView',
                    'captionAsCBMessage' => 'b',
                    'captionAsMarkdown' => 'a',
                ],
                'expectedCaptionAsCBMessage' => '',
                'expectedCaptionAsMarkdown' => 'a',
            ],
            (object)[
                'spec' => (object)[
                    'className' => 'CBArtworkView',
                    'captionAsCBMessage' => 'b',
                    'captionAsMarkdown' => '   ',
                ],
                'expectedCaptionAsCBMessage' => 'b',
                'expectedCaptionAsMarkdown' => '',
            ],
        ];

        $index = 0;

        foreach ($cases as $case) {
            $model = CBModel::build($case->spec);

            $captionAsMarkdown = CBModel::valueToString(
                $model,
                'captionAsMarkdown'
            );

            if ($captionAsMarkdown !== $case->expectedCaptionAsMarkdown) {
                return CBTest::resultMismatchFailure(
                    "Markdown for test at index {$index}",
                    $captionAsMarkdown,
                    $case->expectedCaptionAsMarkdown
                );
            }

            $captionAsCBMessage = CBModel::valueToString(
                $model,
                'captionAsCBMessage'
            );

            if ($captionAsCBMessage !== $case->expectedCaptionAsCBMessage) {
                return CBTest::resultMismatchFailure(
                    "CBMessage for test at index {$index}",
                    $captionAsCBMessage,
                    $case->expectedCaptionAsCBMessage
                );
            }

            $index += 1;
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_build() */

}
