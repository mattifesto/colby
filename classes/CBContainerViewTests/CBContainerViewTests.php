<?php

final class CBContainerViewTests {

    static function CBTests_classTest() {
        $spec = (object)[
            'className' => 'CBContainerView',
            'subviews' => CBViewTests::testSubviewSpecs(),
            'smallImage' => (object)[
                'className' => 'CBImage',
                'ID' => 'bf0c7e133bf1a4bd05a3490a6c05d8fa34f5833f',
                'filename' => 'original',
                'extension' => 'jpeg',
                'height' => 600,
                'width' => 800,
            ],
            'mediumImage' => (object)[
                /* testing deprecated missing class name */
                'ID' => 'bf0c7e133bf1a4bd05a3490a6c05d8fa34f5833f',
                'filename' => 'original',
                'extension' => 'jpeg',
                'height' => 700,
                'width' => 900,
            ],
            'largeImage' => (object)[
                /* testing deprecated missing class name */
                'ID' => 'bf0c7e133bf1a4bd05a3490a6c05d8fa34f5833f',
                'filename' => 'original',
                'extension' => 'jpeg',
                'height' => 800,
                'width' => 1000,
            ],
        ];

        $expectedModel = (object)[
            'className' => 'CBContainerView',
            'backgroundColor' => null,
            'CSSClassNames' => [],
            'HREF' =>'',
            'HREFAsHTML' => '',
            'smallImage' => (object)[
                'className' => 'CBImage',
                'ID' => 'bf0c7e133bf1a4bd05a3490a6c05d8fa34f5833f',
                'filename' => 'original',
                'extension' => 'jpeg',
                'height' => 600,
                'width' => 800,
            ],
            'subviews' => CBViewTests::testSubviewModels(),
        ];

        $model = CBModel::build($spec);

        if ($model != $expectedModel) {
            return (object)[
                'message' =>
                    "The result built model does not match the expected built model.\n\n" .
                    CBConvertTests::resultAndExpectedToMessage($model, $expectedModel),
            ];
        }

        $searchText = CBModel::toSearchText($model);
        $expectedSearchText = CBViewTests::testSubviewSearchText() . ' CBContainerView';

        if ($searchText !== $expectedSearchText) {
            return (object)[
                'message' =>
                    "The result search text does not match the expected search text.\n\n" .
                    CBConvertTests::resultAndExpectedToMessage($searchText, $expectedSearchText),
            ];
        }

        $upgradedSpec = CBModel::upgrade($spec);
        $expectedUpgradedSpec = (object)[
            'className' => 'CBContainerView',
            'subviews' => CBViewTests::testSubviewUpgradedSpecs(),
            'smallImage' => (object)[
                'className' => 'CBImage',
                'ID' => 'bf0c7e133bf1a4bd05a3490a6c05d8fa34f5833f',
                'filename' => 'original',
                'extension' => 'jpeg',
                'height' => 600,
                'width' => 800,
            ],
            'mediumImage' => (object)[
                'className' => 'CBImage',
                'ID' => 'bf0c7e133bf1a4bd05a3490a6c05d8fa34f5833f',
                'filename' => 'original',
                'extension' => 'jpeg',
                'height' => 700,
                'width' => 900,
            ],
            'largeImage' => (object)[
                'className' => 'CBImage',
                'ID' => 'bf0c7e133bf1a4bd05a3490a6c05d8fa34f5833f',
                'filename' => 'original',
                'extension' => 'jpeg',
                'height' => 800,
                'width' => 1000,
            ],
        ];

        if ($upgradedSpec != $expectedUpgradedSpec) {
            return (object)[
                'message' =>
                    "The result upgraded spec does not match the expected upgraded spec.\n\n" .
                    CBConvertTests::resultAndExpectedToMessage($upgradedSpec, $expectedUpgradedSpec),
            ];
        }
    }
}
