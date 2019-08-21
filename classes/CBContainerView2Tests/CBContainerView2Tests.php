<?php

final class CBContainerView2Tests {

    /* -- CBTest interfaces -- -- -- -- -- */

    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'general',
                'title' => 'CBContainerView2',
                'type' => 'server',
            ],
        ];
    }
    /* CBTest_getTests() */


    /* -- tests -- -- -- -- -- */

    /**
     * @return object
     */
    static function CBTest_general(): stdClass {
        $spec = (object)[
            'className' => 'CBContainerView2',
            'image' => (object)[
                /* testing deprecated missing class name */
                'ID' => 'bf0c7e133bf1a4bd05a3490a6c05d8fa34f5833f',
                'base' => 'original',
                'extension' => 'jpeg',
                'height' => 700,
                'width' => 900,
            ],
            'subviews' => CBViewTests::testSubviewSpecs(),
        ];

        $expectedModel = (object)[
            'className' => 'CBContainerView2',
            'CSSClassNames' => [],
            'subviews' => CBViewTests::testSubviewModels(),
        ];

        $model = CBModel::build($spec);

        if ($model != $expectedModel) {
            return CBTest::resultMismatchFailure(
                'build',
                $model,
                $expectedModel
            );
        }

        $searchText = CBModel::toSearchText($model);
        $expectedSearchText = CBViewTests::testSubviewSearchText() . ' CBContainerView2';

        if ($searchText !== $expectedSearchText) {
            return CBTest::resultMismatchFailure(
                'toSearchText',
                $searchText,
                $expectedSearchText
            );
        }

        $upgradedSpec = CBModel::upgrade($spec);
        $expectedUpgradedSpec = (object)[
            'className' => 'CBContainerView2',
            'image' => (object)[
                'className' => 'CBImage',
                'ID' => 'bf0c7e133bf1a4bd05a3490a6c05d8fa34f5833f',
                'filename' => 'original',
                'extension' => 'jpeg',
                'height' => 700,
                'width' => 900,
            ],
            'subviews' => CBViewTests::testSubviewUpgradedSpecs(),
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
