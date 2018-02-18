<?php

final class CBLinkView1Tests {

    static function CBTests_classTest() {
        $spec = (object)[
            'className' => 'CBLinkView1',
            'image' => (object)[
                /* testing deprecated missing class name */
                'ID' => 'bf0c7e133bf1a4bd05a3490a6c05d8fa34f5833f',
                'base' => 'original',
                'extension' => 'jpeg',
                'height' => 700,
                'width' => 900,
            ],
        ];

        $expectedModel = (object)[
            'className' => 'CBLinkView1',
            'description' => '',
            'size' => '',
            'URL' => '',
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
        $expectedSearchText = 'CBLinkView1';

        if ($searchText !== $expectedSearchText) {
            return (object)[
                'message' =>
                    "The result search text does not match the expected search text.\n\n" .
                    CBConvertTests::resultAndExpectedToMessage($searchText, $expectedSearchText),
            ];
        }

        $upgradedSpec = CBModel::upgrade($spec);
        $expectedUpgradedSpec = (object)[
            'className' => 'CBLinkView1',
            'image' => (object)[
                'className' => 'CBImage',
                'ID' => 'bf0c7e133bf1a4bd05a3490a6c05d8fa34f5833f',
                'filename' => 'original',
                'extension' => 'jpeg',
                'height' => 700,
                'width' => 900,
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
