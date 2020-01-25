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
                'type' => 'server',
            ],
            (object)[
                'name' => 'toSearchText',
                'type' => 'server',
            ],
        ];
    }



    /* -- tests -- -- -- -- -- */



    /**
     * @return object
     */
    static function CBTest_build(): stdClass {
        $cases = [
            (object)[
                'spec' => (object)[
                    'className' => 'CBArtworkView',
                    'captionAsMarkdown' => '((br))',
                ],
                'expectedCaptionAsCBMessage' => '\\(\\(br\\)\\)',
            ],
            (object)[
                'spec' => (object)[
                    'className' => 'CBArtworkView',
                    'captionAsCBMessage' => 'b',
                    'captionAsMarkdown' => 'a',
                ],
                'expectedCaptionAsCBMessage' => 'b',
            ],
            (object)[
                'spec' => (object)[
                    'className' => 'CBArtworkView',
                    'captionAsCBMessage' => 'b',
                    'captionAsMarkdown' => '   ',
                ],
                'expectedCaptionAsCBMessage' => 'b',
            ],
        ];

        $index = 0;

        foreach ($cases as $case) {
            $model = CBModel::build($case->spec);

            $captionAsCBMessage = CBModel::valueToString(
                $model,
                'captionAsCBMessage'
            );

            if ($captionAsCBMessage !== $case->expectedCaptionAsCBMessage) {
                return CBTest::resultMismatchFailure(
                    CBConvert::stringToCleanLine(<<<EOT

                        test index {$index}: The "captionAsCBMessage" model
                        property value is not what was expected.

                    EOT),
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



    /**
     * @return object
     */
    static function CBTest_toSearchText(): stdClass {
        $cases = [
            (object)[
                'spec' => (object)[
                    'className' => 'CBArtworkView',
                    'alternativeText' => 'alt',
                    'captionAsMarkdown' => '((br))',
                ],
                'expectedSearchText' => 'alt ((br))',
            ],
            (object)[
                'spec' => (object)[
                    'className' => 'CBArtworkView',
                    'alternativeText' => 'alt',
                    'captionAsCBMessage' => 'b',
                    'captionAsMarkdown' => 'a',
                ],
                'expectedSearchText' => 'alt b',
            ],
            (object)[
                'spec' => (object)[
                    'className' => 'CBArtworkView',
                    'captionAsCBMessage' => 'b',
                    'captionAsMarkdown' => '   ',
                ],
                'expectedSearchText' => 'b b',
            ],
        ];

        $index = 0;

        foreach ($cases as $case) {
            $model = CBModel::build($case->spec);
            $searchText = CBArtworkView::CBModel_toSearchText($model);

            if ($searchText !== $case->expectedSearchText) {
                return CBTest::resultMismatchFailure(
                    CBConvert::stringToCleanLine(<<<EOT

                        test index {$index}

                    EOT),
                    $searchText,
                    $case->expectedSearchText
                );
            }

            $index += 1;
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_toSearchText() */

}
