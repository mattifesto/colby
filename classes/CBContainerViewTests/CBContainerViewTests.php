<?php

final class
CBContainerViewTests {

    /* -- CBTest interfaces -- -- -- -- -- */

    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'type' => 'server',
                'name' => 'general',
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
