<?php

final class CBMenuTests {

    /* -- CBTest interfaces -- -- -- -- -- */



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



    /* -- tests -- -- -- -- -- */



    /**
     * @return object
     */
    static function
    general(
    ): stdClass {
        $spec = (object)[
            'className' => 'CBMenu',
            'items' => [
                (object)[
                    'className' => 'CBTestView',
                    'value' => 42,
                ],
            ],
        ];


        /* build */

        $actualModel = CBModel::build($spec);

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

        if ($actualModel != $expectedModel) {
            return CBTest::resultMismatchFailure(
                'build',
                $actualModel,
                $expectedModel
            );
        }


        /* toSearchText */

        $actualSearchText = CBModel::toSearchText(
            $actualModel
        );

        $expectedSearchText = '';

        if (
            $actualSearchText !== $expectedSearchText
        ) {
            return CBTest::resultMismatchFailure(
                'toSearchText',
                $actualSearchText,
                $expectedSearchText
            );
        }


        /* upgrade */

        $actualUpgradedSpec = CBModel::upgrade($spec);

        $expectedUpgradedSpec = (object)[
            'className' => 'CBMenu',
            'items' => [
                (object)[
                    'className' => 'CBTestView',
                    'value' => 42,
                ],
            ],
            'CBModel_versionDate_property' => '2022_01_15',
        ];

        if ($actualUpgradedSpec != $expectedUpgradedSpec) {
            return CBTest::resultMismatchFailure(
                'upgrade',
                $actualUpgradedSpec,
                $expectedUpgradedSpec
            );
        }


        /* done */

        return (object)[
            'succeeded' => true,
        ];
    }
    /* CBTest_general() */

}
