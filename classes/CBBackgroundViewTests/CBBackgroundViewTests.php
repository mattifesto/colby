<?php

final class CBBackgroundViewTests {

    static function CBTests_classTest() {
        $spec = (object)[
            'className' => 'CBBackgroundView',
            'children' => CBViewTests::testSubviewSpecs(),
        ];

        $expectedModel = (object)[
            'className' => 'CBBackgroundView',
            'color' => "",
            'imageHeight' => null,
            'imageWidth' => null,
            'imageURL' => "",
            'imageShouldRepeatHorizontally' => false,
            'imageShouldRepeatVertically' => false,
            'minimumViewHeightIsImageHeight' => false,
            'children' => CBViewTests::testSubviewModels(),
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
        $expectedSearchText = CBViewTests::testSubviewSearchText() . ' CBBackgroundView';

        if ($searchText !== $expectedSearchText) {
            return (object)[
                'message' =>
                    "The result search text does not match the expected search text.\n\n" .
                    CBConvertTests::resultAndExpectedToMessage($searchText, $expectedSearchText),
            ];
        }

        $upgradedSpec = CBModel::upgrade($spec);
        $expectedUpgradedSpec = (object)[
            'className' => 'CBBackgroundView',
            'children' => CBViewTests::testSubviewUpgradedSpecs(),
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
