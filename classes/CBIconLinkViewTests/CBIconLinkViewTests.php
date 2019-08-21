<?php

final class CBIconLinkViewTests {

    /* -- CBTest interfaces -- -- -- -- -- */

    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'general',
                'title' => 'CBIconLinkView',
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
            'className' => 'CBIconLinkView',
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
            'className' => 'CBIconLinkView',
            'alternativeText' => '',
            'disableRoundedCorners' => false,
            'text' => '',
            'textAsHTML' => '',
            'textColor' => '',
            'URL' => '',
            'URLAsHTML' => '',
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
        $expectedSearchText = 'CBIconLinkView';

        if ($searchText !== $expectedSearchText) {
            return CBTest::resultMismatchFailure(
                'toSearchText',
                $searchText,
                $expectedSearchText
            );
        }

        $upgradedSpec = CBModel::upgrade($spec);
        $expectedUpgradedSpec = (object)[
            'className' => 'CBIconLinkView',
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
