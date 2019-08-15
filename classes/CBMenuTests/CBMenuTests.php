<?php

final class CBMenuTests {

    static function CBTests_classTest() {
        $spec = (object)[
            'className' => 'CBMenu',
            'items' => [
                (object)[
                    'className' => 'CBTestView',
                    'value' => 42,
                ],
            ],
        ];

        $expectedModel = (object)[
            'className' => 'CBMenu',
            'title' => '',
            'titleURI' => '',
            'items' => [
                (object)[
                    'className' => 'CBTestView',
                    'value' => 42,
                ],
            ],
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
        $expectedSearchText = 'CBMenu';

        if ($searchText !== $expectedSearchText) {
            return (object)[
                'message' =>
                    "The result search text does not match the expected search text.\n\n" .
                    CBConvertTests::resultAndExpectedToMessage($searchText, $expectedSearchText),
            ];
        }

        $upgradedSpec = CBModel::upgrade($spec);
        $expectedUpgradedSpec = (object)[
            'className' => 'CBMenu',
            'items' => [
                (object)[
                    'className' => 'CBTestView',
                    'value' => 42,
                ],
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
