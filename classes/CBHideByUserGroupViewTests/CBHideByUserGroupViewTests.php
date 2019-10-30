<?php

final class CBHideByUserGroupViewTests {

    /* -- CBTest interfaces -- -- -- -- -- */

    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'general',
                'title' => 'CBHideByUserGroupView',
                'type' => 'server',
            ],
        ];
    }



    /* -- tests -- -- -- -- -- */



    /**
     * @return object
     */
    static function CBTest_general(): stdClass {
        $spec = (object)[
            'className' => 'CBHideByUserGroupView',
            'subviews' => CBViewTests::testSubviewSpecs(),
        ];


        /* build */

        $expectedModel = (object)[
            'className' => 'CBHideByUserGroupView',
            'hideFromMembers' => false,
            'hideFromNonmembers' => false,
            'groupName' => "",
            'subviews' => CBViewTests::testSubviewModels(),
        ];

        $actualModel = CBModel::build($spec);

        if ($actualModel != $expectedModel) {
            return CBTest::resultMismatchFailure(
                'build',
                $actualModel,
                $expectedModel
            );
        }


        /* toSearchText */

        $actualSearchText = CBModel::toSearchText($actualModel);

        $expectedSearchText = (
            CBViewTests::testSubviewSearchText() .
            ' CBHideByUserGroupView'
        );

        if ($actualSearchText !== $expectedSearchText) {
            return CBTest::resultMismatchFailure(
                'toSearchText',
                $actualSearchText,
                $expectedSearchText
            );
        }


        /* upgrade */

        $actualUpgradedSpec = CBModel::upgrade($spec);

        $expectedUpgradedSpec = (object)[
            'className' => 'CBHideByUserGroupView',
            'subviews' => CBViewTests::testSubviewUpgradedSpecs(),
        ];

        if ($actualUpgradedSpec != $expectedUpgradedSpec) {
            return CBTest::resultMismatchFailure(
                'upgrade',
                $actualUpgradedSpec,
                $expectedUpgradedSpec
            );
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTests_general() */

}
