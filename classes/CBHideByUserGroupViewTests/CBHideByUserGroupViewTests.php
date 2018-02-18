<?php

final class CBHideByUserGroupViewTests {

    static function CBTests_classTest() {
        $spec = (object)[
            'className' => 'CBHideByUserGroupView',
            'subviews' => CBViewTests::testSubviewSpecs(),
        ];

        $expectedModel = (object)[
            'className' => 'CBHideByUserGroupView',
            'hideFromMembers' => false,
            'hideFromNonmembers' => false,
            'groupName' => "",
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
        $expectedSearchText = CBViewTests::testSubviewSearchText() . ' CBHideByUserGroupView';

        if ($searchText !== $expectedSearchText) {
            return (object)[
                'message' =>
                    "The result search text does not match the expected search text.\n\n" .
                    CBConvertTests::resultAndExpectedToMessage($searchText, $expectedSearchText),
            ];
        }

        $upgradedSpec = CBModel::upgrade($spec);
        $expectedUpgradedSpec = (object)[
            'className' => 'CBHideByUserGroupView',
            'subviews' => CBViewTests::testSubviewUpgradedSpecs(),
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
