<?php

final class CBViewTests {

    /* -- CBTest interfaces -- -- -- -- -- */



    /**
     * @return [object]
     */
    static function CBTest_getTests(): array {
        return [
            (object)[
                'name' => 'filterSubviews',
                'title' => 'CBView::filterSubviews()',
                'type' => 'server',
            ],
            (object)[
                'name' => 'getAndSetSubviews',
                'title' => 'CBView::getAndSetSubviews()',
                'type' => 'server',
            ],
        ];
    }



    /* -- tests -- -- -- -- -- */



    /**
     * @return object
     */
    static function CBTest_filterSubviews(): stdClass {
        $originalViewModel = (object)[
            'subviews' => [
                (object)[
                    'action' => 'keep_0_0',
                    'subviews' => [
                        (object)[
                            'action' => 'keep_1_0',
                        ],
                        (object)[
                            'action' => 'remove',
                        ],
                        (object)[
                            'action' => 'keep_1_2',

                        ],
                    ]
                ],
                (object)[
                    'action' => 'remove',
                ],
                (object)[
                    'action' => 'keep_0_2',
                ],
            ],
        ];

        $expectedViewModel = (object)[
            'subviews' => [
                (object)[
                    'action' => 'keep_0_0',
                    'subviews' => [
                        (object)[
                            'action' => 'keep_1_0',
                        ],
                        (object)[
                            'action' => 'keep_1_2',

                        ],
                    ]
                ],
                (object)[
                    'action' => 'keep_0_2',
                ],
            ],
        ];

        $actualViewModel = CBModel::clone($originalViewModel);

        CBView::filterSubviews(
            $actualViewModel,
            function ($viewModel) {
                return CBModel::valueToString($viewModel, 'action') != 'remove';
            }
        );

        if ($actualViewModel != $expectedViewModel) {
            return CBTest::resultMismatchFailure(
                'Test 1',
                $actualViewModel,
                $expectedViewModel
            );
        }

        return (object)[
            'succeeded' => 'true',
        ];
    }
    /* CBTest_filterSubviews() */



    /**
     * @return object
     */
    static function CBTest_getAndSetSubviews(): stdClass {
        $classNames = CBAdmin::fetchClassNames();

        foreach ($classNames as $className) {
            $model = (object)[
                'className' => $className,
            ];

            CBView::setSubviews(
                $model,
                CBViewTests::testSubviewSpecs()
            );

            $actualSubviews = CBView::getSubviews($model);
            $expectedSubviews = CBViewTests::testSubviewSpecs();

            if ($actualSubviews != $expectedSubviews) {
                return CBTest::resultMismatchFailure(
                    'test 1',
                    $actualSubviews,
                    $expectedSubviews
                );
            }
        }

        return (object)[
            'succeeded' => 'true',
        ];
    }
    /* CBTest_getAndSetSubviews() */



    /**
     * This test runs a CBView::render() test for all known classes.
     *
     * @NOTE 2018.02.13
     *
     *      This test exposes the awkwardness in trying to render a view for
     *      purposes other than rendering it to the current page. The rendering
     *      of views below may be affecting the CBHTMLOutput state.
     *
     *      We can't use CBHTMLOutput::begin() and reset() because begin() will
     *      set an exception handler. It's not vitally important, but I created
     *      this note for future reference.
     */
    static function renderTest() {
        $classNames = CBAdmin::fetchClassNames();

        foreach ($classNames as $className) {
            $model = (object)[
                'className' => $className,
            ];

            try {
                ob_start();

                CBView::render($model);

                ob_end_clean();
            } catch (Throwable $throwable) {
                ob_end_clean();

                throw $throwable;
            }
        }
    }



    /**
     * @return [model]
     */
    static function testSubviewModels(): array {
        return [
            (object)[
                'className' => 'CBTestView',
                'value' => 42,
            ]
        ];
    }



    /**
     * @return string
     */
    static function testSubviewSearchText(): string {
        return '42 CBTestView';
    }



    /**
     * @return [mixed]
     */
    static function testSubviewSpecs(): array {
        return [
            (object)[
                'className' => 'CBTestView',
                'value' => 42,
            ],
        ];
    }



    /**
     * @return [model]
     */
    static function testSubviewUpgradedSpecs(): array {
        return [
            (object)[
                'className' => 'CBTestView',
                'value' => 42,
            ],
        ];
    }



    /**
     * @return void
     */
    static function toSubviewsTest(): void {
        $classNames = CBAdmin::fetchClassNames();

        foreach ($classNames as $className) {
            $model = (object)[
                'className' => $className,
            ];

            $result = CBView::toSubviews($model);
        }
    }

}
