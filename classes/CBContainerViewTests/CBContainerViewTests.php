<?php

final class CBContainerViewTests {

    /**
     * @return void
     */
    static function upgradeTest(): void {
        $originalSpec = (object)[
            'className' => 'CBContainerView',
            'mediumImage' => (object)[
                'ID' => 'bf0c7e133bf1a4bd05a3490a6c05d8fa34f5833f',
                'base' => 'original',
                'extension' => 'jpeg',
                'height' => 600,
                'width' => 800,
            ],
            'subviews' => [

                (object)[
                    'className' => 'CBNotAView',
                ],

                2,

                (object)[
                    'className' => 'CBContainerView',
                    'largeImage' => (object)[
                        'ID' => 'bf0c7e133bf1a4bd05a3490a6c05d8fa34f5833f',
                        'base' => 'original',
                        'extension' => 'jpeg',
                        'height' => 600,
                        'width' => 800,
                    ],
                ],
            ],
        ];

        $expectedSpec = (object)[
            'className' => 'CBContainerView',
            'mediumImage' => (object)[
                'className' => 'CBImage',
                'ID' => 'bf0c7e133bf1a4bd05a3490a6c05d8fa34f5833f',
                'filename' => 'original',
                'extension' => 'jpeg',
                'height' => 600,
                'width' => 800,
            ],
            'subviews' => [

                (object)[
                    'className' => 'CBNotAView',
                ],

                (object)[
                    'className' => 'CBContainerView',
                    'largeImage' => (object)[
                        'className' => 'CBImage',
                        'ID' => 'bf0c7e133bf1a4bd05a3490a6c05d8fa34f5833f',
                        'filename' => 'original',
                        'extension' => 'jpeg',
                        'height' => 600,
                        'width' => 800,
                    ],
                    'subviews' => [],
                ],
            ],
        ];

        $upgradedSpec = CBModel::upgrade($originalSpec);

        if ($upgradedSpec != $expectedSpec) {
            $expectedJSON = CBConvert::valueToPrettyJSON($expectedSpec);
            $upgradedJSON = CBConvert::valueToPrettyJSON($upgradedSpec);
            $message = <<<EOT

                CBContainerViewTests::upgradeTest() failed

                Upgraded:

                --- pre\n{$upgradedJSON}
                ---

                Expected:

                --- pre\n{$expectedJSON}
                ---

EOT;

            CBLog::log((object)[
                'className' => __CLASS__,
                'message' => $message,
                'severity' => 3,
            ]);

            throw new Exception("The upgraded CBContainerView spec does not match what was expected. See log entry for details.");
        }
    }
}
