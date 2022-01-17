<?php

final class
CBBackgroundViewTests {

    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'general',
                'type' => 'server',
            ],
        ];
    }
    /* CBTest_getTests() */


    /* -- tests -- -- -- -- -- */

    /**
     * @return object
     */
    static function
    general(
    ): stdClass {
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
            return CBTest::resultMismatchFailure(
                'build',
                $model,
                $expectedModel
            );
        }

        $searchText = CBModel::toSearchText(
            $model
        );

        $expectedSearchText = CBViewTests::testSubviewSearchText();

        if (
            $searchText !== $expectedSearchText
        ) {
            return CBTest::resultMismatchFailure(
                'toSearchText',
                $searchText,
                $expectedSearchText
            );
        }

        $upgradedSpec = CBModel::upgrade($spec);
        $expectedUpgradedSpec = (object)[
            'className' => 'CBBackgroundView',
            'children' => CBViewTests::testSubviewUpgradedSpecs(),
            'CBModel_versionDate_property' => '2022_01_15',
        ];

        if ($upgradedSpec != $expectedUpgradedSpec) {
            return CBTest::resultMismatchFailure(
                'upgrade',
                $upgradedSpec,
                $expectedUpgradedSpec
            );
        }

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_general() */
}
